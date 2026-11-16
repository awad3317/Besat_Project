<?php

namespace App\Livewire\DiscountCodes;

use App\Models\DiscountCode;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UsageUsers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'custom-pagination';

    public int $couponId;
    public string $search = '';
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        $coupon = DiscountCode::findOrFail($this->couponId);
        $searchTerm = trim($this->search);

        return $coupon->users()
            ->withTrashed()
            ->select([
                'users.id',
                'users.name',
                'users.phone',
                'users.deleted_at',
            ])
            ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderByDesc('discount_code_user.created_at')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.discount-codes.usage-users');
    }
}
