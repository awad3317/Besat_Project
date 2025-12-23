<?php

namespace App\Livewire\Users;

use App\Models\User;
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
            'total' => User::count(),
            'banned' => User::where('is_banned', true)->count(),
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
    
    public function toggleBan($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_banned = !$user->is_banned;
        $user->save();
    }
    
    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('whatsapp_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->activeFilter === 'banned', fn($q) => $q->where('is_banned', true))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function render()
    {
        return view('livewire.users.index');
    }
}