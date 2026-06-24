<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Bundle;
use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Course;
use App\Models\MembershipPlan;
use App\Models\Testimonial;
use App\Models\User;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // ── SEO ───────────────────────────────────────────────────────────────
        $siteName    = \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));
        $title       = \App\Models\Setting::get('meta_title', 'Platform Edukasi Digital Terlengkap di Indonesia');
        $description = \App\Models\Setting::get('meta_description', 'Belajar dari instruktur terbaik Indonesia. Ribuan kursus online, bootcamp, dan buku digital untuk tingkatkan skill & karir kamu.');
        $keywords    = \App\Models\Setting::get('meta_keywords', 'kursus online, belajar online, bootcamp, edukasi digital');
        $keywordsArr = array_filter(array_map('trim', explode(',', $keywords)));
        
        $image       = asset('images/og-default.jpg');
        $url         = url('/');

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        if (!empty($keywordsArr)) {
            SEOMeta::setKeywords($keywordsArr);
        }
        SEOMeta::setCanonical($url);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::addImage($image, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName($siteName);

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setImage($image);

        // ── JSON-LD Organization & WebSite schema ────────────────────────────
        JsonLd::setTitle($title);
        JsonLd::setDescription($description);
        JsonLd::setType('WebSite');
        JsonLd::addImage($image);
        JsonLd::addValue('potentialAction', [
            '@type'       => 'SearchAction',
            'target'      => url('/search') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ]);

        // ── Heavy queries cached 10 minutes (mostly static data) ─────────────
        // CACHE DISABLED TEMPORARILY TO FIX INCOMPLETE OBJECT ERRORS
        $data = [
            'featuredCourses' => Course::with(['instructor:id,name,avatar', 'category:id,name,slug'])
                ->where('status', 'published')
                ->where('is_featured', true)
                ->orderByDesc('total_students')
                ->limit(8)
                ->get(),

            'featuredBundles' => Bundle::with(['instructor:id,name,avatar'])->withCount('courses')
                ->where('status', 'published')
                ->latest()
                ->limit(3)
                ->get(),

            'upcomingBootcamps' => Bootcamp::with('instructor:id,name,avatar')
                ->where('status', 'upcoming')
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->limit(3)
                ->get(),

            'featuredBooks' => Book::where('status', 'published')
                ->orderByDesc('created_at')
                ->limit(4)
                ->get(),

            'categories' => Category::whereNull('parent_id')
                ->withCount(['courses as own_courses_count' => fn ($q) => $q->where('status', 'published')])
                ->with(['children' => fn($q) => $q->withCount(['courses as courses_count' => fn($q2) => $q2->where('status', 'published')])])
                ->get()
                ->each(function ($cat) {
                    $cat->courses_count = $cat->own_courses_count + $cat->children->sum('courses_count');
                })
                ->sortByDesc('courses_count')
                ->take(6)
                ->values(),

            'membershipPlans' => MembershipPlan::where('is_active', true)
                ->orderBy('price_monthly')
                ->get(),

            'instructors' => User::role('instructor')
                ->where('is_active', true)
                ->where('is_public', true)
                ->withCount(['courses' => fn ($q) => $q->where('status', 'published')])
                ->having('courses_count', '>=', 1)
                ->orderByDesc('courses_count')
                ->limit(4)
                ->get(),

            'testimonials' => Testimonial::with('user:id,name,avatar')
                ->featured()
                ->highRated(4)
                ->latest()
                ->limit(6)
                ->get(),

            'heroBanners' => Banner::where('position', 'hero')
                ->where('is_active', true)
                ->orderBy('order')
                ->limit(3)
                ->get(),

            'benefits'        => \App\Models\Benefit::active()->ordered()->get(),
            'landingPrograms' => \App\Models\LandingProgram::active()->ordered()->get(),
            'galleries'       => \App\Models\Gallery::active()->ordered()->get(),
            'campuses'        => Campus::active()->ordered()->get(),

            'stats' => [
                'students'    => User::role('user')->count(),
                'courses'     => Course::where('status', 'published')->count(),
                'instructors' => User::role('instructor')->where('is_active', true)->where('is_public', true)->count(),
                'bootcamps'   => Bootcamp::where('status', '!=', 'completed')->count(),
            ],

            'promoBanners' => Banner::where('position', 'promo')
                ->where('is_active', true)
                ->ordered()
                ->get(),

            'recentUsers' => User::role('user')->latest()->limit(5)->get(),
        ];

        // Final safety check: ensuring no incomplete objects sneak in
        foreach ($data as $key => $value) {
            if (is_object($value) && get_class($value) === '__PHP_Incomplete_Class') {
                // If somehow we still get an incomplete object, we force null to skip the view logic
                $data[$key] = collect(); 
            }
        }

        extract($data);

        return view('pages.home', compact(
            'featuredCourses',
            'featuredBundles',
            'upcomingBootcamps',
            'featuredBooks',
            'categories',
            'benefits',
            'landingPrograms',
            'galleries',
            'membershipPlans',
            'campuses',
            'instructors',
            'testimonials',
            'heroBanners',
            'stats',
            'promoBanners',
            'recentUsers'
        ));
    }
}
