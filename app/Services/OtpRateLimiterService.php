<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OtpRateLimiterService
{
    /**
     * توحيد صيغة رقم الهاتف لمنع التحايل عبر تغيير الصيغة (+967 / 00967 / 967 / 0)
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (empty($digits)) {
            return null;
        }

        if (str_starts_with($digits, '00967')) {
            $digits = substr($digits, 5);
        } elseif (str_starts_with($digits, '967') && strlen($digits) >= 12) {
            $digits = substr($digits, 3);
        }

        $digits = ltrim($digits, '0');

        return $digits !== '' ? $digits : null;
    }

    /**
     * استخراج معرّف الجهاز من الهيدر (X-Device-Id)
     */
    public function extractDeviceId(Request $request): ?string
    {
        $headerName = (string) config('otp.device_id_header', 'X-Device-Id');
        $raw = trim((string) $request->header($headerName, ''));

        if ($raw === '' || strlen($raw) < 6 || strlen($raw) > 128) {
            return null;
        }

        return $raw;
    }

    /**
     * فحص جميع طبقات الـ Rate Limiting (Device ID + IP + Phone) قبل إرسال OTP
     */
    public function check(Request $request, ?string $rawPhone = null): array
    {
        $requireDeviceId = (bool) config('otp.require_device_id', false);
        $deviceId = $this->extractDeviceId($request);

        if ($requireDeviceId && $deviceId === null) {
            return [
                'allowed'             => false,
                'status'              => 400,
                'message'             => 'معرّف الجهاز مطلوب لإتمام هذه العملية، يرجى تحديث التطبيق والمحاولة مجدداً.',
                'retry_after_seconds' => 0,
                'limit_type'          => 'device_id_required',
                'attempt_number'      => null,
            ];
        }

        $ip = (string) ($request->ip() ?: 'unknown');
        $normalizedPhone = $this->normalizePhone($rawPhone);
        $now = now()->timestamp;

        // 1. طبقة معرّف الجهاز (X-Device-Id) — تعمل تلقائياً متى ما أرسل التطبيق الهيدر
        if ($deviceId !== null) {
            $deviceHash = sha1($deviceId);
            $deviceCheck = $this->checkScope(
                scope: 'device',
                scopeId: $deviceHash,
                normalizedPhone: $normalizedPhone,
                maxPerHour: (int) config('otp.device_max_per_hour', 5),
                maxPerDay: (int) config('otp.device_max_per_day', 10),
                maxUniquePhones: (int) config('otp.device_max_unique_phones', 3),
                now: $now
            );

            if (! $deviceCheck['allowed']) {
                return $deviceCheck;
            }
        }

        // 2. طبقة عنوان الشبكة (IP) — على الجوال بالكامل (تعمل تلقائياً فوراً)
        $ipHash = sha1($ip);
        $ipMaxUniquePhones = $deviceId === null
            ? (int) config('otp.device_max_unique_phones', 3)
            : (int) config('otp.ip_max_unique_phones', 4);

        $ipCheck = $this->checkScope(
            scope: 'ip',
            scopeId: $ipHash,
            normalizedPhone: $normalizedPhone,
            maxPerHour: (int) config('otp.ip_max_per_hour', 6),
            maxPerDay: (int) config('otp.ip_max_per_day', 12),
            maxUniquePhones: $ipMaxUniquePhones,
            now: $now
        );

        if (! $ipCheck['allowed']) {
            return $ipCheck;
        }

        // 3. طبقة رقم الهاتف الموحّد (Phone) — تمنع إغراق نفس الرقم حتى لو تغيّر الـ IP والجهاز
        if ($normalizedPhone !== null) {
            $phoneCheck = $this->checkScope(
                scope: 'phone',
                scopeId: sha1($normalizedPhone),
                normalizedPhone: null,
                maxPerHour: (int) config('otp.phone_max_per_hour', 3),
                maxPerDay: (int) config('otp.phone_max_per_day', 5),
                maxUniquePhones: null,
                now: $now
            );

            if (! $phoneCheck['allowed']) {
                return $phoneCheck;
            }
        }

        return ['allowed' => true];
    }

    /**
     * تسجيل محاولة إرسال OTP وتفعيل الانتظار التصاعدي (1 د -> 2 د -> 4 د -> 8 د ...)
     */
    public function hit(Request $request, ?string $rawPhone = null): array
    {
        $deviceId = $this->extractDeviceId($request);
        $ip = (string) ($request->ip() ?: 'unknown');
        $normalizedPhone = $this->normalizePhone($rawPhone);
        $now = now()->timestamp;

        $ipResult = $this->hitScope('ip', sha1($ip), $normalizedPhone, $now);

        $deviceResult = null;
        if ($deviceId !== null) {
            $deviceResult = $this->hitScope('device', sha1($deviceId), $normalizedPhone, $now);
        }

        if ($normalizedPhone !== null) {
            $this->hitScope('phone', sha1($normalizedPhone), null, $now);
        }

        return $deviceResult ?? $ipResult;
    }

    /**
     * تصفير عدّاد الانتظار التصاعدي فور نجاح المستخدم في التحقق من الكود
     * (مع الإبقاء على عدّادات الساعة واليوم والأرقام الفريدة لمنع الاستغلال)
     */
    public function clearCooldownOnSuccess(Request $request, ?string $rawPhone = null): void
    {
        $deviceId = $this->extractDeviceId($request);
        $ip = (string) ($request->ip() ?: 'unknown');
        $normalizedPhone = $this->normalizePhone($rawPhone);

        $this->clearBackoffKeys('ip', sha1($ip));

        if ($deviceId !== null) {
            $this->clearBackoffKeys('device', sha1($deviceId));
        }

        if ($normalizedPhone !== null) {
            $this->clearBackoffKeys('phone', sha1($normalizedPhone));
        }
    }

    /**
     * فحص نطاق واحد (device أو ip أو phone)
     */
    protected function checkScope(
        string $scope,
        string $scopeId,
        ?string $normalizedPhone,
        int $maxPerHour,
        int $maxPerDay,
        ?int $maxUniquePhones,
        int $now
    ): array {
        // أ) فحص فترة الانتظار التصاعدي الحالية (Cooldown)
        $cooldownKey = $this->key($scope, 'cooldown', $scopeId);
        $cooldownData = Cache::get($cooldownKey);

        if (is_array($cooldownData) && ($cooldownData['expires_at'] ?? 0) > $now) {
            $retryAfter = (int) ($cooldownData['expires_at'] - $now);
            $attemptNumber = (int) ($cooldownData['attempt'] ?? 1);
            $durationText = $this->formatDurationArabic($retryAfter);

            return [
                'allowed'             => false,
                'status'              => 429,
                'message'             => "يرجى الانتظار {$durationText} قبل طلب رمز تحقق جديد.",
                'retry_after_seconds' => $retryAfter,
                'limit_type'          => "{$scope}_cooldown",
                'attempt_number'      => $attemptNumber,
            ];
        }

        // ب) فحص الحد الساعي
        $hourData = Cache::get($this->key($scope, 'hour', $scopeId));
        if (is_array($hourData) && ($hourData['expires_at'] ?? 0) > $now && ($hourData['count'] ?? 0) >= $maxPerHour) {
            $retryAfter = (int) ($hourData['expires_at'] - $now);
            $durationText = $this->formatDurationArabic($retryAfter);

            return [
                'allowed'             => false,
                'status'              => 429,
                'message'             => "تم تجاوز الحد الأقصى لطلبات رمز التحقق خلال الساعة. يرجى المحاولة بعد {$durationText}.",
                'retry_after_seconds' => $retryAfter,
                'limit_type'          => "{$scope}_hourly_limit",
                'attempt_number'      => (int) $hourData['count'],
            ];
        }

        // ج) فحص الحد اليومي
        $dayData = Cache::get($this->key($scope, 'day', $scopeId));
        if (is_array($dayData) && ($dayData['expires_at'] ?? 0) > $now && ($dayData['count'] ?? 0) >= $maxPerDay) {
            $retryAfter = (int) ($dayData['expires_at'] - $now);
            $durationText = $this->formatDurationArabic($retryAfter);

            return [
                'allowed'             => false,
                'status'              => 429,
                'message'             => "تم تجاوز الحد اليومي المسموح لطلبات رمز التحقق. يرجى المحاولة بعد {$durationText} أو التواصل مع الدعم الفني.",
                'retry_after_seconds' => $retryAfter,
                'limit_type'          => "{$scope}_daily_limit",
                'attempt_number'      => (int) $dayData['count'],
            ];
        }

        // د) فحص عدد الأرقام العشوائية المختلفة (Unique Phones) لنفس الجهاز أو الـ IP
        if ($maxUniquePhones !== null && $normalizedPhone !== null) {
            $phonesData = Cache::get($this->key($scope, 'phones', $scopeId));
            if (is_array($phonesData) && ($phonesData['expires_at'] ?? 0) > $now) {
                $phones = $phonesData['phones'] ?? [];
                if (! in_array($normalizedPhone, $phones, true) && count($phones) >= $maxUniquePhones) {
                    $retryAfter = (int) ($phonesData['expires_at'] - $now);
                    $durationText = $this->formatDurationArabic($retryAfter);

                    return [
                        'allowed'             => false,
                        'status'              => 429,
                        'message'             => "تم تجاوز الحد الأقصى لعدد الأرقام المختلفة المسموح تجربتها من هذا الجهاز. يرجى المحاولة بعد {$durationText} أو التواصل مع الدعم الفني.",
                        'retry_after_seconds' => $retryAfter,
                        'limit_type'          => "{$scope}_unique_phones_limit",
                        'attempt_number'      => count($phones),
                    ];
                }
            }
        }

        return ['allowed' => true];
    }

    /**
     * تسجيل محاولة جديدة وتحديث مؤقت الانتظار التصاعدي للنطاق
     */
    protected function hitScope(string $scope, string $scopeId, ?string $normalizedPhone, int $now): array
    {
        $backoffLadder = config('otp.backoff_seconds', [60, 120, 240, 480, 960, 1800]);
        if (empty($backoffLadder)) {
            $backoffLadder = [60, 120, 240, 480, 960, 1800];
        }

        $resetSeconds = max(3600, (int) config('otp.backoff_reset_hours', 24) * 3600);

        // 1. تحديث عدّاد الانتظار التصاعدي (1 -> 2 -> 3 -> ...)
        $attemptsKey = $this->key($scope, 'attempts', $scopeId);
        $attemptsData = Cache::get($attemptsKey);

        if (is_array($attemptsData) && ($attemptsData['expires_at'] ?? 0) > $now) {
            $attempt = (int) ($attemptsData['count'] ?? 0) + 1;
            $attemptsExpiresAt = (int) $attemptsData['expires_at'];
        } else {
            $attempt = 1;
            $attemptsExpiresAt = $now + $resetSeconds;
        }

        Cache::put($attemptsKey, [
            'count'      => $attempt,
            'expires_at' => $attemptsExpiresAt,
        ], Carbon::createFromTimestamp($attemptsExpiresAt));

        // 2. حساب مدة الانتظار التصاعدي: المحاولة 1 = 60ث، المحاولة 2 = 120ث، المحاولة 3 = 240ث ...
        $stepIndex = min($attempt - 1, count($backoffLadder) - 1);
        $cooldownSeconds = (int) $backoffLadder[$stepIndex];
        $cooldownExpiresAt = $now + $cooldownSeconds;

        Cache::put($this->key($scope, 'cooldown', $scopeId), [
            'attempt'    => $attempt,
            'seconds'    => $cooldownSeconds,
            'expires_at' => $cooldownExpiresAt,
        ], Carbon::createFromTimestamp($cooldownExpiresAt));

        // 3. تحديث العدّاد الساعي واليومي
        $this->incrementWindowCounter($this->key($scope, 'hour', $scopeId), 3600, $now);
        $this->incrementWindowCounter($this->key($scope, 'day', $scopeId), 86400, $now);

        // 4. تسجيل الرقم ضمن الأرقام الفريدة لهذا الجهاز / الـ IP
        if ($normalizedPhone !== null) {
            $phonesKey = $this->key($scope, 'phones', $scopeId);
            $windowSeconds = max(3600, (int) config('otp.unique_phones_window_hours', 24) * 3600);
            $phonesData = Cache::get($phonesKey);

            if (is_array($phonesData) && ($phonesData['expires_at'] ?? 0) > $now) {
                $phones = $phonesData['phones'] ?? [];
                $phonesExpiresAt = (int) $phonesData['expires_at'];
            } else {
                $phones = [];
                $phonesExpiresAt = $now + $windowSeconds;
            }

            if (! in_array($normalizedPhone, $phones, true)) {
                $phones[] = $normalizedPhone;
            }

            Cache::put($phonesKey, [
                'phones'     => $phones,
                'expires_at' => $phonesExpiresAt,
            ], Carbon::createFromTimestamp($phonesExpiresAt));
        }

        return [
            'attempt_number'        => $attempt,
            'next_cooldown_seconds' => $cooldownSeconds,
        ];
    }

    protected function incrementWindowCounter(string $cacheKey, int $windowSeconds, int $now): void
    {
        $data = Cache::get($cacheKey);
        if (is_array($data) && ($data['expires_at'] ?? 0) > $now) {
            $count = (int) ($data['count'] ?? 0) + 1;
            $expiresAt = (int) $data['expires_at'];
        } else {
            $count = 1;
            $expiresAt = $now + $windowSeconds;
        }

        Cache::put($cacheKey, [
            'count'      => $count,
            'expires_at' => $expiresAt,
        ], Carbon::createFromTimestamp($expiresAt));
    }

    protected function clearBackoffKeys(string $scope, string $scopeId): void
    {
        Cache::forget($this->key($scope, 'cooldown', $scopeId));
        Cache::forget($this->key($scope, 'attempts', $scopeId));
    }

    protected function key(string $scope, string $type, string $scopeId): string
    {
        return "otp:rl:{$scope}:{$type}:{$scopeId}";
    }

    /**
     * تنسيق المدة المتبقية باللغة العربية لعرضها للمستخدم بوضوح
     */
    public function formatDurationArabic(int $seconds): string
    {
        $seconds = max(1, $seconds);

        if ($seconds < 60) {
            return "{$seconds} ثانية";
        }

        if ($seconds < 3600) {
            $minutes = intdiv($seconds, 60);
            $remSeconds = $seconds % 60;

            $minText = match ($minutes) {
                1       => 'دقيقة واحدة',
                2       => 'دقيقتين',
                default => $minutes <= 10 ? "{$minutes} دقائق" : "{$minutes} دقيقة",
            };

            return $remSeconds > 0 ? "{$minText} و {$remSeconds} ثانية" : $minText;
        }

        $hours = intdiv($seconds, 3600);
        $remMinutes = intdiv($seconds % 3600, 60);

        $hourText = match ($hours) {
            1       => 'ساعة واحدة',
            2       => 'ساعتين',
            default => $hours <= 10 ? "{$hours} ساعات" : "{$hours} ساعة",
        };

        if ($remMinutes > 0) {
            $minText = match ($remMinutes) {
                1       => 'دقيقة',
                2       => 'دقيقتين',
                default => $remMinutes <= 10 ? "{$remMinutes} دقائق" : "{$remMinutes} دقيقة",
            };
            return "{$hourText} و {$minText}";
        }

        return $hourText;
    }
}
