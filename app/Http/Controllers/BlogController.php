<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;


class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('status', 'published')->with('author');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $posts = $query->orderByDesc('created_at')->paginate(9);
        $categories = Post::where('status', 'published')->distinct()->pluck('category');

        return view('pages.blog.index', compact('posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();
        
        // Increment view count
        $post->increment('view_count');

        $relatedPosts = Post::where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return view('pages.blog.show', compact('post', 'relatedPosts'));
    }
}
