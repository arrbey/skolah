<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlastEmailRequest;
use App\Mail\CoursePromotionMail;
use App\Models\Category;
use App\Models\Course;
use App\Models\Institution;
use App\Models\User;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['instructor', 'category', 'institution']);

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $stats = [
            'total'     => Course::count(),
            'published' => Course::published()->count(),
            'draft'     => Course::draft()->count(),
            'featured'  => Course::where('is_featured', true)->count(),
        ];

        $courses = $query->withCount(['enrollments', 'reviews', 'sections'])
            ->latest()
            ->paginate(20);

        return view('admin.courses.index', compact('courses', 'stats'));
    }

    public function create()
    {
        $categories = Category::parents()->with('children')->orderBy('name')->get();
        $institutions = Institution::active()->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->orderBy('name')->get();

        return view('admin.courses.create', compact('categories', 'institutions', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'instructor_id'    => 'nullable|exists:users,id',
            'institution_id'   => 'nullable|exists:institutions,id',
            'category_id'      => 'required|exists:categories,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'thumbnail'        => 'nullable|image|max:20480',
            'trailer_url'      => 'nullable|url|max:255',
            'price'            => 'required|integer|min:0',
            'discount_price'   => 'nullable|integer|min:0|lt:price',
            'level'            => 'required|in:beginner,intermediate,advanced',
            'status'           => 'required|in:draft,published',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Course::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug']);
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.edit', $course->id)
            ->with('success', 'Kursus berhasil dibuat. Silakan tambahkan kurikulum.');
    }

    public function edit(Course $course)
    {
        $categories = Category::parents()->with('children')->orderBy('name')->get();
        $institutions = Institution::active()->orderBy('name')->get();
        $instructors = User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->orderBy('name')->get();

        $course->loadCount(['sections', 'enrollments', 'reviews']);

        return view('admin.courses.edit', compact('course', 'categories', 'institutions', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'instructor_id'    => 'nullable|exists:users,id',
            'institution_id'   => 'nullable|exists:institutions,id',
            'category_id'      => 'required|exists:categories,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'thumbnail'        => 'nullable|image|max:20480',
            'trailer_url'      => 'nullable|url|max:255',
            'price'            => 'required|integer|min:0',
            'discount_price'   => 'nullable|integer|min:0|lt:price',
            'level'            => 'required|in:beginner,intermediate,advanced',
            'status'           => 'required|in:draft,published',
        ]);

        if ($data['title'] !== $course->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                app(MinioStorageService::class)->delete($course->thumbnail);
            }
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug'] ?? $course->slug);
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    public function approve(Course $course)
    {
        $course->update(['status' => 'published']);

        return back()->with('success', "Kursus \"{$course->title}\" berhasil dipublish.");
    }

    public function reject(Course $course)
    {
        $course->update(['status' => 'draft']);

        return back()->with('success', "Kursus \"{$course->title}\" dikembalikan ke draft.");
    }

    public function toggleFeatured(Course $course)
    {
        $course->update(['is_featured' => ! $course->is_featured]);

        $label = $course->is_featured ? 'ditambahkan ke' : 'dihapus dari';

        return back()->with('success', "Kursus \"{$course->title}\" {$label} unggulan.");
    }

    public function destroy(Course $course)
    {
        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Tidak bisa hapus kursus yang sudah memiliki siswa terdaftar.');
        }

        if ($course->thumbnail) {
            app(MinioStorageService::class)->delete($course->thumbnail);
        }

        $course->sections()->each(fn ($section) => $section->lessons()->delete());
        $course->sections()->delete();
        $course->delete();

        return back()->with('success', "Kursus \"{$course->title}\" berhasil dihapus.");
    }

    // ── Blast Email Promosi Kursus ──

    public function showBlast(Course $course)
    {
        $course->load('instructor', 'category');

        $totalUsers = User::where('role', 'user')
            ->count();

        return view('admin.courses.blast', compact('course', 'totalUsers'));
    }

    public function blast(BlastEmailRequest $request, Course $course)
    {
        $course->load('instructor', 'category');
        $customMessage = $request->input('custom_message') ?? '';

        $users = User::where('role', 'user')
            ->select('id', 'name', 'email')
            ->get();

        $count = 0;
        $failed = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new CoursePromotionMail($user, $course, $customMessage)
                );
                $count++;
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Course blast email failed', [
                    'user_email' => $user->email,
                    'course'     => $course->title,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        Log::info('Course blast sent', [
            'course'         => $course->title,
            'total_sent'     => $count,
            'total_failed'   => $failed,
            'custom_message' => $customMessage,
            'sent_by'        => $request->user()->id,
        ]);

        $message = "Email promosi kursus \"{$course->title}\" berhasil dikirim ke {$count} user.";
        if ($failed > 0) {
            $message .= " ({$failed} gagal kirim)";
        }

        return redirect()->route('admin.courses.index')
            ->with('success', $message);
    }
}
