<?php

namespace App\Livewire\Request;

use App\Models\Driver;
use App\Models\Request as RequestModel;
use App\Notifications\DriverAssignedNotification;
use App\Repositories\ChatRepository;
use App\Services\ActivityLog;
use App\Services\DriverLocationService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'custom-pagination';
    public $page;

    // ── Table Filters ──
    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    // ── Assign Driver Modal ──
    public $selectedRequestId = null;
    public $driverSearch = '';
    public bool $onlineDriversOnly = false;
    public bool $matchVehicleOnly = false;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    // ═══════════════════════════════════════════
    //  COMPUTED: Stats Cards
    // ═══════════════════════════════════════════
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

    // ═══════════════════════════════════════════
    //  COMPUTED: Requests Table (paginated)
    // ═══════════════════════════════════════════
    #[Computed]
    public function requests()
    {
        return RequestModel::with(['driver', 'user', 'vehicle'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('start_address', 'like', '%' . $this->search . '%')
                        ->orWhere('end_address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('driver', fn ($driverQuery) => $driverQuery->where('name', 'like', '%' . $this->search . '%'))
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

    // ═══════════════════════════════════════════
    //  COMPUTED: Selected Request (for modal)
    // ═══════════════════════════════════════════
    #[Computed]
    public function selectedRequest()
    {
        if (! $this->selectedRequestId) {
            return null;
        }

        return RequestModel::with(['user:id,name,phone', 'vehicle:id,type', 'stops'])
            ->find($this->selectedRequestId);
    }

    // ═══════════════════════════════════════════
    //  COMPUTED: Nearby Drivers (for modal list & map)
    // ═══════════════════════════════════════════
    #[Computed]
    public function drivers()
    {
        $request = $this->selectedRequest;
        if (! $request) {
            return collect();
        }

        $lat = $request->start_latitude;
        $lng = $request->start_longitude;

        // Fallback if request has no coordinates
        if (! $lat || ! $lng) {
            return Driver::where('is_banned', false)
                ->where('is_active', true)
                ->with('vehicle:id,type')
                ->when($this->onlineDriversOnly, fn ($q) => $q->where('is_online', true))
                ->when($this->matchVehicleOnly && $request->vehicle_id, fn ($q) => $q->where('vehicle_id', $request->vehicle_id))
                ->when($this->driverSearch, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('name', 'like', "%{$this->driverSearch}%")
                            ->orWhere('phone', 'like', "%{$this->driverSearch}%")
                            ->orWhere('plate_number', 'like', "%{$this->driverSearch}%");
                    });
                })
                ->orderByDesc('is_online')
                ->limit(30)
                ->get();
        }

        $service = app(DriverLocationService::class);

        return $service->getNearestDriversForDashboard(
            latitude: (float) $lat,
            longitude: (float) $lng,
            search: $this->driverSearch,
            onlineOnly: $this->onlineDriversOnly,
            vehicleId: $this->matchVehicleOnly ? $request->vehicle_id : null,
            limit: 30,
        );
    }

    // ═══════════════════════════════════════════
    //  Open Assign Driver Modal
    // ═══════════════════════════════════════════
    public function openAssignDriverModal($requestId)
    {
        $this->selectedRequestId = $requestId;
        $this->driverSearch = '';
        $this->onlineDriversOnly = false;
        $this->matchVehicleOnly = false;

        // Clear cached computed properties so they re-evaluate
        unset($this->selectedRequest, $this->drivers);

        $this->dispatch('open-modal', 'assign-driver-modal');

        // Send initial map data after modal opens
        $this->dispatchMapUpdate();
    }

    // ═══════════════════════════════════════════
    //  Dispatch Map Data to Browser
    // ═══════════════════════════════════════════
    public function dispatchMapUpdate()
    {
        $request = $this->selectedRequest;
        if (! $request) {
            return;
        }

        $drivers = $this->drivers;

        $driverMarkers = $drivers->map(function ($driver) {
            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'phone' => $driver->phone,
                'plate_number' => $driver->plate_number,
                'vehicle_type' => $driver->vehicle?->type ?? 'غير محدد',
                'driver_image' => $driver->driver_image ? url($driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg'),
                'is_online' => (bool) $driver->is_online,
                'latitude' => $driver->latitude,
                'longitude' => $driver->longitude,
                'distance_km' => isset($driver->distance_km) ? round($driver->distance_km, 1) : null,
            ];
        })->filter(fn ($d) => $d['latitude'] && $d['longitude'])->values()->toArray();

        $stops = $request->stops->map(fn ($stop) => [
            'latitude' => $stop->latitude,
            'longitude' => $stop->longitude,
            'order' => $stop->stop_order,
        ])->toArray();

        $this->dispatch('sync-dispatch-map', [
            'pickup' => [
                'latitude' => $request->start_latitude,
                'longitude' => $request->start_longitude,
                'address' => $request->start_address,
            ],
            'dropoff' => [
                'latitude' => $request->end_latitude,
                'longitude' => $request->end_longitude,
                'address' => $request->end_address,
            ],
            'stops' => $stops,
            'drivers' => $driverMarkers,
        ]);
    }

    // ═══════════════════════════════════════════
    //  Filter Hooks → Re-dispatch map data
    // ═══════════════════════════════════════════
    public function updatedDriverSearch()
    {
        unset($this->drivers);
        $this->dispatchMapUpdate();
    }

    public function updatedOnlineDriversOnly()
    {
        unset($this->drivers);
        $this->dispatchMapUpdate();
    }

    public function updatedMatchVehicleOnly()
    {
        unset($this->drivers);
        $this->dispatchMapUpdate();
    }

    // ═══════════════════════════════════════════
    //  Table Filter & Search Hooks
    // ═══════════════════════════════════════════
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

    // ═══════════════════════════════════════════
    //  Assign Driver (Transactional)
    // ═══════════════════════════════════════════
    public function assignDriver(int $driverId, FirebaseService $firebaseService, ChatRepository $chatRepository)
    {
        DB::transaction(function () use ($driverId, $chatRepository) {
            // Lock the request row to prevent race conditions
            $request = RequestModel::where('id', $this->selectedRequestId)->lockForUpdate()->first();

            if (! $request) {
                $this->dispatch('close-modal', 'assign-driver-modal');
                session()->flash('error', 'الطلب غير موجود.');
                return;
            }

            if ($request->status === 'cancelled') {
                $this->dispatch('close-modal', 'assign-driver-modal');
                session()->flash('error', 'لا يمكن تعيين سائق لطلب ملغي.');
                return;
            }

            $driver = Driver::find($driverId);
            if (! $driver) {
                session()->flash('error', 'السائق غير موجود.');
                return;
            }

            // Create or get the order conversation
            $chatRepository->getOrCreateOrderConversation(
                $request->id,
                $request->user_id,
                $driverId,
            );

            // Update request
            $request->update([
                'driver_id' => $driverId,
                'status' => 'accepted',
            ]);

            // Log administrative action
            ActivityLog::log(
                'update',
                'Request',
                "تم تعيين السائق #{$driverId} ({$driver->name}) للطلب #{$request->id} بواسطة المسؤول.",
                $request->id
            );
        });

        // ── Post-Transaction: Notifications (outside DB::transaction so FCM failures don't rollback) ──
        $request = RequestModel::with(['user.devices'])->find($this->selectedRequestId);
        $driver = Driver::find($driverId);

        if ($request && $request->user && $request->user->is_notifications_enabled) {
            // Database notification
            $request->user->notify(new DriverAssignedNotification($request));

            // FCM push to customer devices
            $deviceTokens = $request->user->devices->pluck('device_token')->filter()->toArray();
            if (! empty($deviceTokens)) {
                $title = 'تم تعيين سائق!';
                $body = 'تم تعيين سائق لرحلتك وهو الآن في الطريق إليك.';
                $data = [
                    'new_request' => 'false',
                    'new_message' => 'false',
                    'request_id' => (string) $request->id,
                    'status' => 'accepted',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ];
                foreach ($deviceTokens as $token) {
                    try {
                        $firebaseService->sendNotification($token, $title, $body, $data);
                    } catch (\Exception $e) {
                        \Log::error("Failed to send FCM to customer token: {$token}. Error: " . $e->getMessage());
                    }
                }
            }
        }

        // FCM push to driver
        if ($driver && ! empty($driver->device_token)) {
            $driverTitle = 'طلب جديد مسند إليك!';
            $driverBody = 'تم إسناد الطلب رقم #' . $request->id . ' إليك بنجاح.';
            $driverData = [
                'new_request' => 'true',
                'new_message' => 'false',
                'request_id' => (string) $request->id,
                'status' => 'accepted',
                'start_latitude' => (string) ($request->start_latitude ?? ''),
                'start_longitude' => (string) ($request->start_longitude ?? ''),
                'start_address' => (string) ($request->start_address ?? ''),
                'end_address' => (string) ($request->end_address ?? ''),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            try {
                $firebaseService->sendNotification($driver->device_token, $driverTitle, $driverBody, $driverData);
            } catch (\Exception $e) {
                \Log::error("Failed to send FCM to driver {$driver->id}. Error: " . $e->getMessage());
            }
        }

        $this->dispatch('driver-assigned');
    }

    // ═══════════════════════════════════════════
    //  Chat Actions
    // ═══════════════════════════════════════════
    public function startChatWithCustomer($requestId)
    {
        $request = RequestModel::find($requestId);
        if (! $request || ! $request->user_id) {
            return;
        }

        $conversation = \App\Models\Conversation::firstOrCreate(
            ['user_id' => $request->user_id, 'driver_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'clients']);
    }

    public function startChatWithDriver($requestId)
    {
        $request = RequestModel::find($requestId);
        if (! $request || ! $request->driver_id) {
            return;
        }

        $conversation = \App\Models\Conversation::firstOrCreate(
            ['driver_id' => $request->driver_id, 'user_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'drivers']);
    }

    // ═══════════════════════════════════════════
    //  Cleanup
    // ═══════════════════════════════════════════
    public function clearSelectedRequest()
    {
        $this->selectedRequestId = null;
        $this->driverSearch = '';
        $this->onlineDriversOnly = false;
        $this->matchVehicleOnly = false;
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
