<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $activeFilter = 'all';
    public $search = '';
    public $perPage = 10;

    public $passwordModalUserId = null;
    public $passwordModalUserName = '';
    public $newPassword = '';
    public $newPasswordConfirmation = '';
    public $passwordSuccessMessage = '';
    
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'search'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'page' => ['except' => 1],
    ];
    
    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total' => User::where('type', 'user')->count(),
            'banned' => User::where('is_banned', true)->where('type', 'user')->count(),
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
        if($user->type == 'superAdmin') {
            return;
        }
        $user->is_banned = !$user->is_banned;
        $user->save();

        ActivityLog::log(
            'update',
            'User',
            $user->is_banned ? "تم حظر العميل {$user->name}" : "تم فك حظر العميل {$user->name}",
            $user->id
        );
    }

    public function openPasswordModal($userId): void
    {
        abort_unless(Auth::user()?->type === 'superAdmin', 403);

        $user = User::findOrFail($userId);
        if ($user->type === 'superAdmin') {
            return;
        }

        $this->resetValidation();
        $this->passwordSuccessMessage = '';
        $this->passwordModalUserId = $user->id;
        $this->passwordModalUserName = $user->name;
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';

        $this->dispatch('open-user-password-modal');
    }

    public function updatePassword(): void
    {
        abort_unless(Auth::user()?->type === 'superAdmin', 403);

        $this->validate([
            'newPassword' => ['required', 'string', 'min:8'],
            'newPasswordConfirmation' => ['required', 'same:newPassword'],
        ], [
            'newPassword.required' => 'كلمة المرور الجديدة مطلوبة.',
            'newPassword.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'newPasswordConfirmation.required' => 'تأكيد كلمة المرور مطلوب.',
            'newPasswordConfirmation.same' => 'تأكيد كلمة المرور غير مطابق.',
        ]);

        $user = User::findOrFail($this->passwordModalUserId);
        if ($user->type === 'superAdmin') {
            return;
        }

        $user->update([
            'password' => $this->newPassword,
        ]);

        ActivityLog::log('update', 'User', "تم تغيير كلمة مرور المستخدم {$user->name}", $user->id);

        $this->passwordSuccessMessage = "تم تغيير كلمة مرور {$user->name} بنجاح.";
        $this->reset(['passwordModalUserId', 'passwordModalUserName', 'newPassword', 'newPasswordConfirmation']);
        $this->dispatch('close-user-password-modal');
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
            ->where('type', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
    
    public function render()
    {
        return view('livewire.users.index');
    }
}