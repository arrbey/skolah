<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use App\Services\MinioStorageService;

class CampusController extends Controller
{
    protected $storage;

    public function __construct(MinioStorageService $storage)
    {
        $this->storage = $storage;
    }

    public function index()
    {
        $campuses = Campus::ordered()->get();
        return view('admin.campuses.index', compact('campuses'));
    }

    public function create()
    {
        return view('admin.campuses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'map_link' => 'nullable|string',
            'features' => 'required|array',
            'image' => 'nullable|image|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->storage->uploadBanner($request->file('image'));
        }

        Campus::create($data);

        return redirect()->route('admin.campuses.index')->with('success', 'Kampus berhasil ditambahkan');
    }

    public function edit(Campus $campus)
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'map_link' => 'nullable|string',
            'features' => 'required|array',
            'image' => 'nullable|image|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($campus->image) {
                $this->storage->delete($campus->image);
            }
            $data['image'] = $this->storage->uploadBanner($request->file('image'));
        }

        $campus->update($data);

        return redirect()->route('admin.campuses.index')->with('success', 'Kampus berhasil diperbarui');
    }

    public function destroy(Campus $campus)
    {
        if ($campus->image) {
            $this->storage->delete($campus->image);
        }
        $campus->delete();
        return back()->with('success', 'Kampus berhasil dihapus');
    }
}
