<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ConversationSessionService
{
    protected int $ttlMinutes = 30; // مدة الاحتفاظ بالجلسة
    protected int $maxHistory = 6;  // الاحتفاظ بآخر 6 رسائل فقط

    public function getHistory(string $phone): array
    {
        return Cache::get("chat_history:{$phone}", []);
    }

    public function addMessage(string $phone, string $role, string $content): void
    {
        $history = $this->getHistory($phone);
        $history[] = [
            'role' => $role, // 'user' أو 'assistant'
            'content' => $content
        ];

        if (count($history) > $this->maxHistory) {
            $history = array_slice($history, -$this->maxHistory);
        }

        Cache::put("chat_history:{$phone}", $history, now()->addMinutes($this->ttlMinutes));
    }

    public function clearHistory(string $phone): void
    {
        Cache::forget("chat_history:{$phone}");
    }
}