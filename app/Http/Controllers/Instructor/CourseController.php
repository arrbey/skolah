<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreCourseRequest;
use App\Http\Requests\Instructor\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * List semua course milik instructor.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        $query = Course::where('instructor_id', $instructorId)
            ->withCount(['sections', 'enrollments', 'reviews']);

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['published', 'draft'])) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $courses = $query->latest()->paginate(12)->withQueryString();

        // Stats
        $totalCourses     = Course::where('instructor_id', $instructorId)->count();
        $publishedCourses = Course::where('instructor_id', $instructorId)->published()->count();
        $draftCourses     = Course::where('instructor_id', $instructorId)->draft()->count();

        return view('instructor.courses.index', compact(
            'courses', 'totalCourses', 'publishedCourses', 'draftCourses'
        ));
    }

    /**
     * Form create course baru.
     */
    public function create()
    {
        $categories = Category::parents()->with('children')->orderBy('name')->get();
        $institutions = \App\Models\Institution::active()->orderBy('name')->get();

        return view('instructor.courses.create', compact('categories', 'institutions'));
    }

    /**
     * Simpan course baru.
     */
    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        $data['instructor_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);

        // Pastikan slug unik
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Course::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        // Upload thumbnail ke MinIO
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug']);
        }

        $course = Course::create($data);

        return redirect()
            ->route('instructor.courses.edit', $course->id)
            ->with('success', 'Kursus berhasil dibuat! Sekarang lengkapi konten dan lesson.');
    }

    /**
     * Form edit course.
     */
    public function edit(Course $course)
    {
        // Policy: hanya instructor pemilik atau admin
        $this->authorize('update', $course);

        $categories = Category::parents()->with('children')->orderBy('name')->get();
        $institutions = \App\Models\Institution::active()->orderBy('name')->get();
        $course->loadCount(['sections', 'enrollments', 'reviews']);

        return view('instructor.courses.edit', compact('course', 'categories', 'institutions'));
    }

    /**
     * Update course.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorize('update', $course);

        $data = $request->validated();

        // Update slug jika title berubah
        if (isset($data['title']) && $data['title'] !== $course->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        // Upload thumbnail baru ke MinIO
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama dari MinIO
            if ($course->thumbnail) {
                app(MinioStorageService::class)->delete($course->thumbnail);
            }
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug'] ?? $course->slug);
        }

        $course->update($data);

        return redirect()
            ->route('instructor.courses.edit', $course->id)
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    /**
     * Hapus course (soft check: hanya jika belum ada enrollment).
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        // Cegah hapus jika sudah ada enrollment
        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kursus yang sudah memiliki siswa.');
        }

        // Hapus thumbnail dari MinIO
        if ($course->thumbnail) {
            app(MinioStorageService::class)->delete($course->thumbnail);
        }

        // Hapus sections & lessons terkait
        $course->sections()->each(function ($section) {
            $section->lessons()->delete();
            $section->delete();
        });

        $course->delete();

        return redirect()
            ->route('instructor.courses.index')
            ->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * Tampilkan daftar siswa dan progres mereka untuk kursus tertentu.
     */
    public function students(Course $course)
    {
        $this->authorize('view', $course);

        $enrollments = $course->enrollments()
            ->with('user')
            ->latest('enrolled_at')
            ->paginate(20);

        return view('instructor.courses.students', compact('course', 'enrollments'));
    }
}
