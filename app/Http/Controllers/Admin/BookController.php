<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Models\Institution;
use App\Models\User;
use App\Services\MinioStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with('instructor')->latest();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhere('isbn', 'like', "%$search%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($type = $request->type) {
            $query->where('type', $type);
        }

        $books = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Book::count(),
            'published' => Book::published()->count(),
            'draft'     => Book::draft()->count(),
            'digital'   => Book::digital()->count(),
            'physical'  => Book::physical()->count(),
        ];

        return view('admin.books.index', compact('books', 'stats'));
    }

    public function create(): View
    {
        $instructors = User::role('instructor')->orderBy('name')->get();
        $institutions = Institution::active()->orderBy('name')->get();
        return view('admin.books.create', compact('instructors', 'institutions'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['title']);

        $minio = app(MinioStorageService::class);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $minio->uploadBookCover(
                $request->file('cover_image'),
                $data['slug']
            );
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $minio->uploadBookFile(
                $request->file('file_path'),
                $data['slug']
            );
        }

        Book::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        $instructors = User::role('instructor')->orderBy('name')->get();
        $institutions = Institution::active()->orderBy('name')->get();
        return view('admin.books.edit', compact('book', 'instructors', 'institutions'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image') || $request->hasFile('file_path')) {
            $minio = app(MinioStorageService::class);

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image) $minio->delete($book->cover_image);
                $data['cover_image'] = $minio->uploadBookCover(
                    $request->file('cover_image'),
                    $book->slug
                );
            }

            if ($request->hasFile('file_path')) {
                if ($book->file_path) $minio->delete($book->file_path);
                $data['file_path'] = $minio->uploadBookFile(
                    $request->file('file_path'),
                    $book->slug
                );
            }
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function toggleStatus(Book $book): RedirectResponse
    {
        $book->update([
            'status' => $book->status === 'published' ? 'draft' : 'published',
        ]);

        return back()->with('success', 'Status buku diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $minio = app(MinioStorageService::class);
        if ($book->cover_image) $minio->delete($book->cover_image);
        if ($book->file_path)   $minio->delete($book->file_path);

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
