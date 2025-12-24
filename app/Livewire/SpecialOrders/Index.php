<?php

namespace App\Livewire\SpecialOrders;

use App\Models\SpecialOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'       => SpecialOrder::count(),
            'in_progress' => SpecialOrder::where('status', 'in_progress')->orWhere('status', 'pending')->orWhere('status', 'searching_driver')->orWhere('status', 'paused')->count(),
            'completed'   => SpecialOrder::where('status', 'completed')->count(),
            'cancelled'   => SpecialOrder::where('status', 'cancelled')->count(),
        ];
    }

    public function applyFilter($filter)
    {
        $validFilters = ['all', 'in_progress', 'completed', 'cancelled'];
        if (in_array($filter, $validFilters)) {
            $this->activeFilter = $filter;
            $this->resetPage();
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return SpecialOrder::with(['driver', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('customer_name', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                      ->orWhere('start_address', 'like', '%' . $this->search . '%')
                      ->orWhere('end_address', 'like', '%' . $this->search . '%')
                      ->orWhereHas('driver', fn($driverQuery) => $driverQuery->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->activeFilter !== 'all', fn($q) => $q->where('status', $this->activeFilter))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.special-orders.index');
    }
}
