<?php

namespace App\Livewire;

use App\Models\Bootcamp;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BootcampFilter extends Component
{
    use WithPagination;

    // ── Applied Filter Properties (synced to URL query string) ───────────────

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'tipe', except: '')]
    public string $type = '';          // online | offline

    #[Url(as: 'status', except: '')]
    public string $status = '';        // upcoming | ongoing | completed

    #[Url(as: 'harga', except: '')]
    public string $price = '';         // free | paid

    #[Url(as: 'urut', except: 'terdekat')]
    public string $sort = 'terdekat';  // terdekat | populer | murah | mahal

    // ── Lifecycle Hooks ───────────────────────────────────────────────────────

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedSort(): void   { $this->resetPage(); }

    // ── Actions ───────────────────────────────────────────────────────────────

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Dipanggil dari Alpine via $wire.applyFilters(type, status, price)
     */
    public function applyFilters(string $type, string $status, string $price): void
    {
        $this->type   = $type;
        $this->status = $status;
        $this->price  = $price;
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->search = '';
        $this->type   = '';
        $this->status = '';
        $this->price  = '';
        $this->sort   = 'terdekat';
        $this->resetPage();
    }

    public function removeFilter(string $field): void
    {
        match ($field) {
            'search' => $this->search = '',
            'type'   => $this->type   = '',
            'status' => $this->status = '',
            'price'  => $this->price  = '',
            default  => null,
        };
        $this->resetPage();
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function activeCount(): int
    {
        return collect([$this->search, $this->type, $this->status, $this->price])
            ->filter(fn($v) => $v !== '')
            ->count();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = Bootcamp::query()->with('instructor:id,name,avatar');

        if ($this->search !== '') {
            $query = $query->search($this->search);
        }

        if ($this->type !== '') {
            $query = $query->where('type', $this->type);
        }

        if ($this->status !== '') {
            $query = $query->where('status', $this->status);
        } else {
            $query = $query->whereIn('status', ['upcoming', 'ongoing']);
        }

        if ($this->price === 'free') {
            $query = $query->where('price', 0);
        } elseif ($this->price === 'paid') {
            $query = $query->where('price', '>', 0);
        }

        $query = match ($this->sort) {
            'populer' => $query->orderByDesc('total_registered'),
            'murah'   => $query->orderBy(\Illuminate\Support\Facades\DB::raw('COALESCE(discount_price, price)')),
            'mahal'   => $query->orderByDesc(\Illuminate\Support\Facades\DB::raw('COALESCE(discount_price, price)')),
            default   => $query->orderBy('start_date'),
        };

        $bootcamps = $query->paginate(9);

        // Featured bootcamps (only show if no filters are active)
        $featuredBootcamps = collect();
        if ($this->search === '' && $this->type === '' && $this->status === '' && $this->price === '') {
            $featuredBootcamps = Bootcamp::upcoming()
                ->with('instructor:id,name,avatar')
                ->orderBy('start_date')
                ->limit(3)
                ->get();
        }

        return view('livewire.bootcamp-filter', compact('bootcamps', 'featuredBootcamps'));
    }
}

