<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\User;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BundleController extends Controller
{
    public function index(Request $request)
    {
        $query = Bundle::with(['instructor'])->withCount('courses');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $bundles = $query->latest()->paginate(20);

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $courses = Course::published()->orderBy('title')->get();
        $instructors = User::role('instructor')->orderBy('name')->get();

        return view('admin.bundles.create', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'discount_price' => 'nullable|integer|min:0|lt:price',
            'status'         => 'required|in:draft,published',
            'instructor_id'  => 'nullable|exists:users,id',
            'thumbnail'      => 'nullable|image|max:2048',
            'course_ids'     => 'required|array|min:1',
            'course_ids.*'   => 'exists:courses,id',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Bundle::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug']); // Reuse course thumbnail logic
        }

        $bundle = Bundle::create($data);
        $bundle->courses()->sync($request->course_ids);

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle kursus berhasil dibuat.');
    }

    public function edit(Bundle $bundle)
    {
        $courses = Course::published()->orderBy('title')->get();
        $instructors = User::role('instructor')->orderBy('name')->get();
        $selectedCourses = $bundle->courses()->pluck('courses.id')->toArray();

        return view('admin.bundles.edit', compact('bundle', 'courses', 'instructors', 'selectedCourses'));
    }

    public function update(Request $request, Bundle $bundle)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'discount_price' => 'nullable|integer|min:0|lt:price',
            'status'         => 'required|in:draft,published',
            'instructor_id'  => 'nullable|exists:users,id',
            'thumbnail'      => 'nullable|image|max:2048',
            'course_ids'     => 'required|array|min:1',
            'course_ids.*'   => 'exists:courses,id',
        ]);

        if ($data['title'] !== $bundle->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Bundle::where('slug', $slug)->where('id', '!=', $bundle->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('thumbnail')) {
            if ($bundle->thumbnail) {
                app(MinioStorageService::class)->delete($bundle->thumbnail);
            }
            $data['thumbnail'] = app(MinioStorageService::class)
                ->uploadCourseThumbnail($request->file('thumbnail'), $data['slug'] ?? $bundle->slug);
        }

        $bundle->update($data);
        $bundle->courses()->sync($request->course_ids);

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle kursus berhasil diperbarui.');
    }

    public function destroy(Bundle $bundle)
    {
        if ($bundle->thumbnail) {
            app(MinioStorageService::class)->delete($bundle->thumbnail);
        }
        $bundle->courses()->detach();
        $bundle->delete();

        return back()->with('success', 'Bundle kursus berhasil dihapus.');
    }
}
