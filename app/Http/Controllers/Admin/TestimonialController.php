<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::with('user')->latest();

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('content', 'like', "%{$search}%");
        }

        if ($request->get('featured') !== null && $request->get('featured') !== '') {
            $query->where('is_featured', (bool) $request->get('featured'));
        }

        $testimonials = $query->paginate(18)->withQueryString();

        $stats = [
            'total'    => Testimonial::count(),
            'featured' => Testimonial::where('is_featured', true)->count(),
            'avg'      => round(Testimonial::avg('rating'), 1),
            'five_star'=> Testimonial::where('rating', 5)->count(),
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->update(['is_featured' => !$testimonial->is_featured]);

        $status = $testimonial->is_featured ? 'ditandai sebagai unggulan' : 'dihapus dari unggulan';
        return back()->with('success', "Testimoni berhasil {$status}.");
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
