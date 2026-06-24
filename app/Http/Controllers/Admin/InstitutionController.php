<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::withCount(['courses', 'bootcamps', 'books'])
            ->latest()
            ->paginate(15);

        return view('admin.institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('admin.institutions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:institutions,name',
            'logo'        => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = app(MinioStorageService::class)
                ->upload($request->file('logo'), 'institutions/' . $data['slug']);
        }

        Institution::create($data);

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function edit(Institution $institution)
    {
        return view('admin.institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:institutions,name,' . $institution->id,
            'logo'        => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            if ($institution->logo) {
                app(MinioStorageService::class)->delete($institution->logo);
            }
            $data['logo'] = app(MinioStorageService::class)
                ->upload($request->file('logo'), 'institutions/' . Str::slug($data['name']));
        }

        $institution->update($data);

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function destroy(Institution $institution)
    {
        if ($institution->courses()->exists() || $institution->bootcamps()->exists() || $institution->books()->exists()) {
            return back()->with('error', 'Lembaga tidak bisa dihapus karena masih memiliki kursus, bootcamp, atau buku.');
        }

        if ($institution->logo) {
            app(MinioStorageService::class)->delete($institution->logo);
        }

        $institution->delete();

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Lembaga berhasil dihapus.');
    }
}
