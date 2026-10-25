<?php

namespace App\Livewire\Request;

use App\Models\Driver;
use App\Models\Request as RequestModel;
use App\Notifications\DriverAssignedNotification;
use App\Repositories\ChatRepository;
use App\Services\FirebaseService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function assignDriver($driverId, FirebaseService $firebaseService, ChatRepository $chatRepository)
    {
        $request = RequestModel::find($this->selectedRequestId);
        if (!$request) {
            return;
        }

        if ($request->status === 'cancelled') {
            $this->dispatch('close-modal', 'assign-driver-modal');
            session()->flash('error', 'لا يمكن تعيين سائق لطلب ملغي.');
            return;
        }

        $driver = Driver::find($driverId);
        $conversation = $chatRepository->getOrCreateOrderConversation(
            $request->id,
            $request->user_id,
            $driverId
        );

        $request->update([
            'driver_id' => $driverId,
            'status' => 'accepted',
        ]);

        // 1. إرسال إشعار للعميل
        if ($request->user && $request->user->is_notifications_enabled) {
            $request->user->notify(new DriverAssignedNotification($request));
            $deviceTokens = $request->user->devices->pluck('device_token')->filter()->toArray();
            if (!empty($deviceTokens)) {
                $title = 'تم تعيين سائق!';
                $body = 'تم تعيين سائق لرحلتك وهو الآن في الطريق إليك.';
                $data = [
                    'new_request'  => 'false',
                    'new_message'  => 'false',
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

        \Log::info("1. Checking Driver:", ['id' => $driver?->id, 'token' => $driver?->device_token]);
        // 2. إرسال إشعار للسائق عبر Firebase
        if ($driver && !empty($driver->device_token)) {
            $driverTitle = 'طلب جديد مسند إليك!';
            $driverBody  = 'تم إسناد الطلب رقم #' . $request->id . ' إليك بنجاح.';
            $driverData  = [
                'new_request'     => 'true',
                'new_message'     => 'false',
                'request_id'      => (string) $request->id,
                'status'          => 'accepted',
                'start_latitude'  => (string) ($request->start_latitude ?? ''),
                'start_longitude' => (string) ($request->start_longitude ?? ''),
                'start_address'   => (string) ($request->start_address ?? ''),
                'end_address'     => (string) ($request->end_address ?? ''),
                'click_action'    => 'FLUTTER_NOTIFICATION_CLICK'
            ];
           

            try {
                $firebaseService->sendNotification($driver->device_token, $driverTitle, $driverBody, $driverData);
            } catch (\Exception $e) {
                \Log::error("Failed to send FCM to driver {$driver->id}. Error: " . $e->getMessage());
            }
        }
        

        $this->dispatch('driver-assigned');
    }

    public function startChatWithCustomer($requestId)
    {
        $request = RequestModel::find($requestId);
        if (!$request || !$request->user_id) return;
        
        $conversation = \App\Models\Conversation::firstOrCreate(
            ['user_id' => $request->user_id, 'driver_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'clients']);
    }

    public function startChatWithDriver($requestId)
    {
        $request = RequestModel::find($requestId);
        if (!$request || !$request->driver_id) return;
        
        $conversation = \App\Models\Conversation::firstOrCreate(
            ['driver_id' => $request->driver_id, 'user_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'drivers']);
    }

    public function clearSelectedRequest()
    {
        $this->selectedRequestId = null;
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
