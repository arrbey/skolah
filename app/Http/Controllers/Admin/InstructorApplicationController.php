<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveApplicationRequest;
use App\Http\Requests\Admin\RejectApplicationRequest;
use App\Mail\InstructorApplicationStatusMail;
use App\Models\InstructorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InstructorApplicationController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /admin/instructor-applications — Daftar pengajuan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = InstructorApplication::with('user')->latest();

        $query = match ($filter) {
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            'all'      => $query,
            default    => $query->pending(),
        };

        $applications = $query->paginate(20);

        $stats = [
            'pending'  => InstructorApplication::pending()->count(),
            'approved' => InstructorApplication::approved()->count(),
            'rejected' => InstructorApplication::rejected()->count(),
        ];

        return view('admin.instructor-applications.index', compact('applications', 'filter', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /admin/instructor-applications/{id} — Detail pengajuan
    // ─────────────────────────────────────────────────────────────────────────

    public function show(InstructorApplication $application)
    {
        $application->load('user', 'reviewer');

        return view('admin.instructor-applications.show', compact('application'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /admin/instructor-applications/{id}/approve — Setujui
    // ─────────────────────────────────────────────────────────────────────────

    public function approve(ApproveApplicationRequest $request, InstructorApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        // Update application
        $application->update([
            'status'      => 'approved',
            'admin_notes' => $request->input('admin_notes'),
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        // Update user role
        $user = $application->user;
        $user->update(['role' => 'instructor']);
        $user->syncRoles(['instructor']);

        // Kirim email notifikasi ke user
        Mail::to($user->email)->send(new InstructorApplicationStatusMail($application));

        // Notifikasi in-app disetujui
        try {
            send_notification(
                user: $user,
                type: 'success',
                title: '🎉 Pengajuan Instruktur Disetujui!',
                message: 'Selamat! Kamu resmi menjadi Instruktur di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '. Mulai buat kursus pertamamu sekarang!',
                url: route('instructor.courses.index'),
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Instructor approved in-app notification failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.instructor-applications.index')
            ->with('success', "Pengajuan {$user->name} disetujui! User sekarang menjadi Instruktur.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /admin/instructor-applications/{id}/reject — Tolak
    // ─────────────────────────────────────────────────────────────────────────

    public function reject(RejectApplicationRequest $request, InstructorApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $application->update([
            'status'      => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        // Kirim email notifikasi ke user
        Mail::to($application->user->email)->send(new InstructorApplicationStatusMail($application));

        // Notifikasi in-app ditolak
        try {
            send_notification(
                user: $application->user,
                type: 'warning',
                title: '😔 Pengajuan Instruktur Belum Diterima',
                message: 'Pengajuan instrukturmu belum bisa diterima saat ini. Silakan cek email untuk informasi detail dan coba lagi.',
                url: route('dashboard'),
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Instructor rejected in-app notification failed', [
                'user_id' => $application->user_id,
                'error'   => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.instructor-applications.index')
            ->with('success', "Pengajuan {$application->user->name} ditolak.");
    }
}
