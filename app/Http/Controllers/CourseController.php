<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CourseController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        SEOMeta::setTitle('Kursus Online — ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        SEOMeta::setDescription('Temukan ribuan kursus online dari instruktur terbaik Indonesia. Filter berdasarkan kategori, level, harga, dan rating.');
        SEOMeta::setKeywords(['kursus online', 'belajar coding', 'kursus desain', 'kursus marketing', 'sertifikat online', 'e-learning']);
        SEOMeta::setCanonical(route('courses.index'));
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle('Kursus Online — ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        OpenGraph::setDescription('Temukan ribuan kursus online dari instruktur terbaik Indonesia.');
        OpenGraph::addImage(asset('images/og-courses.jpg'), ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl(route('courses.index'));
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle('Kursus Online — ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        TwitterCard::setDescription('Temukan ribuan kursus online dari instruktur terbaik Indonesia.');

        $query = Course::with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->published();

        // ── Filter: search
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        // ── Filter: category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // ── Filter: level
        if ($request->filled('level') && in_array($request->level, ['beginner', 'intermediate', 'advanced'])) {
            $query->byLevel($request->level);
        }

        // ── Filter: price
        if ($request->price === 'free') {
            $query->free();
        } elseif ($request->price === 'paid') {
            $query->paid();
        }

        // ── Filter: min rating
        if ($request->filled('rating') && is_numeric($request->rating)) {
            $query->where('rating', '>=', (float) $request->rating);
        }

        // ── Filter: instructor
        if ($request->filled('instructor')) {
            $query->byInstructor((int) $request->instructor);
        }

        // ── Sort
        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'newest'    => $query->latest(),
            'price_asc' => $query->orderBy('price'),
            'price_desc'=> $query->orderByDesc('price'),
            'rating'    => $query->topRated(),
            default     => $query->popular(),   // popular = most students
        };

        $courses    = $query->paginate(12)->withQueryString();

        // Categories with recursive child course counts (cached 15 min)
        $categories = Cache::remember('courses.index.categories_v1', 900, function () {
            return Category::whereNull('parent_id')
                ->withCount(['courses as own_courses_count' => function ($q) {
                    $q->where('status', 'published');
                }])
                ->with(['children' => function ($q) {
                    $q->withCount(['courses as courses_count' => function ($q2) {
                        $q2->where('status', 'published');
                    }]);
                }])
                ->get()
                ->map(function ($cat) {
                    return [
                        'slug'          => $cat->slug,
                        'name'          => $cat->name,
                        'courses_count' => $cat->own_courses_count + $cat->children->sum('courses_count'),
                    ];
                })
                ->sortByDesc('courses_count')
                ->values()
                ->all();
        });

        $totalCount = Cache::remember('courses.index.total_count_v1', 900, fn() => Course::published()->count());
 
        // ── Active Flash Sale ────────────────────────────────────────────────
        $activeFlashSale = \App\Models\FlashSale::active()
            ->with(['items' => function($q) {
                $q->with('itemable');
            }])
            ->first();

        return view('pages.courses.index', compact(
            'courses',
            'categories',
            'sort',
            'totalCount',
            'activeFlashSale',
        ));
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(string $slug): View
    {
        $course = Course::with([
            'instructor',
            'category',
            'tags',
            'activeVariants',
            'sections' => fn ($q) => $q->ordered()->with([
                'lessons' => fn ($q) => $q->ordered(),
            ]),
            'reviews' => fn ($q) => $q->with('user:id,name,avatar')->latest()->limit(10),
        ])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

        // Related courses (same category, exclude current)
        $relatedCourses = Course::with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->published()
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->popular()
            ->limit(4)
            ->get();

        // Stats
        $totalLessons  = $course->sections->sum(fn ($s) => $s->lessons->count());
        $totalDuration = $course->sections->sum(fn ($s) => $s->lessons->sum('video_duration'));
        $freePreviewCount = $course->sections->sum(
            fn ($s) => $s->lessons->where('is_free_preview', true)->count()
        );

        // Is enrolled?
        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = $course->enrollments()
                ->where('user_id', auth()->id())
                ->exists();
        }

        // Rating breakdown (1–5 counts)
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $cnt = $course->reviews->where('rating', $i)->count();
            $ratingBreakdown[$i] = [
                'count' => $cnt,
                'pct'   => $course->rating_count > 0 ? round(($cnt / $course->rating_count) * 100) : 0,
            ];
        }

        // SEO
        $seoTitle       = ($course->meta_title ?: $course->title) . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDescription = $course->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($course->description), 160);
        $seoImage       = $course->thumbnail_url ?? asset('images/og-default.jpg');
        $seoUrl         = route('courses.show', $course->slug);
        $seoKeywords    = array_merge(
            [$course->title, $course->category->name ?? 'Kursus Online', 'belajar online', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com'))],
            $course->tags->pluck('name')->toArray()
        );

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDescription);
        SEOMeta::setKeywords($seoKeywords);
        SEOMeta::setCanonical($seoUrl);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($course->title);
        OpenGraph::setDescription(\Illuminate\Support\Str::limit(strip_tags($course->description), 200));
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl($seoUrl);
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($course->title . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        TwitterCard::setDescription($seoDescription);
        TwitterCard::setImage($seoImage);

        // ── JSON-LD Course schema (Google Rich Results) ─────────────────────
        JsonLd::setTitle($course->title);
        JsonLd::setDescription($seoDescription);
        JsonLd::setType('Course');
        JsonLd::addImage($seoImage);
        JsonLd::addValue('provider', [
            '@type' => 'Organization',
            'name'  => \App\Models\Setting::get('site_name', 'Skolah.com'),
            'url'   => url('/'),
        ]);
        if ($course->instructor) {
            JsonLd::addValue('instructor', [
                '@type' => 'Person',
                'name'  => $course->instructor->name,
            ]);
        }
        if ($course->rating_count > 0) {
            JsonLd::addValue('aggregateRating', [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) round($course->rating, 1),
                'reviewCount' => (string) $course->rating_count,
                'bestRating'  => '5',
                'worstRating' => '1',
            ]);
        }
        JsonLd::addValue('offers', [
            '@type'         => 'Offer',
            'price'         => (string) ($course->price ?? 0),
            'priceCurrency' => 'IDR',
            'url'           => $seoUrl,
            'availability'  => 'https://schema.org/InStock',
        ]);

        // Has reviewed?
        $hasReviewed = false;
        if (auth()->check()) {
            $hasReviewed = $course->reviews()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('pages.courses.show', compact(
            'course',
            'relatedCourses',
            'totalLessons',
            'totalDuration',
            'freePreviewCount',
            'isEnrolled',
            'ratingBreakdown',
            'hasReviewed',
        ));
    }

    // ── Store Review ──────────────────────────────────────────────────────────
    public function storeReview(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        // Check if enrolled
        $isEnrolled = $course->enrollments()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Anda harus terdaftar di kursus ini untuk memberikan ulasan.');
        }

        // Check if already reviewed
        $existingReview = $course->reviews()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk kursus ini.');
        }

        // Save review
        $course->reviews()->create([
            'user_id' => auth()->id(),
            'rating'  => $request->rating,
            'review'  => $request->review,
        ]);

        // Update course rating cache
        $reviews = $course->reviews;
        $course->update([
            'rating'       => $reviews->avg('rating'),
            'rating_count' => $reviews->count(),
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
