<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = \App\Models\Gallery::ordered()->get();
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'nullable|string|max:1000',
            'image' => 'required|image|max:20480',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        \App\Models\Gallery::create($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil ditambahkan');
    }

    public function edit(\App\Models\Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, \App\Models\Gallery $gallery)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:20480',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($gallery->image) {
                app(\App\Services\MinioStorageService::class)->delete($gallery->image);
            }
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        $gallery->update($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil diperbarui');
    }

    public function destroy(\App\Models\Gallery $gallery)
    {
        if ($gallery->image) {
            app(\App\Services\MinioStorageService::class)->delete($gallery->image);
        }
        $gallery->delete();
        return back()->with('success', 'Galeri berhasil dihapus');
    }
}
