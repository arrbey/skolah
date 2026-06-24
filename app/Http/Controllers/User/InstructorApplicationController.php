<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\InstructorApplicationRequest;
use App\Models\InstructorApplication;
use Illuminate\Http\Request;

class InstructorApplicationController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /dashboard/become-instructor — Form atau status pengajuan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = $request->user();

        // Jika sudah instructor, redirect ke instructor dashboard
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard')
                ->with('info', 'Kamu sudah menjadi instruktur.');
        }

        // Cek apakah sudah pernah apply
        $application = InstructorApplication::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('dashboard.instructor-apply', compact('application'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /dashboard/become-instructor — Submit pengajuan
    // ─────────────────────────────────────────────────────────────────────────

    public function store(InstructorApplicationRequest $request)
    {
        $user = $request->user();

        // Guard: sudah instructor
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }

        // Guard: masih ada pengajuan pending
        $existingPending = InstructorApplication::where('user_id', $user->id)
            ->pending()
            ->exists();

        if ($existingPending) {
            return back()->with('warning', 'Kamu masih memiliki pengajuan yang sedang diproses.');
        }

        $validated = $request->validated();

        InstructorApplication::create([
            'user_id'       => $user->id,
            'motivation'    => $validated['motivation'],
            'expertise'     => $validated['expertise'],
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'status'        => 'pending',
        ]);

        return redirect()->route('dashboard.become-instructor')
            ->with('success', 'Pengajuan berhasil dikirim! Tim kami akan mereview dalam 1-3 hari kerja.');
    }
}
