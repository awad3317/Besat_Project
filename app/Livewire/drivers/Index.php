<?php

namespace App\Livewire\Drivers;

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
    
    #[Computed] 
    public function stats()
    {
        return [
            'total' => Driver::count(),
            'connected' => Driver::where('is_online', true)->count(),
            'banned' => Driver::where('is_banned', true)->count(),
            'active' => Driver::where('is_active', true)->count(),
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
                'id', 'name', 'phone', 'whatsapp_number',
                'driver_image', 'is_online', 'is_banned',
                'is_active', 'created_at','vehicle_id'
            ])
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