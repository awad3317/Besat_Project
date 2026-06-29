<?php

namespace App\Livewire\Request;

use App\Models\Request as RequestModel;
use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Services\FirebaseService;
use App\Notifications\DriverAssignedNotification;

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
            'in_progress' => RequestModel::whereIn('status', ['pending', 'searching_driver', 'paused', 'in_progress', 'accepted', 'on_trip'])->count(),
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
                    return $query->whereIn('status', ['pending', 'searching_driver', 'paused', 'in_progress', 'accepted', 'on_trip']);
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

        return Driver::where('is_banned', false)
            ->when($request, function ($query) use ($request) {
                $query->where('vehicle_id', $request->vehicle_id);
            })
            ->when($this->driverSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->driverSearch . '%')
                      ->orWhere('phone', 'like', '%' . $this->driverSearch . '%');
                });
            })
            ->orderByDesc('is_online')
            ->orderByDesc('is_active')
            ->limit(10)
            ->get();
    }

    public function openAssignDriverModal($requestId,)
    {
        $this->selectedRequestId = $requestId;
        $this->driverSearch = '';
        $this->dispatch('open-modal', 'assign-driver-modal');
    }

    public function assignDriver($driverId, FirebaseService $firebaseService)
    {
        $request = RequestModel::find($this->selectedRequestId);
        if ($request) {
            $request->update([
                'driver_id' => $driverId,
                'status' => 'accepted' // 'accepted' is the correct database enum value for requests
            ]);
            if ($request->user) {
                $request->user->notify(new DriverAssignedNotification($request));
                $deviceTokens = $request->user->devices->pluck('device_token')->filter()->toArray();
                if (!empty($deviceTokens)) {
                $title = 'تم تعيين سائق!';
                $body = 'تم تعيين سائق لرحلتك وهو الآن في الطريق إليك.';
                $data = [
                    'request_id'   => (string) $request->id,
                    'status'       => 'accepted',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ];
                foreach ($deviceTokens as $token) {
                    try {
                        $firebaseService->sendNotification($token, $title, $body, $data);
                    } catch (\Exception $e) {
                        \Log::error("Failed to send FCM to token: {$token}. Error: " . $e->getMessage());
                    }
                }
            }
        }

        $this->dispatch('driver-assigned');
    }}

    public function clearSelectedRequest()
    {
        $this->selectedRequestId = null;
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
