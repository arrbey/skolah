<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookOrder;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Halaman listing buku
    // GET /books
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        // Statistik untuk hero
        $stats = [
            'total'    => Book::published()->count(),
            'digital'  => Book::published()->whereIn('type', ['digital', 'both'])->count(),
            'physical' => Book::published()->whereIn('type', ['physical', 'both'])->count(),
        ];

        // Featured books — terbaru dengan discount
        $featuredBooks = Book::published()
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->with('instructor:id,name,avatar')
            ->latest()
            ->take(4)
            ->get();

        // SEOTools
        $seoTitle = 'Book Store — Koleksi Buku Terbaik | ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDesc  = 'Temukan koleksi buku fisik dan e-book terbaik untuk menunjang pembelajaran kamu. Tersedia ratusan judul dari penulis berkualitas.';
        $seoImage = asset('images/og-books.jpg');
        $seoUrl   = route('books.index');

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDesc);
        SEOMeta::setKeywords(['buku online', 'ebook', 'buku pelajaran', 'buku digital', 'toko buku', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com'))]);
        SEOMeta::setCanonical($seoUrl);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($seoTitle);
        OpenGraph::setDescription($seoDesc);
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl($seoUrl);
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($seoDesc);
        TwitterCard::setImage($seoImage);

        return view('pages.books.index', compact('stats', 'featuredBooks'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW — Detail buku
    // GET /books/{slug}
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $slug)
    {
        $book = Book::published()
            ->where('slug', $slug)
            ->with('instructor:id,name,avatar,bio')
            ->firstOrFail();

        // Cek apakah user sudah punya buku ini (sudah beli)
        $hasPurchased   = false;
        $userBookOrder  = null;

        if (auth()->check()) {
            $userBookOrder = BookOrder::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->whereHas('order', fn($q) => $q->where('status', 'paid'))
                ->latest()
                ->first();

            // Cek juga via order_items polymorphic
            if (! $userBookOrder) {
                $hasPurchased = \App\Models\OrderItem::whereHas('order', function ($q) {
                        $q->where('user_id', auth()->id())->where('status', 'paid');
                    })
                    ->where('itemable_type', Book::class)
                    ->where('itemable_id', $book->id)
                    ->exists();
            } else {
                $hasPurchased = true;
            }
        }

        // Related books (tipe sama, exclude current)
        $relatedBooks = Book::published()
            ->where('id', '!=', $book->id)
            ->where('type', $book->type)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Jika kurang dari 4, tambahkan dari tipe lain
        if ($relatedBooks->count() < 4) {
            $more = Book::published()
                ->where('id', '!=', $book->id)
                ->whereNotIn('id', $relatedBooks->pluck('id')->toArray())
                ->inRandomOrder()
                ->take(4 - $relatedBooks->count())
                ->get();
            $relatedBooks = $relatedBooks->merge($more);
        }

        // SEOTools
        $seoTitle = ($book->meta_title ?? $book->title) . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDesc  = $book->meta_description ?? Str::limit(strip_tags($book->description ?? ''), 160);
        $seoImage = $book->cover_image ? storageUrl($book->cover_image) : asset('images/og-books.jpg');
        $seoUrl   = route('books.show', $book->slug);

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDesc);
        SEOMeta::setKeywords([$book->title, $book->author ?? 'penulis', 'buku', $book->type === 'digital' ? 'ebook' : 'buku fisik', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com'))]);
        SEOMeta::setCanonical($seoUrl);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($book->title);
        OpenGraph::setDescription(Str::limit(strip_tags($book->description ?? ''), 200));
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'product');
        OpenGraph::setUrl($seoUrl);
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($seoDesc);
        TwitterCard::setImage($seoImage);

        return view('pages.books.show', compact(
            'book',
            'hasPurchased',
            'userBookOrder',
            'relatedBooks',
        ));
    }
}
