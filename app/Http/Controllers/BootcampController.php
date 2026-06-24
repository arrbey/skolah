<?php

namespace App\Http\Controllers;

use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;

class BootcampController extends Controller
{
    /**
     * Daftar semua bootcamp — filter & sort.
     *
     * Route: GET /bootcamps
     */
    public function index(Request $request)
    {
        SEOMeta::setTitle('Bootcamp & Webinar | ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        SEOMeta::setDescription('Daftar bootcamp dan webinar online/offline terbaik. Belajar langsung dari mentor berpengalaman di bidangnya.');
        SEOMeta::setKeywords(['bootcamp', 'webinar', 'belajar online', 'workshop', 'pelatihan', 'mentor', 'skolah']);
        SEOMeta::setCanonical(route('bootcamps.index'));
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle('Bootcamp & Webinar | ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        OpenGraph::setDescription('Belajar langsung dari mentor berpengalaman. Bootcamp online & offline tersedia.');
        OpenGraph::addImage(asset('images/og-bootcamps.jpg'), ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl(route('bootcamps.index'));
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle('Bootcamp & Webinar | ' . \App\Models\Setting::get('site_name', 'Skolah.com'));
        TwitterCard::setDescription('Belajar langsung dari mentor berpengalaman.');

        // Stats untuk hero
        $stats = [
            'total'    => Bootcamp::count(),
            'upcoming' => Bootcamp::upcoming()->count(),
            'online'   => Bootcamp::online()->count(),
        ];

        // Featured upcoming bootcamps (hero highlight — 3 terdekat)
        $featuredBootcamps = Bootcamp::upcoming()
            ->with('instructor:id,name,avatar')
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        return view('pages.bootcamps.index', compact('stats', 'featuredBootcamps'));
    }

    /**
     * Detail bootcamp.
     *
     * Route: GET /bootcamps/{slug}
     */
    public function show(Request $request, string $slug)
    {
        $bootcamp = Bootcamp::where('slug', $slug)
            ->with([
                'instructor:id,name,avatar,bio',
                'paidRegistrations:id,bootcamp_id,user_id',
            ])
            ->firstOrFail();

        // ── SEO ────────────────────────────────────────────────────────────
        $seoTitle       = ($bootcamp->meta_title ?? $bootcamp->title) . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDescription = $bootcamp->meta_description ?? substr(strip_tags($bootcamp->description ?? ''), 0, 160);
        $seoImage       = $bootcamp->thumbnail_url ?? asset('images/og-default.jpg');
        $seoUrl         = route('bootcamps.show', $bootcamp->slug);

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDescription);
        SEOMeta::setKeywords([$bootcamp->title, 'bootcamp', 'webinar', $bootcamp->type === 'online' ? 'online' : 'offline', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com'))]);
        SEOMeta::setCanonical($seoUrl);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($bootcamp->title);
        OpenGraph::setDescription($seoDescription);
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'event');
        OpenGraph::setUrl($seoUrl);
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($seoDescription);
        TwitterCard::setImage($seoImage);

        // ── Cek registrasi user ────────────────────────────────────────────
        $isRegistered    = false;
        $userTicket      = null;
        $pendingRegistration = null;

        if (auth()->check()) {
            $registration = BootcampRegistration::where('user_id', auth()->id())
                ->where('bootcamp_id', $bootcamp->id)
                ->first();

            if ($registration) {
                $isRegistered        = $registration->is_paid;
                $userTicket          = $registration->is_paid ? $registration : null;
                $pendingRegistration = $registration->payment_status === 'pending' ? $registration : null;
            }
        }

        // ── Countdown data ─────────────────────────────────────────────────
        // ISO timestamp untuk Alpine countdown timer
        $countdownTarget = $bootcamp->start_date
            ? $bootcamp->start_date->toIso8601String()
            : null;

        // ── Slot info ──────────────────────────────────────────────────────
        $slotsLeft   = $bootcamp->remaining_seats;
        $slotPercent = ($bootcamp->max_participants > 0)
            ? min(100, (int) round(($bootcamp->total_registered / $bootcamp->max_participants) * 100))
            : 0;

        // ── Related bootcamps ──────────────────────────────────────────────
        $relatedBootcamps = Bootcamp::where('id', '!=', $bootcamp->id)
            ->where(function ($q) use ($bootcamp) {
                $q->where('type', $bootcamp->type)
                  ->orWhere('status', 'upcoming');
            })
            ->with('instructor:id,name,avatar')
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        return view('pages.bootcamps.show', compact(
            'bootcamp',
            'isRegistered',
            'userTicket',
            'pendingRegistration',
            'countdownTarget',
            'slotsLeft',
            'slotPercent',
            'relatedBootcamps',
        ));
    }
}
