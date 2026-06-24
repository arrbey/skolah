<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingProgramController extends Controller
{
    public function index()
    {
        $programs = \App\Models\LandingProgram::ordered()->get();
        return view('admin.landing-programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.landing-programs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'required|array',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'alignment' => 'required|in:left,right',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        \App\Models\LandingProgram::create($data);

        return redirect()->route('admin.landing-programs.index')->with('success', 'Program berhasil dibuat');
    }

    public function edit(\App\Models\LandingProgram $landingProgram)
    {
        return view('admin.landing-programs.edit', compact('landingProgram'));
    }

    public function update(Request $request, \App\Models\LandingProgram $landingProgram)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'required|array',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'alignment' => 'required|in:left,right',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($landingProgram->image) {
                app(\App\Services\MinioStorageService::class)->delete($landingProgram->image);
            }
            $data['image'] = app(\App\Services\MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        $landingProgram->update($data);

        return redirect()->route('admin.landing-programs.index')->with('success', 'Program berhasil diperbarui');
    }

    public function destroy(\App\Models\LandingProgram $landingProgram)
    {
        if ($landingProgram->image) {
            app(\App\Services\MinioStorageService::class)->delete($landingProgram->image);
        }
        $landingProgram->delete();
        return back()->with('success', 'Program berhasil dihapus');
    }
}
