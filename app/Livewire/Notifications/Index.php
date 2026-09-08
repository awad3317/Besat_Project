<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Repositories\UserRepository;
use App\Repositories\DriverRepository;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public $target = 'all';
    public $title = '';
    public $body = '';

    protected $rules = [
        'target' => 'required|in:all,users,drivers',
        'title' => 'required|string|max:255',
        'body' => 'required|string|max:1000',
    ];

    public function send(UserRepository $userRepository, DriverRepository $driverRepository, FirebaseService $firebaseService)
    {
        $this->validate();

        $tokens = [];

        if ($this->target === 'all' || $this->target === 'users') {
            $userTokens = $userRepository->getAllFcmTokens();
            $tokens = array_merge($tokens, $userTokens);
        }

        if ($this->target === 'all' || $this->target === 'drivers') {
            $driverTokens = $driverRepository->getAllDeviceTokens();
            $tokens = array_merge($tokens, $driverTokens);
        }

        // Remove empty or duplicate tokens
        $tokens = array_unique(array_filter($tokens));

        if (empty($tokens)) {
            session()->flash('error', 'لم يتم العثور على أي مستخدمين أو سائقين لإرسال الإشعار إليهم.');
            $this->dispatch('notify', ['type' => 'error', 'message' => 'لم يتم العثور على أي مستخدمين أو سائقين لإرسال الإشعار إليهم.']);
            return;
        }

        try {
            $firebaseService->sendMulticast($tokens, $this->title, $this->body);
            
            session()->flash('success', 'تم إرسال الإشعارات بنجاح (' . count($tokens) . ' مستلم).');
            $this->dispatch('notify', ['type' => 'success', 'message' => 'تم إرسال الإشعارات بنجاح (' . count($tokens) . ' مستلم).']);
            
            $this->reset(['title', 'body']);
        } catch (\Exception $e) {
            Log::error('Broadcast Notification Error: ' . $e->getMessage());
            session()->flash('error', 'حدث خطأ أثناء الإرسال: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'حدث خطأ أثناء الإرسال: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.notifications.index');
    }
}
