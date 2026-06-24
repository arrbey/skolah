<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('author');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->orderByDesc('created_at')->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['author_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil diterbitkan!');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        return back()->with('success', 'Artikel berhasil dihapus!');
    }

    public function toggleStatus(Post $post)
    {
        $post->status = $post->status === 'published' ? 'draft' : 'published';
        $post->save();

        return back()->with('success', 'Status artikel berhasil diubah!');
    }
}
