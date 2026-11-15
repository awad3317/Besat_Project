<?php

namespace App\Livewire\SpecialOrders;

use App\Models\Conversation;
use App\Models\ServiceBooking;
use App\Services\ActivityLog;
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

    // ── خصائص نافذة إلغاء الحجز ──
    public $cancellingOrderId = null;
    public $cancellationReason = '';

    // ── خصائص نافذة تعديل الحجز والتسعيرة ──
    public $editingOrderId = null;
    public $editServiceType = 'events';
    public $editClientName = '';
    public $editPassengerCount = 1;
    public $editVehiclesCount = 1;
    public bool $editWantsAc = false;
    public $editStartAddress = '';
    public $editEndAddress = '';
    public $editTotalPrice = null;
    public $editPaymentStatus = 'unpaid';
    public $editStatus = 'pending';
    public $editNotes = '';

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    // ═══════════════════════════════════════════
    //  دالة الإحصائيات حسب نوع الخدمة (service_type)
    // ═══════════════════════════════════════════
    #[Computed(cache: true)]
    public function stats()
    {
        $counts = ServiceBooking::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN service_type = 'events' THEN 1 ELSE 0 END) as events,
            SUM(CASE WHEN service_type = 'zafona' THEN 1 ELSE 0 END) as zafona,
            SUM(CASE WHEN service_type = 'rehla' THEN 1 ELSE 0 END) as rehla,
            SUM(CASE WHEN service_type = 'safety_airport' THEN 1 ELSE 0 END) as safety_airport,
            SUM(CASE WHEN service_type = 'dawam' THEN 1 ELSE 0 END) as dawam,
            SUM(CASE WHEN service_type = 'moshwarana' THEN 1 ELSE 0 END) as moshwarana
        ")->first();

        return [
            'total'          => (int) ($counts->total ?? 0),
            'events'         => (int) ($counts->events ?? 0),
            'zafona'         => (int) ($counts->zafona ?? 0),
            'rehla'          => (int) ($counts->rehla ?? 0),
            'safety_airport' => (int) ($counts->safety_airport ?? 0),
            'dawam'          => (int) ($counts->dawam ?? 0),
            'moshwarana'     => (int) ($counts->moshwarana ?? 0),
        ];
    }

    // ═══════════════════════════════════════════
    //  دالة الفلترة حسب نوع الخدمة (applyFilter)
    // ═══════════════════════════════════════════
    public function applyFilter($filter)
    {
        $validFilters = array_merge(['all'], array_keys(ServiceBooking::serviceTypes()));
        if (in_array($filter, $validFilters, true)) {
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
        return ServiceBooking::with([
                'user' => fn ($q) => $q->withTrashed(),
                'vehicle',
                'stops',
            ])
            ->withCount('stops')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', $search)
                        ->orWhere('client_name', 'like', $search)
                        ->orWhere('service_type', 'like', $search)
                        ->orWhere('start_address', 'like', $search)
                        ->orWhere('end_address', 'like', $search)
                        ->orWhere('notes', 'like', $search)
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->withTrashed()
                            ->where(function ($sub) use ($search) {
                                $sub->where('name', 'like', $search)
                                    ->orWhere('phone', 'like', $search);
                            }))
                        ->orWhereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery
                            ->where('type', 'like', $search));
                });
            })
            ->when($this->activeFilter !== 'all', function ($q) {
                return $q->where('service_type', $this->activeFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    // ═══════════════════════════════════════════
    //  إجراءات اعتماد أو إكمال الحجز
    // ═══════════════════════════════════════════
    public function approveOrder($orderId)
    {
        $order = ServiceBooking::find($orderId);
        if (! $order || in_array($order->status, ['cancelled', 'completed'], true)) {
            return;
        }

        $order->update([
            'status' => 'approved',
        ]);

        ActivityLog::log(
            'update',
            'ServiceBooking',
            "تمت الموافقة على حجز الخدمة #{$order->id} ({$order->service_type_text})",
            $order->id
        );

        unset($this->stats, $this->orders);
    }

    public function completeOrder($orderId)
    {
        $order = ServiceBooking::find($orderId);
        if (! $order || $order->status === 'cancelled') {
            return;
        }

        $order->update([
            'status' => 'completed',
        ]);

        ActivityLog::log(
            'update',
            'ServiceBooking',
            "تم تحويل حالة حجز الخدمة #{$order->id} إلى مكتملة",
            $order->id
        );

        unset($this->stats, $this->orders);
    }

    // ═══════════════════════════════════════════
    //  إجراءات إلغاء الحجز
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
            'cancellationReason.required' => 'يرجى إدخال سبب إلغاء الحجز.',
            'cancellationReason.min'      => 'سبب الإلغاء يجب أن لا يقل عن 3 أحرف.',
        ]);

        $order = ServiceBooking::find($this->cancellingOrderId);
        if (! $order) {
            $this->dispatch('close-modal', 'cancel-special-order-modal');
            return;
        }

        $serviceDetails = is_array($order->service_details) ? $order->service_details : [];
        $serviceDetails['cancellation_reason'] = $this->cancellationReason;
        $serviceDetails['cancelled_by'] = auth()->id();

        $order->update([
            'status'          => 'cancelled',
            'service_details' => $serviceDetails,
        ]);

        ActivityLog::log(
            'update',
            'ServiceBooking',
            "تم إلغاء حجز الخدمة #{$order->id} - السبب: {$this->cancellationReason}",
            $order->id
        );

        $this->cancellingOrderId = null;
        $this->cancellationReason = '';
        unset($this->stats, $this->orders);

        $this->dispatch('close-modal', 'cancel-special-order-modal');
    }

    // ═══════════════════════════════════════════
    //  إجراءات تعديل الحجز والتسعيرة
    // ═══════════════════════════════════════════
    public function openEditModal($orderId)
    {
        $order = ServiceBooking::with(['user' => fn ($q) => $q->withTrashed()])->find($orderId);
        if (! $order) {
            return;
        }

        $this->editingOrderId = $order->id;
        $this->editServiceType = $order->service_type ?? 'events';
        $this->editClientName = $order->client_name ?? ($order->user?->name ?? '');
        $this->editPassengerCount = $order->passenger_count ?? 1;
        $this->editVehiclesCount = $order->vehicles_count ?? 1;
        $this->editWantsAc = (bool) $order->wants_ac;
        $this->editStartAddress = $order->start_address ?? '';
        $this->editEndAddress = $order->end_address ?? '';
        $this->editTotalPrice = $order->total_price;
        $this->editPaymentStatus = $order->payment_status ?? 'unpaid';
        $this->editStatus = $order->status ?? 'pending';
        $this->editNotes = $order->notes ?? '';

        $this->resetValidation();
        $this->dispatch('open-modal', 'edit-special-order-modal');
    }

    public function updateOrder()
    {
        $validated = $this->validate([
            'editServiceType'    => ['required', 'in:events,zafona,rehla,safety_airport,dawam,moshwarana'],
            'editClientName'     => ['required', 'string', 'max:255'],
            'editPassengerCount' => ['required', 'integer', 'min:1'],
            'editVehiclesCount'  => ['required', 'integer', 'min:1'],
            'editWantsAc'        => ['boolean'],
            'editStartAddress'   => ['required', 'string', 'max:255'],
            'editEndAddress'     => ['required', 'string', 'max:255'],
            'editTotalPrice'     => ['nullable', 'numeric', 'min:0'],
            'editPaymentStatus'  => ['required', 'in:unpaid,partially_paid,paid'],
            'editStatus'         => ['required', 'in:pending,approved,rejected,in_progress,completed,cancelled'],
            'editNotes'          => ['nullable', 'string', 'max:1000'],
        ], [
            'editClientName.required'   => 'اسم العميل مطلوب.',
            'editStartAddress.required' => 'نقطة الانطلاق مطلوبة.',
            'editEndAddress.required'   => 'نقطة الوصول مطلوبة.',
            'editTotalPrice.numeric'    => 'السعر يجب أن يكون رقماً صحيحاً.',
        ]);

        $order = ServiceBooking::find($this->editingOrderId);
        if (! $order) {
            $this->dispatch('close-modal', 'edit-special-order-modal');
            return;
        }

        $order->update([
            'service_type'    => $validated['editServiceType'],
            'client_name'     => $validated['editClientName'],
            'passenger_count' => (int) $validated['editPassengerCount'],
            'vehicles_count'  => (int) $validated['editVehiclesCount'],
            'wants_ac'        => (bool) $validated['editWantsAc'],
            'start_address'   => $validated['editStartAddress'],
            'end_address'     => $validated['editEndAddress'],
            'total_price'     => $validated['editTotalPrice'] !== '' && $validated['editTotalPrice'] !== null ? (float) $validated['editTotalPrice'] : null,
            'payment_status'  => $validated['editPaymentStatus'],
            'status'          => $validated['editStatus'],
            'notes'           => $validated['editNotes'],
        ]);

        ActivityLog::log(
            'update',
            'ServiceBooking',
            "تم تحديث بيانات حجز الخدمة #{$order->id}",
            $order->id
        );

        $this->editingOrderId = null;
        unset($this->stats, $this->orders);

        $this->dispatch('close-modal', 'edit-special-order-modal');
    }

    // ═══════════════════════════════════════════
    //  محادثات الدعم المباشر مع العميل
    // ═══════════════════════════════════════════
    public function startChatWithCustomer($orderId)
    {
        $order = ServiceBooking::find($orderId);
        if (! $order || ! $order->user_id) {
            return;
        }

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $order->user_id, 'driver_id' => null, 'type' => 'support'],
            ['status' => 'open']
        );

        return redirect()->route('chats.index', ['c' => $conversation->id, 'tab' => 'clients']);
    }

    public function render()
    {
        return view('livewire.special-orders.index');
    }
}
