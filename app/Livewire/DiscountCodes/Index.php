<?php

namespace App\Livewire\DiscountCodes;

use App\Models\DiscountCode;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '', as: 'q')]
    public $search = '';

    #[Url(except: 'all')]
    public $filter = 'all';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filter'])) {
            $this->resetPage(); 
        }
    }
     public function applyFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'    => DiscountCode::count(),
            'active'   => DiscountCode::where('is_active', true)->count(),
            'inactive' => DiscountCode::where('is_active', false)->count(),
        ];
    }

    #[Computed]
    public function coupons()
    {
        return DiscountCode::query()
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter !== 'all', function ($query) {
                
                $query->where('is_active', $this->filter === 'active');
            })
            ->latest() 
            ->simplePaginate(9);
    }

    /**
     * عرض الـ View
     */
    public function render()
    {
        return view('livewire.discount-codes.index');
    }
}

