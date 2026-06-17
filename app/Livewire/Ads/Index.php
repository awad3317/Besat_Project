<?php

namespace App\Livewire\Ads;

use App\Models\ads;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    #[Url(except: 'all')]
    public $filter = 'all';

    public function applyFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'    => ads::count(),
            'active'   => ads::where('is_active', true)->count(),
            'inactive' => ads::where('is_active', false)->count(),
        ];
    }

    #[Computed]
    public function ads()
    {
        return ads::query()
            ->when($this->filter !== 'all', function ($query) {
                $query->where('is_active', $this->filter === 'active');
            })
            ->latest()
            ->simplePaginate(12);
    }

    public function toggleActive($id)
    {
        $ad = ads::findOrFail($id);
        $ad->update(['is_active' => !$ad->is_active]);

        // Clear mobile cache
        app(\App\Repositories\AdRepository::class)->clearAdsCache();

        unset($this->stats, $this->ads);
    }

    public function render()
    {
        return view('livewire.ads.index');
    }
}
