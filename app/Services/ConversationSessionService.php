<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ConversationSessionService
{
    protected int $ttlMinutes = 30;
    protected int $maxHistory = 6;

    public function getHistory(string $phone): array
    {
        return Cache::get("chat_history:{$phone}", []);
    }

    public function addMessage(string $phone, string $role, string $content): void
    {
        $history = $this->getHistory($phone);
        $history[] = [
            'role' => $role,
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

    // --- إضافات التحويل للدعم الفني (Human Support Handoff) ---

    // 1. الفحص هل العميل محول للدعم الفني حالياً؟
    public function isHumanSupportActive(string $phone): bool
    {
        return Cache::has("human_support:{$phone}");
    }

    // 2. تفعيل التحويل للدعم الفني وإيقاف الـ AI (لمدة 24 ساعة مثلاً)
    public function enableHumanSupport(string $phone, int $hours = 24): void
    {
        Cache::put("human_support:{$phone}", true, now()->addHours($hours));
    }

    // 3. إعادة تفعيل الـ AI للعميل (إذا انتهى الدعم الفني)
    public function disableHumanSupport(string $phone): void
    {
        Cache::forget("human_support:{$phone}");
    }
}