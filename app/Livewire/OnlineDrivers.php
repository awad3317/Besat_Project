<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Computed;

class OnlineDrivers extends Component
{
    #[Computed]
    public function getOnlineDrivers()  {
        return Driver::where('is_online',true)->where('is_active',true)->get();
    }
    public function render()
    {
        return view('livewire.online-drivers');
    }
}
