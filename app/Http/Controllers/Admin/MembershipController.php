<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMembershipPlanRequest;
use App\Http\Requests\Admin\UpdateMembershipPlanRequest;
use App\Models\Course;
use App\Models\MembershipPlan;
use App\Models\PromoCode;
use App\Models\UserMembership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::with('promoCode')
            ->withCount(['userMemberships', 'activeMembers', 'courses'])
            ->orderBy('price_monthly')
            ->get();

        $stats = [
            'total'          => MembershipPlan::count(),
            'active'         => MembershipPlan::active()->count(),
            'total_members'  => UserMembership::count(),
            'active_members' => UserMembership::active()->count(),
        ];

        return view('admin.memberships.index', compact('plans', 'stats'));
    }

    public function create()
    {
        $courses    = Course::published()->orderBy('title')->get(['id', 'title']);
        $promoCodes = PromoCode::where('is_active', true)->orderBy('code')->get(['id', 'code', 'discount_type', 'discount_value']);

        return view('admin.memberships.create', compact('courses', 'promoCodes'));
    }

    public function store(StoreMembershipPlanRequest $request)
    {
        $data = $request->validated();
        $data['features'] = $this->parseFeatures($request->input('features_text', ''));

        $plan = MembershipPlan::create($data);
        $plan->courses()->sync($request->input('course_ids', []));

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Paket membership berhasil dibuat.');
    }

    public function edit(MembershipPlan $membership)
    {
        $membership->loadCount(['userMemberships', 'activeMembers']);
        $membership->load('courses');

        $plan       = $membership;
        $courses    = Course::published()->orderBy('title')->get(['id', 'title']);
        $promoCodes = PromoCode::where('is_active', true)->orderBy('code')->get(['id', 'code', 'discount_type', 'discount_value']);

        return view('admin.memberships.edit', compact('plan', 'courses', 'promoCodes'));
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membership)
    {
        $data = $request->validated();
        $data['features'] = $this->parseFeatures($request->input('features_text', ''));

        $membership->update($data);
        $membership->courses()->sync($request->input('course_ids', []));

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Paket membership berhasil diperbarui.');
    }

    public function destroy(MembershipPlan $membership)
    {
        if ($membership->activeMembers()->exists()) {
            return back()->with('error', 'Tidak bisa hapus paket yang masih memiliki member aktif.');
        }

        $membership->delete();

        return back()->with('success', "Paket \"{$membership->name}\" berhasil dihapus.");
    }

    public function toggleActive(MembershipPlan $membership)
    {
        $membership->update(['is_active' => ! $membership->is_active]);

        $label = $membership->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Paket \"{$membership->name}\" berhasil {$label}.");
    }

    public function togglePopular(MembershipPlan $membership)
    {
        $membership->update(['is_popular' => ! $membership->is_popular]);

        $label = $membership->is_popular ? 'ditandai populer' : 'dihapus dari populer';

        return back()->with('success', "Paket \"{$membership->name}\" berhasil {$label}.");
    }

    /**
     * Parse features from textarea (one per line) to array.
     */
    private function parseFeatures(string $text): array
    {
        return collect(explode("\n", $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->toArray();
    }
}
