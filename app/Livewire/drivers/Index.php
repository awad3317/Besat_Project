<?php

namespace App\Livewire\drivers;

use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;
    
    public $totalDrivers = 0;
    public $connectedDrivers = 0;
    public $bannedDrivers = 0;
    public $activeDrivers = 0;
    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'activeFilter' => ['except' => 'all'],
        'page' => ['except' => 1],
    ];
    
    public function mount()
    {
        $this->calculateStats();
    }
    
    public function filterByAll()
    {
        $this->activeFilter = 'all';
        $this->resetPage();
    }
    
    public function filterByBanned()
    {
        $this->activeFilter = 'banned';
        $this->resetPage();
    }
    
    public function filterByConnected()
    {
        $this->activeFilter = 'connected';
        $this->resetPage();
    }

    public function filterByActive()
    {
        $this->activeFilter = 'active';
        $this->resetPage();
    }
    
    #[On('stats-need-update')]
    public function calculateStats()
    {
        $this->totalDrivers = Driver::count();
        $this->connectedDrivers = Driver::where('is_online', true)->count();
        $this->bannedDrivers = Driver::where('is_banned', true)->count();
        $this->activeDrivers = Driver::where('is_active', true)->count();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function toggleBan($driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $driver->update(['is_banned' => !$driver->is_banned]);
        $this->dispatch('page-reload');
    }
    
    public function getDriversProperty()
    {
        $query = Driver::with(['vehicle', 'requests'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activeFilter === 'connected', function ($query) {
                $query->where('is_online', true);
            })
            ->when($this->activeFilter === 'banned', function ($query) {
                $query->where('is_banned', true);
            })
            ->when($this->activeFilter === 'active', function ($query) {
                $query->where('is_active', true);
            });

        return $query->paginate($this->perPage);
    }
    
    public function render()
    {
        return view('livewire.drivers.index', [
            'drivers' => $this->drivers,
        ]);
    }
}