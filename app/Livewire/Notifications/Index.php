<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\UserDevice;
use App\Models\Driver;
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
        'body' => 'required|string',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    #[Computed]
    public function usersCount()
    {
        return UserDevice::whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->distinct('device_token')
            ->count('device_token');
    }

    #[Computed]
    public function driversCount()
    {
        return Driver::whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->distinct('device_token')
            ->count('device_token');
    }

    #[Computed]
    public function allCount()
    {
        return $this->usersCount() + $this->driversCount();
    }

    public function send(FirebaseService $firebaseService)
    {
        $this->validate();

        try {
            $deviceTokens = [];

            if ($this->target === 'users' || $this->target === 'all') {
                $userTokens = UserDevice::whereNotNull('device_token')
                    ->where('device_token', '!=', '')
                    ->pluck('device_token')
                    ->toArray();
                $deviceTokens = array_merge($deviceTokens, $userTokens);
            }

            if ($this->target === 'drivers' || $this->target === 'all') {
                $driverTokens = Driver::whereNotNull('device_token')
                    ->where('device_token', '!=', '')
                    ->pluck('device_token')
                    ->toArray();
                $deviceTokens = array_merge($deviceTokens, $driverTokens);
            }

            $deviceTokens = array_unique($deviceTokens);

            if (empty($deviceTokens)) {
                return redirect()->back()->with([
                    'error' => 'لا توجد أجهزة مسجلة لإرسال الإشعار إليها في الفئة المحددة.',
                ]);
            }

            $data = [
                'type' => 'general_notification',
                'timestamp' => now()->toIso8601String(),
            ];

            $successCount = 0;
            $failCount = 0;

            foreach ($deviceTokens as $token) {
                try {
                    $firebaseService->sendNotification($token, $this->title, $this->body, $data);
                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                    Log::warning("Failed to send notification to token: {$token}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Reset form
            $this->reset(['title', 'body']);

            return redirect()->back()->with([
                'success' => "تم إرسال الإشعار بنجاح إلى {$successCount} جهاز." 
                    . ($failCount > 0 ? " (فشل: {$failCount})" : ''),
            ]);

        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());

            return redirect()->back()->with([
                'error' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notifications.index');
    }
}
