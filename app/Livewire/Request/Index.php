<?php

namespace App\Livewire\Request;

use App\Models\Request as RequestModel;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'custom-pagination';
    public $page;

    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    
    #[Computed]
    public function stats()
    {
        return [
            'total' => RequestModel::count(),
            'in_progress' => RequestModel::whereIn('status', ['pending', 'searching_driver', 'paused', 'in_progress'])->count(),
            'completed' => RequestModel::where('status', 'completed')->count(),
            'cancelled' => RequestModel::where('status', 'cancelled')->count(),
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
    public function requests()
    {
        return RequestModel::with(['driver', 'user']) 
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    
                    $q->where('start_address', 'like', '%' . $this->search . '%')
                        ->orWhere('end_address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('driver', fn($driverQuery) => $driverQuery->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%')
                                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->activeFilter !== 'all', function ($query) {
                
                if ($this->activeFilter === 'in_progress') {
                    return $query->whereIn('status', ['pending', 'searching_driver', 'paused', 'in_progress']);
                }
                return $query->where('status', $this->activeFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
