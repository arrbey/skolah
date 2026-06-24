<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorActivity;
use App\Models\User;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::instructors();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $instructors = $query->withCount(['courses', 'enrollments'])
            ->latest()
            ->paginate(15);

        return view('admin.instructors.index', compact('instructors'));
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun instruktur {$user->name} berhasil {$status}.");
    }

    public function togglePublic(User $user)
    {
        $user->update(['is_public' => !$user->is_public]);
        
        $status = $user->is_public ? 'ditampilkan' : 'disembunyikan';
        return back()->with('success', "Instruktur {$user->name} berhasil {$status} dari daftar publik.");
    }

    public function activities(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')
            ->whereHas('user', function($q) {
                $q->where('role', 'instructor');
            });

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('route_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $activities = $query->latest('created_at')->paginate(30);
        $instructors = User::instructors()->orderBy('name')->get();

        return view('admin.instructors.activities', compact('activities', 'instructors'));
    }
}
