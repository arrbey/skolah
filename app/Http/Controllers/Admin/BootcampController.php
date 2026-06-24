<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlastEmailRequest;
use App\Mail\BootcampPromotionMail;
use App\Models\Bootcamp;
use App\Models\Institution;
use App\Models\User;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BootcampController extends Controller
{
    public function index()
    {
        $bootcamps = Bootcamp::with(['instructor', 'institution'])
            ->withCount('registrations')
            ->latest()
            ->paginate(15);

        return view('admin.bootcamps.index', compact('bootcamps'));
    }

    public function create()
    {
        $institutions = Institution::active()->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->orderBy('name')->get();

        return view('admin.bootcamps.create', compact('institutions', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'instructor_id'    => 'required|exists:users,id',
            'institution_id'   => 'nullable|exists:institutions,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'thumbnail'        => 'nullable|image|max:20480',
            'price'            => 'required|integer|min:0',
            'discount_price'   => 'nullable|integer|min:0|lt:price',
            'type'             => 'required|in:online,offline',
            'platform'         => 'nullable|string|max:100',
            'meeting_link'     => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'max_participants' => 'required|integer|min:0',
            'status'           => 'required|in:upcoming,ongoing,completed',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Bootcamp::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadBootcampThumbnail($request->file('thumbnail'), $data['slug']);
        }

        $bootcamp = Bootcamp::create($data);

        return redirect()->route('admin.bootcamps.index')
            ->with('success', 'Bootcamp berhasil dibuat!');
    }

    public function edit(Bootcamp $bootcamp)
    {
        $institutions = Institution::active()->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->orderBy('name')->get();

        return view('admin.bootcamps.edit', compact('bootcamp', 'institutions', 'instructors'));
    }

    public function update(Request $request, Bootcamp $bootcamp)
    {
        $data = $request->validate([
            'instructor_id'    => 'required|exists:users,id',
            'institution_id'   => 'nullable|exists:institutions,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'thumbnail'        => 'nullable|image|max:20480',
            'price'            => 'required|integer|min:0',
            'discount_price'   => 'nullable|integer|min:0|lt:price',
            'type'             => 'required|in:online,offline',
            'platform'         => 'nullable|string|max:100',
            'meeting_link'     => 'nullable|string|max:255',
            'location'         => 'nullable|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'max_participants' => 'required|integer|min:0',
            'status'           => 'required|in:upcoming,ongoing,completed',
        ]);

        if ($data['title'] !== $bootcamp->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Bootcamp::where('slug', $slug)->where('id', '!=', $bootcamp->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('thumbnail')) {
            if ($bootcamp->thumbnail) {
                app(MinioStorageService::class)->delete($bootcamp->thumbnail);
            }
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadBootcampThumbnail($request->file('thumbnail'), $data['slug'] ?? $bootcamp->slug);
        }

        $bootcamp->update($data);

        return redirect()->route('admin.bootcamps.index')
            ->with('success', 'Bootcamp berhasil diperbarui.');
    }

    public function destroy(Bootcamp $bootcamp)
    {
        if ($bootcamp->registrations()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus bootcamp yang sudah memiliki peserta.');
        }

        if ($bootcamp->thumbnail) {
            app(MinioStorageService::class)->delete($bootcamp->thumbnail);
        }

        $bootcamp->delete();

        return redirect()->route('admin.bootcamps.index')
            ->with('success', 'Bootcamp berhasil dihapus.');
    }

    // ── Blast Email Promosi Bootcamp ──

    public function showBlast(Bootcamp $bootcamp)
    {
        $bootcamp->load('instructor');

        $totalUsers = User::where('role', 'user')
            ->count();

        return view('admin.bootcamps.blast', compact('bootcamp', 'totalUsers'));
    }

    public function blast(BlastEmailRequest $request, Bootcamp $bootcamp)
    {
        $bootcamp->load('instructor');
        $customMessage = $request->input('custom_message') ?? '';

        $users = User::where('role', 'user')
            ->select('id', 'name', 'email')
            ->get();

        $count = 0;
        $failed = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new BootcampPromotionMail($user, $bootcamp, $customMessage)
                );
                $count++;
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Bootcamp blast email failed', [
                    'user_email' => $user->email,
                    'bootcamp'   => $bootcamp->title,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        Log::info('Bootcamp blast sent', [
            'bootcamp'       => $bootcamp->title,
            'total_sent'     => $count,
            'total_failed'   => $failed,
            'custom_message' => $customMessage,
            'sent_by'        => $request->user()->id,
        ]);

        $message = "Email promosi bootcamp \"{$bootcamp->title}\" berhasil dikirim ke {$count} user.";
        if ($failed > 0) {
            $message .= " ({$failed} gagal kirim)";
        }

        return redirect()->route('admin.bootcamps.index')
            ->with('success', $message);
    }
}
