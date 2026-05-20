<?php

namespace App\Livewire\Banks;

use App\Models\Bank;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

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
    
    /**
     * حساب إحصائيات البنوك بشكل كاش تزامني سريع
     */
    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'   => Bank::count(),
            'stopped' => Bank::where('is_active', false)->count(),
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
    
    /**
     * تبديل حالة تفعيل البنك (نشط / متوقف) فورياً
     */
    public function toggleStatus($bankId)
    {
        $bank = Bank::findOrFail($bankId);
        $bank->is_active = !$bank->is_active;
        $bank->save();
        Cache::forget('active_banks_list');
        // إجبار الكاش على التحديث عند تغيير الحالة
        unset($this->stats);
    }
    
    /**
     * استعلام جلب البنوك مع محرك البحث المتقدم والتصفية
     */
    #[Computed]
    public function banks()
    {
        return Bank::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('account_name', 'like', '%' . $this->search . '%')
                      ->orWhere('account_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activeFilter === 'stopped', fn($q) => $q->where('is_active', false))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function render()
    {
        return view('livewire.banks.index');
    }
}