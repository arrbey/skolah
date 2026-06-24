<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreBootcampRequest;
use App\Http\Requests\Instructor\UpdateBootcampRequest;
use App\Models\Bootcamp;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BootcampController extends Controller
{
    /**
     * List semua bootcamp milik instructor.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        $query = Bootcamp::where('instructor_id', $instructorId)
            ->withCount('registrations');

        if ($request->filled('status') && in_array($request->status, ['upcoming', 'ongoing', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $bootcamps = $query->latest()->paginate(12)->withQueryString();

        $totalBootcamps   = Bootcamp::where('instructor_id', $instructorId)->count();
        $upcomingCount    = Bootcamp::where('instructor_id', $instructorId)->upcoming()->count();
        $completedCount   = Bootcamp::where('instructor_id', $instructorId)->completed()->count();

        return view('instructor.bootcamps.index', compact(
            'bootcamps', 'totalBootcamps', 'upcomingCount', 'completedCount'
        ));
    }

    /**
     * Form create bootcamp baru.
     */
    public function create()
    {
        $institutions = \App\Models\Institution::active()->orderBy('name')->get();
        return view('instructor.bootcamps.create', compact('institutions'));
    }

    /**
     * Simpan bootcamp baru.
     */
    public function store(StoreBootcampRequest $request)
    {
        $data = $request->validated();
        $data['instructor_id'] = auth()->id();
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

        return redirect()
            ->route('instructor.bootcamps.edit', $bootcamp->id)
            ->with('success', 'Bootcamp berhasil dibuat!');
    }

    /**
     * Form edit bootcamp.
     */
    public function edit(Bootcamp $bootcamp)
    {
        $this->authorize('update', $bootcamp);
        $bootcamp->loadCount('registrations');
        $institutions = \App\Models\Institution::active()->orderBy('name')->get();

        return view('instructor.bootcamps.edit', compact('bootcamp', 'institutions'));
    }

    /**
     * Update bootcamp.
     */
    public function update(UpdateBootcampRequest $request, Bootcamp $bootcamp)
    {
        $this->authorize('update', $bootcamp);

        $data = $request->validated();

        if (isset($data['title']) && $data['title'] !== $bootcamp->title) {
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

        return redirect()
            ->route('instructor.bootcamps.edit', $bootcamp->id)
            ->with('success', 'Bootcamp berhasil diperbarui.');
    }

    /**
     * Hapus bootcamp (hanya jika belum ada registrasi).
     */
    public function destroy(Bootcamp $bootcamp)
    {
        $this->authorize('delete', $bootcamp);

        if ($bootcamp->registrations()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus bootcamp yang sudah memiliki peserta.');
        }

        if ($bootcamp->thumbnail) {
            app(MinioStorageService::class)->delete($bootcamp->thumbnail);
        }

        $bootcamp->delete();

        return redirect()
            ->route('instructor.bootcamps.index')
            ->with('success', 'Bootcamp berhasil dihapus.');
    }

    /**
     * Tampilkan daftar peserta bootcamp.
     */
    public function registrations(Bootcamp $bootcamp)
    {
        $this->authorize('view', $bootcamp);

        $registrations = $bootcamp->registrations()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('instructor.bootcamps.registrations', compact('bootcamp', 'registrations'));
    }
}
