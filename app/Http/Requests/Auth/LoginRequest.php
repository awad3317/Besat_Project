<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('phone', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'phone' => 'البيانات المدخلة غير صحيحة',
            ]);
        }
        if(Auth::user()->type == 'user' || Auth::user()->is_banned == true){
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([ 
                'phone' => 'ليس لديك صلاحية الدخول من خلال هذا الحساب او ان الحساب محظور',
            ]);
        }
        try {
        $deviceToken = 'f8ePF6O2SHOHTIkufFdDob:APA91bGcQX620q-JAOk7F0c9d82nijudjoSbKZ3siluX-r2JgWs3baJ3MzlQg08ft_QzkXPSUVuDV2KNukVpoG3EBRYiAfe4dd-mLA7I3fiH1fH71CoRaF4';
        
        // استدعاء السيرفيس ديناميكياً وإرسال الإشعار
        $firebaseService = app(\App\Services\FirebaseService::class);
        $firebaseService->sendNotification(
            $deviceToken,
            'تسجيل دخول جديد 🔐',
            'تم تسجيل الدخول إلى لوحة التحكم بنجاح بواسطة: ' . Auth::user()->name,
            ['type' => 'admin_login_alert'] // بيانات إضافية اختيارية للتطبيق
        );
    } catch (\Exception $e) {
        // نقوم بعمل Log للخطأ فقط حتى لا يتوقف تسجيل الدخول إذا فشل الإشعار التجريبي
        \Log::error('فشل إرسال الإشعار التجريبي للأدمن: ' . $e->getMessage());
    }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'phone' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('phone')).'|'.$this->ip());
    }
}
