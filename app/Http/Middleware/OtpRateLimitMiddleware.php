<?php

namespace App\Http\Middleware;

use App\Services\OtpRateLimiterService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OtpRateLimitMiddleware
{
    public function __construct(private OtpRateLimiterService $otpRateLimiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $phone = $request->input('phone');
        $check = $this->otpRateLimiter->check($request, is_string($phone) ? $phone : null);

        if (! $check['allowed']) {
            Log::warning('OTP Rate Limit Blocked', [
                'ip'                  => $request->ip(),
                'device_id'           => $this->otpRateLimiter->extractDeviceId($request),
                'phone'               => $this->otpRateLimiter->normalizePhone(is_string($phone) ? $phone : null),
                'path'                => $request->path(),
                'limit_type'          => $check['limit_type'] ?? 'rate_limit',
                'retry_after_seconds' => $check['retry_after_seconds'] ?? 0,
            ]);

            $retryAfter = (int) ($check['retry_after_seconds'] ?? 0);

            return response()->json([
                'success' => false,
                'message' => $check['message'],
                'data'    => [
                    'retry_after_seconds' => $retryAfter,
                    'limit_type'          => $check['limit_type'] ?? 'rate_limit',
                    'attempt_number'      => $check['attempt_number'] ?? null,
                ],
            ], (int) ($check['status'] ?? 429))
                ->header('Retry-After', (string) $retryAfter);
        }

        $response = $next($request);

        // تسجيل المحاولة وتفعيل الانتظار التصاعدي فقط عند نجاح إرسال OTP فعلياً
        if ($this->shouldRecordOtpAttempt($response)) {
            $hitMeta = $this->otpRateLimiter->hit($request, is_string($phone) ? $phone : null);

            // إرفاق وقت الانتظار القادم في الهيدر لمساعدة تطبيق الجوال في عرض العدّاد التنازلي
            if (isset($hitMeta['next_cooldown_seconds'])) {
                $response->headers->set('X-OTP-Cooldown-Seconds', (string) $hitMeta['next_cooldown_seconds']);
            }
        }

        return $response;
    }

    /**
     * التحقق مما إذا كانت الاستجابة تعني إرسال OTP فعلياً
     * (تجنب حرق المحاولة إذا كان otp_enabled=false أو فشل التحقق الأولي 422/401)
     */
    protected function shouldRecordOtpAttempt(Response $response): bool
    {
        if (! $response->isSuccessful()) {
            return false;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return true;
        }

        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            return true;
        }

        // إذا كان النظام في وضع otp_enabled=false يُعيد otp_required = false (لم يُرسل OTP عبر واتساب)
        if (array_key_exists('data', $decoded) && is_array($decoded['data']) && array_key_exists('otp_required', $decoded['data'])) {
            return (bool) $decoded['data']['otp_required'];
        }

        return ($decoded['success'] ?? true) === true;
    }
}
