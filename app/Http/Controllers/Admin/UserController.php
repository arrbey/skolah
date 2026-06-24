<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserInvitationMail;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($request->input('status') === 'suspended') {
            $query->whereNotNull('suspended_at');
        } elseif ($request->input('status') === 'active') {
            $query->whereNull('suspended_at');
        }

        // Filter by verification status
        if ($request->input('verification') === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->input('verification') === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $stats = [
            'total'       => User::count(),
            'users'       => User::where('role', 'user')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'admins'      => User::where('role', 'admin')->count(),
        ];

        $users = $query->withCount(['orders', 'enrollments'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role'  => ['required', 'string', 'in:user,instructor,admin'],
        ]);

        $result = $this->inviteUser($data);

        if (! $result['success']) {
            // JANGAN expose plaintext password ke session flash / UI / log.
            // Admin harus trigger reset password manual jika email gagal.
            \Illuminate\Support\Facades\Log::warning('User invite email failed', [
                'user_id' => $result['user']->id,
                'email'   => $result['user']->email,
            ]);

            return redirect()->route('admin.users.index')
                ->with('warning', "User {$result['user']->name} berhasil dibuat, namun email undangan gagal dikirim. Silakan trigger reset password manual untuk user ini.");
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$result['user']->name} berhasil diundang. Detail login telah dikirim ke email.");
    }

    /**
     * Bulk Import Users via CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        
        // Cek jumlah baris sebelum memproses (Limit 50 User)
        $lineCount = count(file($file->getRealPath())) - 1; // Kurangi 1 untuk header
        if ($lineCount > 50) {
            return back()->with('error', "Batas maksimal import adalah 50 user sekali jalan. File kamu berisi {$lineCount} user.");
        }

        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip Header
        fgetcsv($handle);

        $invited = 0;
        $skipped = 0;
        $errors  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Check if row is valid (Nama, Email, Role)
            if (count($row) < 3) {
                $errors++;
                continue;
            }

            $name  = trim($row[0]);
            $email = trim($row[1]);
            $role  = strtolower(trim($row[2]));

            // Basic validation
            if (empty($name) || empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors++;
                continue;
            }

            // Check duplicate
            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            // Valid role
            if (! in_array($role, ['user', 'instructor', 'admin'])) {
                $role = 'user'; // default
            }

            $this->inviteUser([
                'name'  => $name,
                'email' => $email,
                'role'  => $role
            ], true); // true = isImport

            $invited++;
        }

        fclose($handle);

        $message = "Berhasil mengundang {$invited} pengguna.";
        if ($skipped > 0) $message .= " ({$skipped} email sudah ada, dilewati).";
        if ($errors > 0) $message .= " ({$errors} baris tidak valid).";

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Download CSV Template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_undang_user.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Email', 'Role (user/instructor/admin)']);
            fputcsv($file, ['Siswa Contoh', 'siswa@contoh.com', 'user']);
            fputcsv($file, ['Pengajar Baru', 'pengajar@bisnis.com', 'instructor']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Logic for inviting a user
     */
    protected function inviteUser($data, $isImport = false)
    {
        // Selalu pakai random password agar aman, baik import maupun manual
        $password = Str::random(12);

        $user = User::create([
            'is_imported'          => $isImport,
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'role'                 => $data['role'],
            'password'             => Hash::make($password),
            'must_change_password' => true,
            'is_verified'          => $isImport ? false : true, // Jika import, biarkan mereka verifikasi manual via dashboard
            'email_verified_at'    => $isImport ? null : now(),   // Jika import, biarkan null
        ]);

        $user->assignRole($data['role']);

        try {
            // Email dikirim untuk memberi tahu email & password mereka
            Mail::to($user->email)->send(new UserInvitationMail($user, $password));
            return ['success' => true, 'user' => $user, 'password' => $password];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim email undangan: ' . $e->getMessage());
            return ['success' => false, 'user' => $user, 'password' => $password];
        }
    }

    public function suspend(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menonaktifkan admin.');
        }

        $user->update(['suspended_at' => now()]);

        return back()->with('success', "User {$user->name} berhasil disuspend.");
    }

    public function activate(User $user)
    {
        $user->update(['suspended_at' => null]);

        return back()->with('success', "User {$user->name} berhasil diaktifkan.");
    }

    public function verifyManual(User $user)
    {
        $user->update([
            'email_verified_at' => now(),
            'is_verified' => true,
        ]);

        return back()->with('success', "User {$user->name} berhasil diverifikasi manual!");
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus admin.');
        }

        // Jika ini instruktur, Anda mungkin ingin melakukan pengecekan tambahan 
        // seperti apakah dia memiliki kursus aktif. Namun SoftDelete sudah cukup aman.
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} berhasil dihapus.");
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = User::query();

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')->get();

        if ($format === 'excel') {
            return $this->exportExcel($users);
        }

        return $this->exportPdf($users);
    }

    private function exportPdf($users)
    {
        $pdf = Pdf::loadView('admin.users.export-pdf', compact('users'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('users-' . date('Y-m-d') . '.pdf');
    }

    private function exportExcel($users)
    {
        $filename = 'users-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['ID', 'Nama', 'Email', 'Role', 'Terdaftar', 'Status']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->created_at->format('d/m/Y'),
                    $user->suspended_at ? 'Suspended' : 'Aktif',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
