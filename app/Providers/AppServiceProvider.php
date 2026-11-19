<?php

namespace App\Providers;

use App\Services\OtpRateLimiterService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Livewire::component('special-orders.index', \App\Livewire\SpecialOrders\Index::class);
        Livewire::component('discount-codes.index', \App\Livewire\DiscountCodes\Index::class);
        Livewire::component('special-orders.create', \App\Livewire\SpecialOrders\Create::class);
        Livewire::component('request.create', \App\Livewire\Request\Create::class);
        Livewire::component('request.index',\App\Livewire\Request\Index::class);
        Livewire::component('systems.settings', \App\Livewire\Systems\Settings::class);

        $this->configureOtpRateLimiters();
    }

    protected function configureOtpRateLimiters(): void
    {
        // حماية مسارات التحقق من رمز الـ OTP ضد التخمين (Brute-Force)
        RateLimiter::for('otp-verify', function (Request $request) {
            $limiterService = app(OtpRateLimiterService::class);
            $ip = (string) ($request->ip() ?: 'unknown');
            $phone = $limiterService->normalizePhone($request->input('phone')) ?? 'no-phone';
            $deviceId = $limiterService->extractDeviceId($request);

            $maxAttempts = (int) config('otp.verify_max_attempts', 5);
            $decayMinutes = (int) config('otp.verify_decay_minutes', 10);

            $responseCallback = function (Request $req, array $headers) use ($limiterService) {
                $retryAfter = (int) ($headers['Retry-After'] ?? 60);
                $durationText = $limiterService->formatDurationArabic($retryAfter);

                return response()->json([
                    'success' => false,
                    'message' => "تجاوزت الحد المسموح لمحاولات إدخال رمز التحقق. يرجى المحاولة بعد {$durationText}.",
                    'data'    => [
                        'retry_after_seconds' => $retryAfter,
                        'limit_type'          => 'otp_verify_limit',
                    ],
                ], 429, $headers);
            };

            $limits = [
                Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by('otp:verify:ip:' . sha1($ip . '|' . $phone))
                    ->response($responseCallback),
            ];

            if ($deviceId !== null) {
                $limits[] = Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by('otp:verify:device:' . sha1($deviceId . '|' . $phone))
                    ->response($responseCallback);
            }

            return $limits;
        });

        // حماية مسار فحص وجود رقم الهاتف ضد الاستكشاف العشوائي للأرقام
        RateLimiter::for('check-existence', function (Request $request) {
            $limiterService = app(OtpRateLimiterService::class);
            $ip = (string) ($request->ip() ?: 'unknown');
            $maxPerMinute = (int) config('otp.check_existence_max_per_minute', 10);

            return Limit::perMinute($maxPerMinute)
                ->by('auth:check-existence:' . sha1($ip))
                ->response(function (Request $req, array $headers) use ($limiterService) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 60);
                    $durationText = $limiterService->formatDurationArabic($retryAfter);

                    return response()->json([
                        'success' => false,
                        'message' => "تم إرسال طلبات كثيرة خلال وقت قصير. يرجى المحاولة بعد {$durationText}.",
                        'data'    => [
                            'retry_after_seconds' => $retryAfter,
                            'limit_type'          => 'check_existence_limit',
                        ],
                    ], 429, $headers);
                });
        });
    }
}
