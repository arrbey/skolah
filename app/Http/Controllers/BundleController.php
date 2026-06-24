<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(Request $request): View
    {
        SEOMeta::setTitle('Bundle Kursus Hemat — ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        SEOMeta::setDescription('Beli paket kursus lebih hemat dengan Bundle Kursus. Dapatkan akses ke beberapa kursus sekaligus dengan harga spesial.');

        $query = Bundle::with(['instructor:id,name,avatar'])->withCount('courses')->where('status', 'published');

        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%");
        }

        $bundles = $query->latest()->paginate(12);

        return view('pages.bundles.index', compact('bundles'));
    }

    public function show(string $slug): View
    {
        $bundle = Bundle::with(['instructor', 'courses' => fn($q) => $q->published()->with('instructor')])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        // SEO
        $seoTitle = $bundle->title . ' — ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription(\Illuminate\Support\Str::limit(strip_tags($bundle->description), 160));

        OpenGraph::setTitle($bundle->title);
        OpenGraph::addImage($bundle->thumbnail_url);
        
        TwitterCard::setTitle($bundle->title);
        TwitterCard::setImage($bundle->thumbnail_url);

        // Check if user has all courses in bundle
        $isOwned = false;
        if (auth()->check()) {
            $courseIds = $bundle->courses->pluck('id')->toArray();
            $enrolledCount = auth()->user()->enrollments()
                ->whereIn('course_id', $courseIds)
                ->count();
            
            if ($enrolledCount === count($courseIds)) {
                $isOwned = true;
            }
        }

        return view('pages.bundles.show', compact('bundle', 'isOwned'));
    }
}
