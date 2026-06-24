<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::withCount('courses')
            ->when($request->get('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        Tag::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with('success', "Tag \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with('success', "Tag berhasil diperbarui menjadi \"{$validated['name']}\".");
    }

    public function destroy(Tag $tag)
    {
        $tag->courses()->detach();
        $tag->delete();
        return back()->with('success', "Tag \"{$tag->name}\" berhasil dihapus.");
    }
}
