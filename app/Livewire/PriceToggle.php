<?php

namespace App\Livewire;

use App\Models\MembershipPlan;
use Livewire\Component;

class PriceToggle extends Component
{
    public string $cycle = 'monthly';

    // Data dikirim dari parent view
    public $plans;
    public $activeMembership;

    public function mount($plans = null, $activeMembership = null): void
    {
        $this->plans            = $plans ?? MembershipPlan::active()->orderBy('price_monthly')->get();
        $this->activeMembership = $activeMembership;
    }

    public function toggleCycle(): void
    {
        $this->cycle = $this->cycle === 'monthly' ? 'yearly' : 'monthly';
    }

    public function setCycle(string $cycle): void
    {
        $this->cycle = in_array($cycle, ['monthly', 'yearly']) ? $cycle : 'monthly';
    }

    public function render()
    {
        return view('livewire.price-toggle', [
            'plans' => $this->plans,
            'cycle' => $this->cycle,
            'activeMembership' => $this->activeMembership,
        ]);
    }
}
