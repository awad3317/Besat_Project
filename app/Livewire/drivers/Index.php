<?php

namespace App\Livewire\drivers;

use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'search'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];

    // تم إزالة الكاش لضمان تحديث الأرقام لحظياً مع أي تغيير في الطلبات
    #[Computed]
    public function stats()
    {
        // استخدام نفس الاستعلام الفرعي الموجود في دالة drivers لضمان تطابق الأرقام
        $balanceSubquery = "(
            SELECT 
                COALESCE(SUM(CASE WHEN r.payment_method IN ('wallet', 'digital_payment') THEN (r.original_price - r.app_commission_amount) ELSE 0 END), 0) 
                - 
                COALESCE(SUM(CASE WHEN r.payment_method = 'cash' THEN (r.app_commission_amount - r.discount_amount) ELSE 0 END), 0)
            FROM requests r
            WHERE r.driver_id = drivers.id
              AND r.status = 'completed'
              AND r.driver_settlement_id IS NULL
        )";

        $highestIndebted = Driver::select('id', 'name')
            ->selectRaw("$balanceSubquery as net_balance")
            ->havingRaw('net_balance < 0')
            ->orderBy('net_balance', 'asc') // الترتيب تصاعدياً لجلب أكبر رقم بالسالب
            ->first();

        $highestDues = Driver::select('id', 'name')
            ->selectRaw("$balanceSubquery as net_balance")
            ->havingRaw('net_balance > 0')
            ->orderBy('net_balance', 'desc') // الترتيب تنازلياً لجلب أكبر رقم بالموجب
            ->first();

        return [
            'total' => Driver::count(),
            'connected' => Driver::where('is_online', true)->count(),
            'banned' => Driver::where('is_banned', true)->count(),
            'active' => Driver::where('is_active', true)->count(),
            'highest_indebted_name' => $highestIndebted ? $highestIndebted->name : 'لا يوجد',
            'highest_indebted_amount' => $highestIndebted ? abs($highestIndebted->net_balance) : 0,
            'highest_dues_name' => $highestDues ? $highestDues->name : 'لا يوجد',
            'highest_dues_amount' => $highestDues ? $highestDues->net_balance : 0,
        ];
    }

    public function applyFilter($filter)
    {
        $this->activeFilter = $filter;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('toggle-active')]
    public function toggleActive($driverId)
    {
        Driver::where('id', $driverId)->update([
            'is_active' => DB::raw('NOT is_active')
        ]);
    }

    public function toggleBan($driverId)
    {
        Driver::where('id', $driverId)->update([
            'is_banned' => DB::raw('NOT is_banned')
        ]);
    }

    #[Computed]
    public function drivers()
    {
        return Driver::query()
            ->with([
                'vehicle:id,type',
                'requests:id,driver_id'
            ])
            ->select([
                'id',
                'name',
                'phone',
                'whatsapp_number',
                'driver_image',
                'is_online',
                'is_banned',
                'is_active',
                'created_at',
                'vehicle_id'
            ])
            ->selectRaw("
                (
                    SELECT 
                        COALESCE(SUM(CASE WHEN r.payment_method IN ('wallet', 'digital_payment') THEN (r.original_price - r.app_commission_amount) ELSE 0 END), 0) 
                        - 
                        COALESCE(SUM(CASE WHEN r.payment_method = 'cash' THEN (r.app_commission_amount - r.discount_amount) ELSE 0 END), 0)
                    FROM requests r
                    WHERE r.driver_id = drivers.id
                      AND r.status = 'completed'
                      AND r.driver_settlement_id IS NULL
                ) as net_balance
            ")
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activeFilter === 'connected', fn($q) => $q->where('is_online', true))
            ->when($this->activeFilter === 'banned', fn($q) => $q->where('is_banned', true))
            ->when($this->activeFilter === 'active', fn($q) => $q->where('is_active', true))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.drivers.index');
    }
}