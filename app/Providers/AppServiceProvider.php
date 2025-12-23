<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Livewire\Users\Index;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('drivers.index', \App\Livewire\drivers\Index::class);
        Livewire::component('users.index', \App\Livewire\Users\Index::class);
    }
}
