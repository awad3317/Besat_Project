<?php

namespace App\Livewire\Request;

use App\Models\Driver;
use App\Models\Request as RequestModel;
use App\Models\Vehicle;
use App\Notifications\DriverAssignedNotification;
use App\Notifications\RequestVehicleChangedNotification;
use App\Notifications\TripCancelledNotification;
use App\Repositories\ChatRepository;
use App\Services\ActivityLog;
use App\Services\DriverLocationService;
use App\Services\FirebaseService;
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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
    public bool $modalOnly = false;
    public bool $dashboardMode = false;

    // ── Assign Driver Modal ──
    public $selectedRequestId = null;
    public $driverSearch = '';
    public bool $onlineDriversOnly = true;
    public bool $matchVehicleOnly = true;

    // ── Cancel Request Modal ──
    public $cancellingRequestId = null;
    public $cancellationReason = '';

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    private function visibleRequestsQuery()
    {
        return RequestModel::where(function ($q) {
            $q->whereNotIn('payment_method', ['digital_payment', 'deposit'])
                ->orWhere('payment_status', 'paid');
        });
    }

    // ═══════════════════════════════════════════
    //  COMPUTED: Stats Cards
    // ═══════════════════════════════════════════
    #[Computed]
    public function stats()
    {
        $baseQuery = $this->visibleRequestsQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'in_progress' => (clone $baseQuery)->whereIn('status', ['pending', 'searching_driver', 'paused', 'in_progress', 'accepted', 'on_trip'])->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];
    }

    // ═══════════════════════════════════════════
    //  COMPUTED: Requests Table (paginated)
    // ═══════════════════════════════════════════
    #[Computed]
    public function requests()
    {
        if ($this->dashboardMode) {
            return $this->visibleRequestsQuery()
                ->with([
                    'driver',
                    'user' => fn ($query) => $query->withTrashed(),
                    'vehicle',
                    'bank:id,name',
                ])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return $this->visibleRequestsQuery()
            ->with([
                'driver',
                'user' => fn ($query) => $query->withTrashed(),
                'vehicle',
                'bank:id,name',
            ])
            ->when($this->search, function ($query) {
                $searchTerm = trim($this->search);
                $numericId = ltrim($searchTerm, '#');

                $query->where(function ($q) use ($searchTerm, $numericId) {
                    if ($numericId !== '' && ctype_digit($numericId)) {
                        $q->where('id', (int) $numericId)
                            ->orWhere('start_address', 'like', '%' . $searchTerm . '%');
                    } else {
                        $q->where('start_address', 'like', '%' . $searchTerm . '%');
                    }

                    $q->orWhere('end_address', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('driver', fn ($driverQuery) => $driverQuery->where('name', 'like', '%' . $searchTerm . '%'))
                        ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                            $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('phone', 'like', '%' . $searchTerm . '%');
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

    #[Computed]
    public function vehicles()
    {
        return Vehicle::select('id', 'type')->orderBy('id')->get();
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
            vehicleId: ($this->matchVehicleOnly && $request->vehicle_id) ? (int) $request->vehicle_id : null,
            limit: 30,
        );
    }

    // ═══════════════════════════════════════════
    //  Open Assign Driver Modal
    // ═══════════════════════════════════════════
    #[On('open-assign-driver-modal')]
    public function openAssignDriverModal($requestId = null)
    {
        if (is_array($requestId)) {
            $requestId = $requestId['requestId'] ?? $requestId['id'] ?? reset($requestId);
        }

        $request = RequestModel::find($requestId);
        if (! $request) {
            $this->dispatchError('الطلب غير موجود.');
            return;
        }

        if (! $request->canAssignDriver()) {
            $this->dispatchError($this->driverAssignmentBlockedMessage($request), 'غير مسموح!');
            return;
        }

        $this->selectedRequestId = $requestId;
        $this->driverSearch = '';
        $this->onlineDriversOnly = true;
        $this->matchVehicleOnly = true;

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
                'vehicle_id' => $driver->vehicle_id,
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
        if (! $this->selectedRequestId) {
            $this->dispatch('close-modal', 'assign-driver-modal');
            $this->dispatchError('لم يتم تحديد أي رحلة.');
            return;
        }

        $errorMessage = null;
        $assigned = false;
        $vehicleMismatch = false;
        $driverVehicleType = 'غير محدد';
        $requestVehicleType = 'غير محدد';

        DB::transaction(function () use (
            $driverId,
            $chatRepository,
            &$errorMessage,
            &$assigned,
            &$vehicleMismatch,
            &$driverVehicleType,
            &$requestVehicleType
        ) {
            // Lock the request row to prevent race conditions
            $request = RequestModel::with('vehicle:id,type')->where('id', $this->selectedRequestId)->lockForUpdate()->first();

            if (! $request) {
                $errorMessage = 'الطلب غير موجود.';
                return;
            }

            if (! $request->canAssignDriver()) {
                $errorMessage = $this->driverAssignmentBlockedMessage($request);
                return;
            }

            $driver = Driver::with('vehicle:id,type')->find($driverId);
            if (! $driver) {
                $errorMessage = 'السائق غير موجود.';
                return;
            }

            if ($request->vehicle_id && (int) $driver->vehicle_id !== (int) $request->vehicle_id) {
                $vehicleMismatch = true;
                $driverVehicleType = $driver->vehicle?->type ?? 'غير محدد';
                $requestVehicleType = $request->vehicle?->type ?? 'غير محدد';
                $errorMessage = 'لا يمكن تعيين سائق بمركبة تختلف عن نوع المركبة التي اختارها العميل في الطلب.';
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

            $assigned = true;
        });

        if (! $assigned) {
            if ($vehicleMismatch) {
                $this->dispatch(
                    'vehicle-mismatch-blocked',
                    driverVehicle: $driverVehicleType,
                    requestVehicle: $requestVehicleType
                );
                return;
            }

            $this->clearSelectedRequest();
            unset($this->selectedRequest, $this->drivers);
            $this->dispatch('close-modal', 'assign-driver-modal');
            $this->dispatchError($errorMessage ?? 'تعذر تعيين السائق للرحلة.', 'غير مسموح!');
            return;
        }

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
        if ($request && $driver && ! empty($driver->device_token)) {
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
    //  Change Request Vehicle Type
    // ═══════════════════════════════════════════
    public function changeRequestVehicle(
        $newVehicleId,
        $requestId = null,
        ?FirebaseService $firebaseService = null,
        ?PriceCalculationService $priceCalculationService = null
    ) {
        $targetRequestId = $requestId ?: $this->selectedRequestId;
        if (! $targetRequestId) {
            $this->dispatchError('لم يتم تحديد أي رحلة.');
            return;
        }

        $firebaseService = $firebaseService ?? app(FirebaseService::class);
        $priceCalculationService = $priceCalculationService ?? app(PriceCalculationService::class);
        $errorMessage = null;
        $changed = false;
        $oldVehicleId = null;
        $oldVehicleType = 'غير محدد';
        $newVehicleType = 'غير محدد';
        $oldFinalPrice = 0;
        $newFinalPrice = 0;
        $adminName = 'الدعم الفني';

        DB::transaction(function () use (
            $targetRequestId,
            $newVehicleId,
            $priceCalculationService,
            &$errorMessage,
            &$changed,
            &$oldVehicleId,
            &$oldVehicleType,
            &$newVehicleType,
            &$oldFinalPrice,
            &$newFinalPrice,
            &$adminName
        ) {
            $request = RequestModel::with(['vehicle:id,type', 'stops', 'discountCode'])
                ->where('id', $targetRequestId)
                ->lockForUpdate()
                ->first();

            if (! $request) {
                $errorMessage = 'الطلب غير موجود.';
                return;
            }

            if (! $request->canChangeVehicle()) {
                $errorMessage = ! $request->canAssignDriver()
                    ? 'لا يمكن تغيير نوع المركبة لرحلة مكتملة أو ملغية.'
                    : 'لا يمكن تغيير نوع المركبة بعد قبول الرحلة أو تعيين سائق لها.';
                return;
            }

            $newVehicle = Vehicle::with('pricing')->find($newVehicleId);
            if (! $newVehicle) {
                $errorMessage = 'نوع المركبة المختار غير موجود.';
                return;
            }

            if ((int) $request->vehicle_id === (int) $newVehicle->id) {
                return;
            }

            $oldVehicleId = $request->vehicle_id;
            $oldVehicleType = $request->vehicle?->type ?? 'غير محدد';
            $newVehicleType = $newVehicle->type;
            $oldFinalPrice = (float) $request->final_price;

            $tripDatetime = $request->trip_datetime
                ? $request->trip_datetime->format('Y-m-d H:i:s')
                : ($request->created_at ? $request->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'));

            $dataForPricing = [
                'start_latitude'  => (float) $request->start_latitude,
                'start_longitude' => (float) $request->start_longitude,
                'end_latitude'    => (float) $request->end_latitude,
                'end_longitude'   => (float) $request->end_longitude,
                'stops'           => $request->stops->map(fn ($s) => [
                    'latitude'  => (float) $s->latitude,
                    'longitude' => (float) $s->longitude,
                ])->toArray(),
                'wants_ac'        => (bool) $request->wants_ac,
                'trip_datetime'   => $tripDatetime,
            ];

            try {
                $priceDetails = $priceCalculationService->getFullPriceDetails(
                    $dataForPricing,
                    $newVehicle,
                    $request->discountCode
                );
            } catch (\Throwable $e) {
                // في حال تعذر الاتصال بخدمة المسافات الخارجية مع وجود المسافة محفوظة مسبقاً في الطلب، نستخدم دوال PriceCalculationService نفسها
                $distanceInKm = (float) ($request->distance_km ?? 0);
                if ($distanceInKm <= 0) {
                    $errorMessage = $e->getMessage();
                    return;
                }

                $pricePerKm = $priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $newVehicle);
                $distanceBreakdown = $priceCalculationService->getTieredDistanceBreakdown($distanceInKm, $newVehicle);
                $basePrice = $priceCalculationService->calculatePrice($distanceInKm, $newVehicle, $newVehicle->min_price);

                $surchargesDetails = [];
                $acCost = 0;
                $rawAcCost = 0.0;
                $acApplied = false;

                if ($request->wants_ac && $newVehicle->has_ac_option) {
                    $rawAcCost = $distanceInKm * (float) $newVehicle->ac_price_per_km;
                    $acCost = $priceCalculationService->roundPrice($rawAcCost);
                    $acApplied = true;
                    $surchargesDetails[] = [
                        'id'     => 'ac_cost',
                        'name'   => 'رسوم تشغيل التكييف',
                        'amount' => $acCost,
                    ];
                }

                $surchargesData = $priceCalculationService->calculateSurcharges($tripDatetime);
                $totalSurchargeAmount = (int) $surchargesData['total_amount'];

                if (! empty($surchargesData['details'])) {
                    $surchargesDetails = array_merge($surchargesDetails, $surchargesData['details']);
                }

                $originalPrice = $priceCalculationService->roundPrice($basePrice + $acCost + $totalSurchargeAmount);
                $finalPrice = $originalPrice;
                $discountAmount = 0;
                $couponForResponse = null;
                $couponDetails = null;
                $couponObject = $request->discountCode;

                if ($couponObject) {
                    $minTripFare = ! is_null($couponObject->min_trip_fare) ? (float) $couponObject->min_trip_fare : null;
                    $couponRate = (float) $couponObject->discount_rate;
                    $roundedDiscount = 0;
                    $wasCapped = false;
                    $wasRejectedByMinFare = false;

                    if ($minTripFare === null || $minTripFare <= 0 || $originalPrice >= $minTripFare) {
                        $couponForResponse = number_format($couponRate * 100, 2) . '%';
                        $roundedDiscount = $priceCalculationService->roundPrice($originalPrice * $couponRate);

                        if (! is_null($couponObject->max_discount_amount) && (float) $couponObject->max_discount_amount > 0) {
                            $wasCapped = $roundedDiscount > (float) $couponObject->max_discount_amount;
                            $discountAmount = (int) min($roundedDiscount, (float) $couponObject->max_discount_amount);
                        } else {
                            $discountAmount = (int) $roundedDiscount;
                        }

                        $discountAmount = min($discountAmount, (int) $originalPrice);
                        $finalPrice = max(0, $originalPrice - $discountAmount);

                        if ($discountAmount > 0) {
                            $surchargesDetails[] = [
                                'id'     => 'discount',
                                'name'   => "خصم قسيمة ({$couponForResponse})",
                                'amount' => -$discountAmount,
                            ];
                        }
                    } else {
                        $wasRejectedByMinFare = true;
                    }

                    $couponDetails = [
                        'id'                          => $couponObject->id ?? null,
                        'code'                        => $couponObject->code ?? null,
                        'discount_rate'               => $couponRate,
                        'discount_percentage'         => number_format($couponRate * 100, 2) . '%',
                        'min_trip_fare'               => $minTripFare,
                        'max_discount_amount'         => ! is_null($couponObject->max_discount_amount) ? (float) $couponObject->max_discount_amount : null,
                        'calculated_discount_rounded' => (int) $roundedDiscount,
                        'applied_discount_amount'     => (int) $discountAmount,
                        'was_capped_by_max_discount'  => $wasCapped,
                        'was_rejected_by_min_fare'    => $wasRejectedByMinFare,
                    ];
                }

                $appCommissionAmount = $priceCalculationService->calculateCommission($originalPrice);
                $driverAmount = $originalPrice - $appCommissionAmount;
                $companyNetRevenue = $appCommissionAmount - $discountAmount;

                $priceDetails = [
                    'distance_in_km'             => round($distanceInKm, 2),
                    'price_per_km'               => round((float) $pricePerKm, 2),
                    'effective_avg_price_per_km' => (float) $distanceBreakdown['effective_avg_price_per_km'],
                    'raw_distance_price'         => (float) $distanceBreakdown['raw_distance_price'],
                    'min_price_applied'          => (bool) $distanceBreakdown['min_price_applied'],
                    'base_price_before_rounding' => (float) $distanceBreakdown['base_price_before_rounding'],
                    'tiers_breakdown'            => $distanceBreakdown['tiers_breakdown'],
                    'base_price'                 => (int) $basePrice,
                    'original_price'             => (int) $originalPrice,
                    'final_price'                => (int) $finalPrice,
                    'vehicle'                    => $newVehicle->type,
                    'coupon'                     => $couponForResponse,
                    'coupon_details'             => $couponDetails,
                    'ac_applied'                 => $acApplied,
                    'raw_ac_cost'                => round($rawAcCost, 2),
                    'ac_cost'                    => (int) $acCost,
                    'total_surcharges'           => (int) $totalSurchargeAmount,
                    'discount_amount'            => (int) $discountAmount,
                    'app_commission_amount'      => (int) $appCommissionAmount,
                    'driver_amount'              => (int) $driverAmount,
                    'company_net_revenue'        => (int) $companyNetRevenue,
                    'surcharges_details'         => $surchargesDetails,
                ];
            }

            $newFinalPrice = (float) $priceDetails['final_price'];

            $adminUser = Auth::user() ?? auth()->user() ?? auth('web')->user();
            $adminName = $adminUser?->name ?? 'الدعم الفني';

            $request->update([
                'vehicle_id'            => $newVehicle->id,
                'distance_km'           => $priceDetails['distance_in_km'],
                'original_price'        => $priceDetails['original_price'],
                'final_price'           => $priceDetails['final_price'],
                'discount_amount'       => $priceDetails['discount_amount'],
                'surcharge_amount'      => $priceDetails['total_surcharges'],
                'ac_cost'               => $priceDetails['ac_cost'],
                'app_commission_amount' => $priceDetails['app_commission_amount'],
                'pricing_snapshot'      => [
                    'calculation_method'         => 'cumulative_tiered_segments',
                    'calculated_at'              => now()->format('Y-m-d H:i:s'),
                    'trip_datetime'              => $tripDatetime,
                    'vehicle_id'                 => $newVehicle->id,
                    'vehicle_type'               => $newVehicle->type,
                    'vehicle_min_price'          => (float) $newVehicle->min_price,
                    'distance_km'                => (float) $priceDetails['distance_in_km'],
                    'applied_price_per_km'       => (float) ($priceDetails['price_per_km'] ?? 0),
                    'effective_avg_price_per_km' => (float) ($priceDetails['effective_avg_price_per_km'] ?? 0),
                    'tiers_breakdown'            => $priceDetails['tiers_breakdown'] ?? [],
                    'raw_tiers_total_price'      => (float) ($priceDetails['raw_distance_price'] ?? $priceDetails['base_price']),
                    'min_price_applied'          => (bool) ($priceDetails['min_price_applied'] ?? false),
                    'base_price_before_rounding' => (float) ($priceDetails['base_price_before_rounding'] ?? $priceDetails['base_price']),
                    'base_price'                 => (float) $priceDetails['base_price'],
                    'wants_ac'                   => (bool) ($priceDetails['ac_applied'] ?? false),
                    'ac_price_per_km'            => (float) ($newVehicle->ac_price_per_km ?? 0),
                    'raw_ac_cost'                => (float) ($priceDetails['raw_ac_cost'] ?? 0),
                    'calculated_ac_cost'         => (float) $priceDetails['ac_cost'],
                    'surcharge_amount'           => (float) $priceDetails['total_surcharges'],
                    'surcharges_breakdown'       => $priceDetails['surcharges_details'] ?? [],
                    'original_price'             => (float) $priceDetails['original_price'],
                    'coupon_details'             => $priceDetails['coupon_details'] ?? null,
                    'discount_amount'            => (float) $priceDetails['discount_amount'],
                    'app_commission_rate'        => (float) ($priceDetails['app_commission_rate'] ?? 0),
                    'app_commission_amount'      => (float) $priceDetails['app_commission_amount'],
                    'driver_amount'              => (float) $priceDetails['driver_amount'],
                    'company_net_revenue'        => (float) $priceDetails['company_net_revenue'],
                    'final_price'                => (float) $priceDetails['final_price'],
                ],
            ]);

            $priceCalculationService->attachSurcharges($request, $priceDetails['surcharges_details'] ?? []);

            ActivityLog::log(
                'update',
                'Request',
                "تم تغيير نوع المركبة في الطلب #{$request->id} من ({$oldVehicleType}) إلى ({$newVehicleType}) وإعادة احتساب السعر من ({$oldFinalPrice}) إلى ({$newFinalPrice})، الدعم الفني: {$adminName}",
                $request->id
            );

            $changed = true;
        });

        if ($errorMessage) {
            $this->dispatchError($errorMessage, 'غير مسموح!');
            return;
        }

        if (! $changed) {
            return;
        }

        // Send Database + Firebase Notifications to Customer
        $request = RequestModel::with(['user.devices', 'vehicle:id,type'])->find($targetRequestId);

        if ($request && $request->user) {
            $title = 'تم تغيير نوع المركبة في طلبك';
            $body = "تم تغيير نوع المركبة في الطلب رقم #{$request->id} من ({$oldVehicleType}) إلى ({$newVehicleType}) وأصبح سعر الرحلة ({$newFinalPrice} ريال)، الدعم الفني: {$adminName}";

            // 1. Database notification
            $request->user->notify(new RequestVehicleChangedNotification(
                tripRequest: $request,
                oldVehicleType: $oldVehicleType,
                newVehicleType: $newVehicleType,
                adminName: $adminName,
                oldVehicleId: $oldVehicleId ? (int) $oldVehicleId : null,
                newVehicleId: (int) $newVehicleId,
                oldFinalPrice: $oldFinalPrice,
                newFinalPrice: $newFinalPrice,
            ));

            // 2. Firebase FCM push notification
            if ($request->user->is_notifications_enabled ?? true) {
                $deviceTokens = $request->user->devices->pluck('device_token')->filter()->toArray();
                if (! empty($deviceTokens)) {
                    $data = [
                        'new_request'      => 'false',
                        'new_message'      => 'false',
                        'request_id'       => (string) $request->id,
                        'status'           => (string) $request->status,
                        'old_vehicle_id'   => (string) ($oldVehicleId ?? ''),
                        'new_vehicle_id'   => (string) $newVehicleId,
                        'old_vehicle_type' => (string) $oldVehicleType,
                        'new_vehicle_type' => (string) $newVehicleType,
                        'old_final_price'  => (string) $oldFinalPrice,
                        'new_final_price'  => (string) $newFinalPrice,
                        'changed_by'       => (string) $adminName,
                        'click_action'     => 'FLUTTER_NOTIFICATION_CLICK',
                    ];
                    foreach ($deviceTokens as $token) {
                        try {
                            $firebaseService->sendNotification($token, $title, $body, $data);
                        } catch (\Exception $e) {
                            \Log::error("Failed to send vehicle change FCM to customer token: {$token}. Error: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Refresh computed properties & map if modal is open for this request
        unset($this->selectedRequest, $this->drivers, $this->requests);
        if ((int) $this->selectedRequestId === (int) $targetRequestId) {
            $this->dispatchMapUpdate();
        }

        $this->dispatch('vehicle-changed');
    }

    // ═══════════════════════════════════════════
    //  Cancel Request Modal & Action
    // ═══════════════════════════════════════════
    public function openCancelModal($requestId)
    {
        $request = RequestModel::find($requestId);
        if (! $request) {
            $this->dispatchError('الطلب غير موجود.');
            return;
        }

        if (in_array($request->status, ['completed', 'cancelled'], true)) {
            $this->dispatchError('لا يمكن إلغاء رحلة مكتملة أو ملغية بالفعل.', 'غير مسموح!');
            return;
        }

        $this->cancellingRequestId = $requestId;
        $this->cancellationReason = '';
        $this->resetValidation('cancellationReason');

        $this->dispatch('open-modal', 'cancel-request-modal');
    }

    public function cancelRequest(?FirebaseService $firebaseService = null)
    {
        $this->validate([
            'cancellationReason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'cancellationReason.required' => 'يرجى إدخال سبب إلغاء الرحلة.',
            'cancellationReason.min'      => 'سبب الإلغاء يجب أن لا يقل عن 3 أحرف.',
            'cancellationReason.max'      => 'سبب الإلغاء يجب أن لا يتجاوز 500 حرف.',
        ]);

        if (! $this->cancellingRequestId) {
            $this->dispatch('close-modal', 'cancel-request-modal');
            $this->dispatchError('لم يتم تحديد أي رحلة.');
            return;
        }

        $adminUser = Auth::user() ?? auth()->user() ?? auth('web')->user();
        $adminId = $adminUser?->id;
        $adminName = $adminUser?->name ?? 'المسؤول';
        $reason = trim($this->cancellationReason);
        $errorMessage = null;
        $errorTitle = 'خطأ!';
        $cancelledRequestId = null;

        DB::transaction(function () use ($adminId, $adminName, $reason, &$errorMessage, &$errorTitle, &$cancelledRequestId) {
            $lockedRequest = RequestModel::where('id', $this->cancellingRequestId)
                ->lockForUpdate()
                ->first();

            if (! $lockedRequest) {
                $errorMessage = 'الطلب غير موجود.';
                return;
            }

            if (in_array($lockedRequest->status, ['completed', 'cancelled'], true)) {
                $errorTitle = 'غير مسموح!';
                $errorMessage = 'لا يمكن إلغاء هذه الرحلة لأنها مكتملة أو ملغية بالفعل.';
                return;
            }

            $lockedRequest->update([
                'status'       => 'cancelled',
                'cancelled_by' => $adminId,
            ]);

            ActivityLog::log(
                'update',
                'Request',
                "تم إلغاء الرحلة #{$lockedRequest->id} بواسطة ({$adminName}) - السبب: {$reason}",
                $lockedRequest->id
            );

            $cancelledRequestId = $lockedRequest->id;
        });

        if ($errorMessage) {
            $this->dispatch('close-modal', 'cancel-request-modal');
            $this->dispatchError($errorMessage, $errorTitle);
            return;
        }

        // إشعار العميل والسائق إن وجد بعد الإلغاء (قاعدة البيانات + Firebase FCM) مع السبب
        $request = RequestModel::with(['user.devices', 'driver'])->find($cancelledRequestId);
        $firebaseService = $firebaseService ?? app(FirebaseService::class);
        $notificationTitle = 'تم إلغاء الرحلة!';
        $notificationData = [
            'new_request'  => 'false',
            'new_message'  => 'false',
            'request_id'   => (string) ($request?->id ?? $cancelledRequestId),
            'status'       => 'cancelled',
            'cancelled_by' => 'ADMIN',
            'reason'       => $reason,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        // 1. إشعار العميل (Database + Firebase FCM)
        if ($request && $request->user) {
            try {
                $request->user->notify(new TripCancelledNotification($request, $reason, 'ADMIN'));
            } catch (\Throwable $e) {
                \Log::error("Failed to store cancellation notification for user {$request->user_id}: " . $e->getMessage());
            }

            if ($request->user->is_notifications_enabled ?? true) {
                $deviceTokens = $request->user->devices?->pluck('device_token')->filter()->unique()->values()->toArray() ?? [];
                if (! empty($deviceTokens)) {
                    $customerBody = "تم إلغاء طلب الرحلة رقم #{$request->id} من قبل الإدارة. السبب: {$reason}";
                    foreach ($deviceTokens as $token) {
                        try {
                            $firebaseService->sendNotification($token, $notificationTitle, $customerBody, $notificationData);
                        } catch (\Throwable $e) {
                            \Log::error("Failed to send cancellation FCM to customer token {$token}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // 2. إشعار السائق إن كان معيّناً (Database + Firebase FCM) مع السبب
        if ($request && $request->driver) {
            try {
                $request->driver->notify(new TripCancelledNotification($request, $reason, 'ADMIN'));
            } catch (\Throwable $e) {
                \Log::error("Failed to store cancellation notification for driver {$request->driver->id}: " . $e->getMessage());
            }

            if (! empty($request->driver->device_token)) {
                $driverBody = "تم إلغاء الرحلة رقم #{$request->id} من قبل الإدارة. السبب: {$reason}";
                try {
                    $firebaseService->sendNotification(
                        $request->driver->device_token,
                        $notificationTitle,
                        $driverBody,
                        $notificationData
                    );
                } catch (\Throwable $e) {
                    \Log::error("Failed to send cancellation FCM to driver {$request->driver->id}: " . $e->getMessage());
                }
            }
        }

        $this->cancellingRequestId = null;
        $this->cancellationReason = '';
        unset($this->stats, $this->requests, $this->selectedRequest);

        $this->dispatch('close-modal', 'cancel-request-modal');
    }

    private function driverAssignmentBlockedMessage(RequestModel $request): string
    {
        return match ($request->status) {
            'completed' => 'لا يمكن تعيين سائق لرحلة مكتملة.',
            'cancelled' => 'لا يمكن تعيين سائق لرحلة ملغية.',
            default => 'لا يمكن تعيين سائق لهذه الرحلة في حالتها الحالية.',
        };
    }

    private function dispatchError(string $message, string $title = 'خطأ!'): void
    {
        session()->flash('error', $message);
        $this->dispatch('open-error-modal', title: $title, message: $message);
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
        $this->onlineDriversOnly = true;
        $this->matchVehicleOnly = true;
    }

    public function render()
    {
        return view('livewire.request.index');
    }
}
