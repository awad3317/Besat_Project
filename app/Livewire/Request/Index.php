<?php

namespace App\Livewire\Request;

use App\Models\Request as RequestModel;
use App\Models\Driver;
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

    public $selectedRequestId = null;
    public $driverSearch = '';

    // ... existing properties ...

    #[Computed]
    public function drivers()
    {
        $request = $this->selectedRequestId ? RequestModel::find($this->selectedRequestId) : null;

        return Driver::where('is_active', true)
            ->where('is_online', true)
            ->where('is_banned', false)
            ->when($request, function ($query) use ($request) {
                $query->where('vehicle_id', $request->vehicle_id);
            })
            ->when($this->driverSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->driverSearch . '%')
                      ->orWhere('phone', 'like', '%' . $this->driverSearch . '%');
            })
            ->limit(10)
            ->get();
    }

    public function openAssignDriverModal($requestId)
    {
        $this->selectedRequestId = $requestId;
        $this->driverSearch = '';
        $this->dispatch('open-modal', 'assign-driver-modal');
    }

    public function assignDriver($driverId)
    {
        $request = RequestModel::find($this->selectedRequestId);
        if ($request) {
            $request->update([
                'driver_id' => $driverId,
                'status' => 'in_progress' // Or keep current status depending on logic, but usually assignment implies progress
            ]);
            
            // Generate notification logic if needed (optional for now as per plan)
        }

        $this->dispatch('close-modal', 'assign-driver-modal');
        $this->selectedRequestId = null;
        $this->dispatch('notify', message: 'تم تعيين السائق بنجاح', type: 'success');
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
