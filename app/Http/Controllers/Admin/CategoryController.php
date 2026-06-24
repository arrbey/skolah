<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::parents()
            ->with(['children' => fn ($q) => $q->withCount('courses')])
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::parents()->orderBy('name')->get();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $parents = Category::parents()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->courses()->exists()) {
            return back()->with('error', 'Tidak bisa hapus kategori yang memiliki kursus.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Tidak bisa hapus kategori yang memiliki sub-kategori. Hapus sub-kategori terlebih dahulu.');
        }

        $category->delete();

        return back()->with('success', "Kategori \"{$category->name}\" berhasil dihapus.");
    }
}
