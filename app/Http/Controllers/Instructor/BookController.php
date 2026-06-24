<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreBookRequest;
use App\Http\Requests\Instructor\UpdateBookRequest;
use App\Models\Book;
use App\Models\Institution;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * List semua buku milik instructor.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        $query = Book::where('instructor_id', $instructorId)
            ->withCount('orders');

        if ($request->filled('status') && in_array($request->status, ['published', 'draft'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && in_array($request->type, ['digital', 'physical', 'both'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $books = $query->latest()->paginate(12)->withQueryString();

        $totalBooks     = Book::where('instructor_id', $instructorId)->count();
        $publishedBooks = Book::where('instructor_id', $instructorId)->published()->count();
        $draftBooks     = Book::where('instructor_id', $instructorId)->draft()->count();

        return view('instructor.books.index', compact(
            'books', 'totalBooks', 'publishedBooks', 'draftBooks'
        ));
    }

    /**
     * Form create buku baru.
     */
    public function create()
    {
        $institutions = Institution::active()->orderBy('name')->get();
        return view('instructor.books.create', compact('institutions'));
    }

    /**
     * Simpan buku baru.
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();
        $data['instructor_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);

        $originalSlug = $data['slug'];
        $counter = 1;
        while (Book::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        // Upload cover image ke MinIO
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = app(MinioStorageService::class)
                ->uploadBookCover($request->file('cover_image'), $data['slug']);
        }

        // Upload file PDF digital ke MinIO (PRIVATE)
        if ($request->hasFile('file_path')) {
            $data['file_path'] = app(MinioStorageService::class)
                ->uploadBookFile($request->file('file_path'), 0); // ID belum ada, akan diupdate setelah create
        }

        $book = Book::create($data);

        // Jika file PDF diupload, pindahkan ke path yang benar (dengan book_id)
        if ($request->hasFile('file_path') && isset($data['file_path'])) {
            // Re-upload dengan ID yang benar
            $newPath = app(MinioStorageService::class)
                ->uploadBookFile($request->file('file_path'), $book->id);
            // Hapus upload sementara
            app(MinioStorageService::class)->delete($data['file_path']);
            $book->update(['file_path' => $newPath]);
        }

        return redirect()
            ->route('instructor.books.edit', $book->id)
            ->with('success', 'Buku berhasil dibuat!');
    }

    /**
     * Form edit buku.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        $book->loadCount('orders');
        $institutions = Institution::active()->orderBy('name')->get();

        return view('instructor.books.edit', compact('book', 'institutions'));
    }

    /**
     * Update buku.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $data = $request->validated();

        if (isset($data['title']) && $data['title'] !== $book->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Book::where('slug', $slug)->where('id', '!=', $book->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        // Upload cover baru ke MinIO
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                app(MinioStorageService::class)->delete($book->cover_image);
            }
            $data['cover_image'] = app(MinioStorageService::class)
                ->uploadBookCover($request->file('cover_image'), $data['slug'] ?? $book->slug);
        }

        // Upload file PDF baru ke MinIO (PRIVATE)
        if ($request->hasFile('file_path')) {
            if ($book->file_path) {
                app(MinioStorageService::class)->delete($book->file_path);
            }
            $data['file_path'] = app(MinioStorageService::class)
                ->uploadBookFile($request->file('file_path'), $book->id);
        }

        $book->update($data);

        return redirect()
            ->route('instructor.books.edit', $book->id)
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Hapus buku (hanya jika belum ada order).
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->orders()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus buku yang sudah memiliki pesanan.');
        }

        if ($book->cover_image) {
            app(MinioStorageService::class)->delete($book->cover_image);
        }
        if ($book->file_path) {
            app(MinioStorageService::class)->delete($book->file_path);
        }

        $book->delete();

        return redirect()
            ->route('instructor.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    /**
     * Tampilkan daftar pesanan buku.
     */
    public function orders(Book $book)
    {
        $this->authorize('view', $book);

        $orders = $book->orders()
            ->with(['order.user'])
            ->latest()
            ->paginate(20);

        return view('instructor.books.orders', compact('book', 'orders'));
    }
}
