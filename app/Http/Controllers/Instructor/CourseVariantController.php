<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseVariantController extends Controller
{
    /**
     * Tampilkan halaman kelola varian course.
     */
    public function index(Course $course)
    {
        $this->authorize('update', $course);

        $variants = $course->variants()->ordered()->get();

        return view('instructor.courses.variants', compact('course', 'variants'));
    }

    /**
     * Simpan varian baru.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $data = $request->validate([
            'delivery_type'    => ['required', 'in:online,offline,hybrid'],
            'label'            => ['nullable', 'string', 'max:255'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999', 'lt:price'],
            'schedule_start'   => ['nullable', 'date'],
            'schedule_end'     => ['nullable', 'date', 'after_or_equal:schedule_start'],
            'location'         => ['nullable', 'string', 'max:255'],
            'platform'         => ['nullable', 'string', 'max:100'],
            'meeting_link'     => ['nullable', 'url', 'max:500'],
            'max_participants' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active'        => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? $course->variants()->count();

        $course->variants()->create($data);

        return back()->with('success', 'Varian berhasil ditambahkan!');
    }

    /**
     * Update varian.
     */
    public function update(Request $request, Course $course, CourseVariant $variant): RedirectResponse
    {
        $this->authorize('update', $course);

        // Pastikan variant milik course ini
        if ($variant->course_id !== $course->id) {
            abort(404);
        }

        $data = $request->validate([
            'delivery_type'    => ['required', 'in:online,offline,hybrid'],
            'label'            => ['nullable', 'string', 'max:255'],
            'price'            => ['required', 'integer', 'min:0', 'max:99999999'],
            'discount_price'   => ['nullable', 'integer', 'min:0', 'max:99999999', 'lt:price'],
            'schedule_start'   => ['nullable', 'date'],
            'schedule_end'     => ['nullable', 'date', 'after_or_equal:schedule_start'],
            'location'         => ['nullable', 'string', 'max:255'],
            'platform'         => ['nullable', 'string', 'max:100'],
            'meeting_link'     => ['nullable', 'url', 'max:500'],
            'max_participants' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active'        => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $variant->update($data);

        return back()->with('success', 'Varian berhasil diperbarui!');
    }

    /**
     * Hapus varian (hanya jika belum ada enrollment).
     */
    public function destroy(Course $course, CourseVariant $variant): RedirectResponse
    {
        $this->authorize('update', $course);

        if ($variant->course_id !== $course->id) {
            abort(404);
        }

        // Cek apakah ada enrollment dengan variant ini
        if ($variant->enrollments()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus varian yang sudah memiliki siswa terdaftar. Non-aktifkan saja.');
        }

        $variant->delete();

        return back()->with('success', 'Varian berhasil dihapus!');
    }
}
