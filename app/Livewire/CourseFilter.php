<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Course;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CourseFilter extends Component
{
    use WithPagination;

    // ── URL-synced filter state ───────────────────────────────────────────────
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'level', except: '')]
    public string $level = '';

    #[Url(as: 'price', except: '')]
    public string $price = '';          // '' | 'free' | 'paid'

    #[Url(as: 'rating', except: '')]
    public string $minRating = '';      // '' | '3' | '4' | '4.5'

    #[Url(as: 'sort', except: 'popular')]
    public string $sort = 'popular';    // popular|newest|rating|price_asc|price_desc

    // ── UI state (not URL-synced) ─────────────────────────────────────────────
    public bool $sidebarOpen = false;

    // ── Categories (loaded once in mount, not every render) ───────────────────
    public array $categories = [];

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->categories = $this->loadCategories();
    }

    // ── Apply filter (user clicks "Terapkan Filter" button) ──────────────────
    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->search    = '';
        $this->category  = '';
        $this->level     = '';
        $this->price     = '';
        $this->minRating = '';
        $this->sort      = 'popular';
        $this->resetPage();
    }

    public function removeFilter(string $field): void
    {
        $this->$field = match ($field) {
            'sort'  => 'popular',
            default => '',
        };
        $this->resetPage();
    }

    // ── Active filter count ───────────────────────────────────────────────────
    public function getActiveCountProperty(): int
    {
        return collect([$this->category, $this->level, $this->price, $this->minRating])
            ->filter(fn ($v) => $v !== '')
            ->count();
    }

    // ── Categories as plain arrays (loaded once in mount) ───────────────────
    private function loadCategories(): array
    {
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
                $childrenCount = $cat->children->sum('courses_count');
                return [
                    'slug'          => $cat->slug,
                    'name'          => $cat->name,
                    'courses_count' => $cat->own_courses_count + $childrenCount,
                ];
            })
            ->sortByDesc('courses_count')
            ->values()
            ->toArray();
    }

    // ── Render ────────────────────────────────────────────────────────────────
    public function render()
    {
        $query = Course::with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->published();

        if ($this->search !== '') {
            $query->search($this->search);
        }
        if ($this->category !== '') {
            $query->byCategory($this->category);
        }
        if ($this->level !== '') {
            $query->byLevel($this->level);
        }
        if ($this->price === 'free') {
            $query->free();
        } elseif ($this->price === 'paid') {
            $query->paid();
        }
        if ($this->minRating !== '') {
            $query->where('rating', '>=', (float) $this->minRating);
        }

        match ($this->sort) {
            'newest'     => $query->latest(),
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->topRated(),
            default      => $query->popular(),
        };

        $courses = $query->paginate(12);

        return view('livewire.course-filter', [
            'courses'       => $courses,
            'totalFiltered' => $courses->total(),
        ]);
    }
}
