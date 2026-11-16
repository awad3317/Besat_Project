<?php

namespace App\Livewire\Ratings;

use App\Models\Rating;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'custom-pagination';

    public $activeFilter = 'all';

    public $starFilter = '';

    public $search = '';

    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'starFilter' => ['except' => '', 'as' => 'stars'],
        'page' => ['except' => 1],
    ];

    #[Computed]
    public function stats(): array
    {
        $row = Rating::selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN rating_value >= 4 THEN 1 ELSE 0 END), 0) as high,
            COALESCE(SUM(CASE WHEN rating_value <= 2 THEN 1 ELSE 0 END), 0) as low,
            COALESCE(SUM(CASE WHEN comment IS NOT NULL AND TRIM(comment) != '' THEN 1 ELSE 0 END), 0) as commented,
            COALESCE(AVG(rating_value), 0) as average
        ")->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'high' => (int) ($row->high ?? 0),
            'low' => (int) ($row->low ?? 0),
            'commented' => (int) ($row->commented ?? 0),
            'average' => (float) ($row->average ?? 0),
        ];
    }

    public function applyFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        $this->starFilter = '';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStarFilter(): void
    {
        if ($this->starFilter !== '') {
            $this->activeFilter = 'all';
        }
        $this->resetPage();
    }

    #[Computed]
    public function ratings()
    {
        $search = trim($this->search);

        return Rating::query()
            ->select([
                'id',
                'request_id',
                'driver_id',
                'user_id',
                'rating_value',
                'comment',
                'created_at',
            ])
            ->with([
                'user' => fn ($query) => $query->withTrashed()->select('id', 'name', 'phone', 'deleted_at'),
                'driver' => fn ($query) => $query->select('id', 'name', 'phone', 'driver_image', 'vehicle_id'),
                'driver.vehicle:id,type',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $numericSearch = ltrim($search, '#');

                $query->where(function ($q) use ($like, $numericSearch) {
                    $q->where('comment', 'like', $like)
                        ->when(is_numeric($numericSearch), fn ($sub) => $sub->orWhere('request_id', (int) $numericSearch)
                            ->orWhere('id', (int) $numericSearch))
                        ->orWhereHas('user', fn ($u) => $u->withTrashed()->where(function ($uq) use ($like) {
                            $uq->where('name', 'like', $like)
                                ->orWhere('phone', 'like', $like);
                        }))
                        ->orWhereHas('driver', fn ($d) => $d->where(function ($dq) use ($like) {
                            $dq->where('name', 'like', $like)
                                ->orWhere('phone', 'like', $like);
                        }));
                });
            })
            ->when($this->activeFilter === 'high', fn ($q) => $q->where('rating_value', '>=', 4))
            ->when($this->activeFilter === 'low', fn ($q) => $q->where('rating_value', '<=', 2))
            ->when($this->activeFilter === 'commented', fn ($q) => $q->whereNotNull('comment')->whereRaw("TRIM(comment) != ''"))
            ->when($this->starFilter !== '' && in_array((int) $this->starFilter, [1, 2, 3, 4, 5], true), function ($q) {
                $q->where('rating_value', (int) $this->starFilter);
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.ratings.index');
    }
}
