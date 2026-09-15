<?php

namespace App\Livewire\Drivers;

use App\Models\Driver;
use App\Models\DriverSettlement;
use App\Models\Request as TripRequest;
use App\Services\DriverSettlementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Settlements extends Component
{
    use WithPagination;

    public $driverId;
    public $showSettlementModal = false;
    public $unsettledStats = [
        'trips_count' => 0,
        'driver_owes_app' => 0,
        'app_owes_driver' => 0,
        'net_balance' => 0,
    ];
    public $total_trips = 0;

    public function mount($driverId)
    {
        $this->driverId = $driverId;
        $this->calculateUnsettledStats();
    }

    public function calculateUnsettledStats()
    {
        $requests = TripRequest::where('driver_id', $this->driverId)
            ->where('status', 'completed')
            ->whereNull('driver_settlement_id')
            ->get();
        $total_trips = TripRequest::where('driver_id', $this->driverId)->count();
        $tripsCount = $requests->count();
        $driverOwesApp = 0;
        $appOwesDriver = 0;



        foreach ($requests as $request) {
            if ($request->payment_method === 'cash') {
                $driverOwesApp += ($request->app_commission_amount - $request->discount_amount);
            } elseif (in_array($request->payment_method, ['wallet', 'digital_payment'])) {
                $appOwesDriver += ($request->original_price - $request->app_commission_amount);
            }
        }

        $netBalance = $appOwesDriver - $driverOwesApp;

        $this->unsettledStats = [
            'trips_count' => $tripsCount,
            'driver_owes_app' => $driverOwesApp,
            'app_owes_driver' => $appOwesDriver,
            'net_balance' => $netBalance,
        ];
        $this->total_trips = $total_trips;
    }

    #[Computed]
    public function driver()
    {
        return Driver::findOrFail($this->driverId);
    }

    #[Computed]
    public function stats()
    {
        try {
            $service = app(DriverSettlementService::class);
            return $service->getDriverStats($this->driverId);
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function settlements()
    {
        return DriverSettlement::with('admin')
            ->where('driver_id', $this->driverId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function openSettlementModal()
    {
        $this->calculateUnsettledStats();

        if ($this->unsettledStats['trips_count'] == 0) {
            $this->dispatch('notify', ['message' => 'لا توجد رحلات غير مصفاة لهذا السائق.', 'type' => 'error']);
            return;
        }

        $this->showSettlementModal = true;
    }

    public function closeSettlementModal()
    {
        $this->showSettlementModal = false;
    }

    public function confirmSettlement()
    {
        try {
            DB::transaction(function () {
                $requests = TripRequest::where('driver_id', $this->driverId)
                    ->where('status', 'completed')
                    ->whereNull('driver_settlement_id')
                    ->lockForUpdate()
                    ->get();

                if ($requests->count() === 0) {
                    throw new \Exception('لا توجد رحلات غير مصفاة.');
                }

                $driverOwesApp = 0;
                $appOwesDriver = 0;

                foreach ($requests as $request) {
                    if ($request->payment_method === 'cash') {
                        $driverOwesApp += ($request->app_commission_amount - $request->discount_amount);
                    } elseif (in_array($request->payment_method, ['wallet', 'digital_payment'])) {
                        $appOwesDriver += ($request->original_price - $request->app_commission_amount);
                    }
                }

                $netBalance = $appOwesDriver - $driverOwesApp;
                $settlementAction = $netBalance < 0 ? 'driver_pays_app' : 'app_pays_driver';

                $settlement = DriverSettlement::create([
                    'driver_id' => $this->driverId,
                    'admin_id' => Auth::id(),
                    'trips_count' => $requests->count(),
                    'driver_owes_app' => $driverOwesApp,
                    'app_owes_driver' => $appOwesDriver,
                    'net_balance' => $netBalance,
                    'settlement_action' => $settlementAction,
                ]);

                TripRequest::whereIn('id', $requests->pluck('id'))->update([
                    'driver_settlement_id' => $settlement->id
                ]);
            });

            $this->closeSettlementModal();
            $this->calculateUnsettledStats();
            $this->dispatch('notify', ['message' => 'تمت تصفية الحساب بنجاح', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.drivers.settlements');
    }
}
