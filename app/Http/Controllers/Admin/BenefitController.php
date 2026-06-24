<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $benefits = \App\Models\Benefit::ordered()->get();
        return view('admin.benefits.index', compact('benefits'));
    }

    public function create()
    {
        return view('admin.benefits.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        if (!isset($data['order'])) {
            $data['order'] = (\App\Models\Benefit::max('order') ?? 0) + 1;
        }

        \App\Models\Benefit::create($data);

        return redirect()->route('admin.benefits.index')->with('success', 'Benefit berhasil dibuat');
    }

    public function edit(\App\Models\Benefit $benefit)
    {
        return view('admin.benefits.edit', compact('benefit'));
    }

    public function update(Request $request, \App\Models\Benefit $benefit)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($benefit->image) {
                app(\App\Services\MinioStorageService::class)->delete($benefit->image);
            }
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        $benefit->update($data);

        return redirect()->route('admin.benefits.index')->with('success', 'Benefit berhasil diperbarui');
    }

    public function destroy(\App\Models\Benefit $benefit)
    {
        if ($benefit->image) {
            app(\App\Services\MinioStorageService::class)->delete($benefit->image);
        }
        $benefit->delete();
        return back()->with('success', 'Benefit berhasil dihapus');
    }

    public function toggleActive(\App\Models\Benefit $benefit)
    {
        $benefit->update(['is_active' => !$benefit->is_active]);
        return back()->with('success', 'Status benefit diubah');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $index => $id) {
            \App\Models\Benefit::where('id', $id)->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}
