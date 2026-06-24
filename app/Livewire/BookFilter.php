<?php

namespace App\Livewire;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BookFilter extends Component
{
    use WithPagination;

    // ── URL-synced filter properties ──────────────────────────────────────────

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'tipe')]
    public string $type = '';

    #[Url(as: 'harga')]
    public string $price = '';

    #[Url(as: 'urut', except: 'terbaru')]
    public string $sort = 'terbaru';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingPrice(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function clearAll(): void
    {
        $this->reset(['search', 'type', 'price', 'sort']);
        $this->sort = 'terbaru';
        $this->resetPage();
    }

    public function removeFilter(string $field): void
    {
        match ($field) {
            'search' => $this->search = '',
            'type'   => $this->type   = '',
            'price'  => $this->price  = '',
            'sort'   => $this->sort   = 'terbaru',
            default  => null,
        };
        $this->resetPage();
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    public function getActiveCountProperty(): int
    {
        return collect([
            $this->search,
            $this->type,
            $this->price,
        ])->filter(fn($v) => $v !== '')->count();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = Book::published()
            ->with('instructor:id,name,avatar');

        // Search
        if ($this->search !== '') {
            $query->search($this->search);
        }

        // Type filter
        if ($this->type !== '') {
            if ($this->type === 'digital') {
                $query->whereIn('type', ['digital', 'both']);
            } elseif ($this->type === 'physical') {
                $query->whereIn('type', ['physical', 'both']);
            }
        }

        // Price filter
        if ($this->price === 'free') {
            $query->where(function ($q) {
                $q->where('price', 0)
                  ->orWhereNull('price');
            });
        } elseif ($this->price === 'paid') {
            $query->where('price', '>', 0);
        }

        // Sort
        $query = match ($this->sort) {
            'terbaru'  => $query->latest(),
            'populer'  => $query->orderByDesc(
                DB::raw('(SELECT COUNT(*) FROM book_orders WHERE book_orders.book_id = books.id)')
            ),
            'murah'    => $query->orderBy(DB::raw('COALESCE(discount_price, price)'), 'asc'),
            'mahal'    => $query->orderBy(DB::raw('COALESCE(discount_price, price)'), 'desc'),
            'az'       => $query->orderBy('title', 'asc'),
            default    => $query->latest(),
        };

        $books = $query->paginate(12);

        return view('livewire.book-filter', [
            'books'       => $books,
            'activeCount' => $this->activeCount,
        ]);
    }
}
