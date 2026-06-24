<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Global search: Course, Bootcamp, Book.
     *
     * Catatan: query "%LIKE%" tidak bisa pakai index — untuk skala besar
     * pertimbangkan FULLTEXT index atau Laravel Scout (Algolia/Meilisearch).
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        $courses   = collect();
        $bootcamps = collect();
        $books     = collect();

        if ($query !== '') {
            $like = '%' . $query . '%';

            $courses = Course::where('status', 'published')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like);
                })
                ->with('instructor')
                ->limit(8)
                ->get();

            $bootcamps = Bootcamp::where('status', '!=', 'completed')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like);
                })
                ->with('instructor')
                ->limit(6)
                ->get();

            $books = Book::where('status', 'published')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like)
                      ->orWhere('author', 'like', $like);
                })
                ->limit(8)
                ->get();
        }

        return view('pages.search', compact('query', 'courses', 'bootcamps', 'books'));
    }
}
