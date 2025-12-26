<?php

namespace App\Livewire\SpecialOrders;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Vehicle;

class Create extends Component
{

    #[Computed]
    public function vehicles()
    {
        return Vehicle::get();
    }
    public function render()
    {
        return view('livewire.special-orders.create');
    }
}
