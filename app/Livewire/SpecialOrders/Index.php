<?php

namespace App\Livewire\SpecialOrders;

use App\Models\Conversation;
use App\Models\Driver;
use App\Models\SpecialOrder;
use App\Services\ActivityLog;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'custom-pagination';

    // ── الخصائص الأساسية للفلترة والبحث والترقيم ──
    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    // ── خصائص نافذة تعيين السائق ──
    public $selectedOrderId = null;
    public $driverSearch = '';
    public bool $onlineDriversOnly = false;
    public $assignOrderPrice = null;

    // ── خصائص نافذة إلغاء الطلب ──
    public $cancellingOrderId = null;
    public $cancellationReason = '';

    // ── خصائص نافذة تعديل الطلب والتسعيرة ──
    public $editingOrderId = null;
    public $editTitle = '';
    public $editCustomerName = '';
    public $editCustomerPhone = '';
    public $editCustomerWhatsapp = '';
    public $editStartAddress = '';
    public $editEndAddress = '';
    public $editPrice = null;
    public $editDescription = '';
    public $editStatus = 'pending';

    // ── خصائص نافذة عرض التفاصيل السريع ──
    public $viewingOrderId = null;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    // ═══════════════════════════════════════════
    //  دالة الإحصائيات (Stats)
    // ═══════════════════════════════════════════
    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'       => SpecialOrder::count(),
            'in_progress' => SpecialOrder::whereIn('status', ['in_progress', 'pending', 'searching_driver', 'paused'])->count(),
            'completed'   => SpecialOrder::where('status', 'completed')->count(),
            'cancelled'   => SpecialOrder::where('status', 'cancelled')->count(),
        ];
    }

    // ═══════════════════════════════════════════
    //  دالة الفلترة (applyFilter)
    // ═══════════════════════════════════════════
    public function applyFilter($filter)
    {
        $validFilters = ['all', 'in_progress', 'completed', 'cancelled'];
        if (in_array($filter, $validFilters)) {
            $this->activeFilter = $filter;
            $this->resetPage();
        }
    }

    // ═══════════════════════════════════════════
    //  دالة البحث (updatedSearch)
    // ═══════════════════════════════════════════
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ═══════════════════════════════════════════
    //  دالة جلب البيانات (orders)
    // ═══════════════════════════════════════════
    #[Computed]
    public function orders()
    {
        return SpecialOrder::with(['driver', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                        ->orWhere('title', 'like', '%' . $this->search . '%')
                        ->orWhere('start_address', 'like', '%' . $this->search . '%')
                        ->orWhere('end_address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('driver', fn ($driverQuery) => $driverQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->activeFilter !== 'all', function ($q) {
                if ($this->activeFilter === 'in_progress') {
                    return $q->whereIn('status', ['in_progress', 'pending', 'searching_driver', 'paused']);
                }
                return $q->where('status', $this->activeFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    // ═══════════════════════════════════════════
    //  الطلب المحدد لتعيين سائق وقائمة السائقين
    // ═══════════════════════════════════════════
    #[Computed]
    public function selectedOrder()
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        return SpecialOrder::with(['user', 'driver'])->find($this->selectedOrderId);
    }

    #[Computed]
    public function viewingOrder()
    {
        if (! $this->viewingOrderId) {
            return null;
        }

        return SpecialOrder::with(['user', 'driver', 'creator', 'canceller'])->find($this->viewingOrderId);
    }

    #[Computed]
    public function drivers()
    {
        if (! $this->selectedOrderId) {
            return collect();
        }

        return Driver::where('is_banned', false)
            ->where('is_active', true)
            ->with('vehicle:id,type')
            ->when($this->onlineDriversOnly, fn ($q) => $q->where('is_online', true))
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

    // ═══════════════════════════════════════════
    //  إجراءات تعيين السائق
    // ═══════════════════════════════════════════
    public function openAssignDriverModal($orderId)
    {
        $order = SpecialOrder::find($orderId);
        if (! $order) {
            return;
        }

        $this->selectedOrderId = $orderId;
        $this->driverSearch = '';
        $this->onlineDriversOnly = false;
        $this->assignOrderPrice = $order->price;

        unset($this->selectedOrder, $this->drivers);

        $this->dispatch('open-modal', 'assign-special-driver-modal');
    }

    public function assignDriver(int $driverId, FirebaseService $firebaseService)
    {
        if (! $this->selectedOrderId) {
            return;
        }

        $order = null;
        $driver = Driver::find($driverId);

        if (! $driver) {
            return;
        }

        DB::transaction(function () use ($driverId, $driver, &$order) {
            $order = SpecialOrder::where('id', $this->selectedOrderId)->lockForUpdate()->first();

            if (! $order || $order->status === 'cancelled') {
                return;
            }

            $updateData = [
                'driver_id' => $driverId,
                'status'    => 'in_progress',
            ];

            if ($this->assignOrderPrice !== null && $this->assignOrderPrice !== '') {
                $updateData['price'] = (int) $this->assignOrderPrice;
            }

            $order->update($updateData);

            ActivityLog::log(
                'update',
                'SpecialOrder',
                "تم تعيين السائق #{$driverId} ({$driver->name}) للطلب الخاص #{$order->id}",
                $order->id
            );
        });

        // إرسال إشعار للسائق إذا توفر التوكن
        if ($order && $driver && ! empty($driver->device_token)) {
            try {
                $firebaseService->sendNotification(
                    $driver->device_token,
                    'طلب خاص جديد مسند إليك!',
                    "تم إسناد الطلب الخاص #{$order->id} ({$order->title}) إليك بنجاح.",
                    [
                        'new_request'   => 'true',
                        'new_message'   => 'false',
                        'special_order_id' => (string) $order->id,
                        'status'        => 'in_progress',
                        'start_address' => (string) ($order->start_address ?? ''),
                        'end_address'   => (string) ($order->end_address ?? ''),
                        'click_action'  => 'FLUTTER_NOTIFICATION_CLICK',
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Failed to send FCM to driver {$driver->id} for SpecialOrder: " . $e->getMessage());
            }
        }

        unset($this->stats, $this->orders, $this->selectedOrder);
        $this->dispatch('special-driver-assigned');
    }

    // ═══════════════════════════════════════════
    //  إجراءات إلغاء الطلب
    // ═══════════════════════════════════════════
    public function openCancelModal($orderId)
    {
        $this->cancellingOrderId = $orderId;
        $this->cancellationReason = '';
        $this->resetValidation();
        $this->dispatch('open-modal', 'cancel-special-order-modal');
    }

    public function cancelOrder()
    {
        $this->validate([
            'cancellationReason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'cancellationReason.required' => 'يرجى إدخال سبب إلغاء الطلب.',
            'cancellationReason.min'      => 'سبب الإلغاء يجب أن لا يقل عن 3 أحرف.',
        ]);

        $order = SpecialOrder::find($this->cancellingOrderId);
        if (! $order) {
            $this->dispatch('close-modal', 'cancel-special-order-modal');
            return;
        }

        $order->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $this->cancellationReason,
            'cancelled_by'        => auth()->id(),
        ]);

        ActivityLog::log(
            'update',
            'SpecialOrder',
            "تم إلغاء الطلب الخاص #{$order->id} - السبب: {$this->cancellationReason}",
            $order->id
        );

        $this->cancellingOrderId = null;
        $this->cancellationReason = '';
        unset($this->stats, $this->orders);

        $this->dispatch('close-modal', 'cancel-special-order-modal');
    }

    // ═══════════════════════════════════════════
    //  إجراءات إكمال الطلب
    // ═══════════════════════════════════════════
    public function completeOrder($orderId)
    {
        $order = SpecialOrder::find($orderId);
        if (! $order || $order->status === 'cancelled') {
            return;
        }

        $order->update([
            'status' => 'completed',
        ]);

        ActivityLog::log(
            'update',
            'SpecialOrder',
            "تم تحويل حالة الطلب الخاص #{$order->id} إلى مكتملة",
            $order->id
        );

        unset($this->stats, $this->orders);
    }

    // ═══════════════════════════════════════════
    //  إجراءات تعديل الطلب والتسعيرة
    // ═══════════════════════════════════════════
    public function openEditModal($orderId)
    {
        $order = SpecialOrder::with('user')->find($orderId);
        if (! $order) {
            return;
        }

        $this->editingOrderId = $order->id;
        $this->editTitle = $order->title ?? '';
        $this->editCustomerName = $order->customer_name ?? ($order->user?->name ?? '');
        $this->editCustomerPhone = $order->customer_phone ?? ($order->user?->phone ?? '');
        $this->editCustomerWhatsapp = $order->customer_whatsapp ?? ($order->user?->whatsapp_number ?? '');
        $this->editStartAddress = $order->start_address ?? '';
        $this->editEndAddress = $order->end_address ?? '';
        $this->editPrice = $order->price;
        $this->editDescription = $order->description ?? '';
        $this->editStatus = $order->status ?? 'pending';

        $this->resetValidation();
        $this->dispatch('open-modal', 'edit-special-order-modal');
    }

    public function updateOrder()
    {
        $validated = $this->validate([
            'editTitle'            => ['required', 'string', 'max:255'],
            'editCustomerName'     => ['nullable', 'string', 'max:255'],
            'editCustomerPhone'    => ['nullable', 'string', 'max:30'],
            'editCustomerWhatsapp' => ['nullable', 'string', 'max:30'],
            'editStartAddress'     => ['required', 'string', 'max:255'],
            'editEndAddress'       => ['required', 'string', 'max:255'],
            'editPrice'            => ['nullable', 'numeric', 'min:0'],
            'editDescription'      => ['nullable', 'string', 'max:1000'],
            'editStatus'           => ['required', 'in:pending,searching_driver,paused,in_progress,completed,cancelled'],
        ], [
            'editTitle.required'        => 'عنوان الطلب مطلوب.',
            'editStartAddress.required' => 'نقطة الانطلاق مطلوبة.',
            'editEndAddress.required'   => 'نقطة الوصول مطلوبة.',
            'editPrice.numeric'         => 'السعر يجب أن يكون رقماً صحيحاً.',
        ]);

        $order = SpecialOrder::find($this->editingOrderId);
        if (! $order) {
            $this->dispatch('close-modal', 'edit-special-order-modal');
            return;
        }

        $order->update([
            'title'             => $validated['editTitle'],
            'customer_name'     => $validated['editCustomerName'],
            'customer_phone'    => $validated['editCustomerPhone'],
            'customer_whatsapp' => $validated['editCustomerWhatsapp'],
            'start_address'     => $validated['editStartAddress'],
            'end_address'       => $validated['editEndAddress'],
            'price'             => $validated['editPrice'] !== '' && $validated['editPrice'] !== null ? (int) $validated['editPrice'] : null,
            'description'       => $validated['editDescription'],
            'status'            => $validated['editStatus'],
        ]);

        ActivityLog::log(
            'update',
            'SpecialOrder',
            "تم تحديث بيانات الطلب الخاص #{$order->id}",
            $order->id
        );

        $this->editingOrderId = null;
        unset($this->stats, $this->orders);

        $this->dispatch('close-modal', 'edit-special-order-modal');
    }

    // ═══════════════════════════════════════════
    //  نافذة عرض التفاصيل السريع
    // ═══════════════════════════════════════════
    public function openDetailsModal($orderId)
    {
        $this->viewingOrderId = $orderId;
        unset($this->viewingOrder);
        $this->dispatch('open-modal', 'view-special-order-modal');
    }

    // ═══════════════════════════════════════════
    //  محادثات الدعم المباشر مع العميل أو السائق
    // ═══════════════════════════════════════════
    public function startChatWithCustomer($orderId)
    {
        $order = SpecialOrder::find($orderId);
        if (! $order || ! $order->user_id) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $order->user_id, 'driver_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'clients']);
    }

    public function startChatWithDriver($orderId)
    {
        $order = SpecialOrder::find($orderId);
        if (! $order || ! $order->driver_id) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['driver_id' => $order->driver_id, 'user_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'drivers']);
    }

    public function clearSelectedOrder()
    {
        $this->selectedOrderId = null;
        $this->driverSearch = '';
        $this->onlineDriversOnly = false;
        $this->assignOrderPrice = null;
    }

    public function render()
    {
        return view('livewire.special-orders.index');
    }
}
