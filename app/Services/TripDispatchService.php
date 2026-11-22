<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminNotification;
use App\Repositories\AppSettingRepository;
use App\Services\Evolution\EvolutionApiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TripDispatchService
{
    protected $driverLocationService;
    protected $firebaseService;
    protected $appSettingRepository;
    protected EvolutionApiService $evolutionApiService;

    // حقن الاعتماديات (Dependency Injection)
    public function __construct(
        DriverLocationService $driverLocationService,
        FirebaseService $firebaseService,
        AppSettingRepository $appSettingRepository,
        ?EvolutionApiService $evolutionApiService = null
    ) {
        $this->driverLocationService = $driverLocationService;
        $this->firebaseService = $firebaseService;
        $this->appSettingRepository = $appSettingRepository;
        $this->evolutionApiService = $evolutionApiService ?? app(EvolutionApiService::class);
    }

    /**
     * دالة توجيه الطلب للسائقين القريبين أو إرسال إشعار للداشبورد في النظام اليدوي
     */
    public function dispatchToDrivers($tripRequest)
    {
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });

        if ($appSettings && $appSettings->auto_assign_to_drivers) {
            // جلب السائقين القريبين المتصلين وبنفس نوع المركبة بناءً على إحداثيات الطلب
            $nearestDrivers = $this->driverLocationService->getNearestDrivers(
                $tripRequest->start_latitude, 
                $tripRequest->start_longitude, 
                8, 
                20,
                $tripRequest->vehicle_id
            );

            $notifiedDrivers = collect();

            if ($nearestDrivers) {
                $title = 'طلب جديد';
                $body  = 'يوجد طلب جديد في منطقتك، اضغط لقبول الطلب';
                $data  = [
                    'type'            => 'auto_dispatch_request',
                    'new_request'     => 'true',
                    'new_message'     => 'false',
                    'request_id'      => (string) $tripRequest->id,
                    'start_latitude'  => (string) $tripRequest->start_latitude,
                    'start_longitude' => (string) $tripRequest->start_longitude,
                    'start_address'   => (string) $tripRequest->start_address,
                ];

                foreach ($nearestDrivers as $driver) {
                    if (!empty($driver->device_token)) {
                        $notifiedDrivers->push($driver);
                        try {
                            $this->firebaseService->sendNotification($driver->device_token, $title, $body, $data);
                        } catch (\Exception $e) {
                            Log::error("Failed to send FCM to driver {$driver->id}: " . $e->getMessage());
                        }
                    }
                }
            }

            // إرسال إشعار واتساب لرقم خدمة العملاء بحالة التوزيع التلقائي وأسماء السائقين
            $this->notifyCustomerServiceViaWhatsApp($tripRequest, $notifiedDrivers);

            return true; // تم الإرسال بنجاح
        } else {
            // النظام اليدوي - إرسال الطلب للداشبورد فقط
            $this->notifyAdmins($tripRequest);
            return false;
        }
    }

    /**
     * إرسال إشعار عبر الواتساب لخدمة العملاء عند التوزيع التلقائي للرحلة
     */
    protected function notifyCustomerServiceViaWhatsApp($tripRequest, Collection $notifiedDrivers): void
    {
        $customerServicePhone = config('services.customer_service.whatsapp');

        if (empty($customerServicePhone)) {
            Log::warning('Customer service WhatsApp number is not configured in services.customer_service.whatsapp.');
            return;
        }

        try {
            $driversCount = $notifiedDrivers->count();
            $customerName = $tripRequest->user?->name ?? 'غير محدد';
            $customerPhone = $tripRequest->user?->phone ?? 'غير محدد';
            $vehicleType = $tripRequest->vehicle?->type ?? 'غير محدد';
            $startAddress = $tripRequest->start_address ?? 'غير محدد';
            $endAddress = $tripRequest->end_address ?? 'غير محدد';
            $finalPrice = number_format((float) ($tripRequest->final_price ?? 0), 0);

            if ($driversCount < 2) {
                // أقل من سائقين (0 أو 1): تنبيه أحمر عاجل بضرورة التدخل الفوري
                $header = "🔴🚨 *تنبيه عاجل (أحمر) - يجب التدخل الفوري!*\n";
                if ($driversCount === 0) {
                    $statusLine = "⚠️ يوجد طلب رحلة جديد رقم *#{$tripRequest->id}* ولكن *لم يتم العثور على أي سائق متصل قريب (0 سائقين)*!\n🔴 *يجب التدخل الفوري وتوجيه الطلب يدوياً.*";
                } else {
                    $statusLine = "⚠️ يوجد طلب رحلة جديد رقم *#{$tripRequest->id}* ولم يتم إرسال الإشعار إلا إلى *سائق واحد فقط (أقل من سائقين)*!\n🔴 *يجب التدخل الفوري ومتابعة الطلب.*";
                }
            } elseif ($driversCount <= 3) {
                // 3 سائقين أو أقل (2 أو 3): تنبيه أحمر بأن العدد قليل وقد يتطلب التدخل
                $header = "🔴⚠️ *تنبيه (أحمر) - عدد السائقين القريبين قليل ({$driversCount} سائقين فقط)*\n";
                $statusLine = "يوجد طلب رحلة جديد رقم *#{$tripRequest->id}* ولم يتم العثور إلا على *{$driversCount} سائقين فقط*.\n🔴 *يرجى الانتباه والتدخل في حال عدم قبول الطلب (يجب التدخل الفوري إذا كان العدد أقل من 2).*";
            } else {
                // أكثر من 3 سائقين (4 أو 5 سائقين فأكثر): إشعار طبيعي مطمئن مع أسماء السائقين
                $header = "🟢✅ *إشعار طلب رحلة جديد (توزيع تلقائي)*\n";
                $statusLine = "يوجد طلب رحلة جديد رقم *#{$tripRequest->id}* وتم إرسال الإشعار إلى *{$driversCount} سائقين* بنجاح.";
            }

            $message  = $header . "\n";
            $message .= $statusLine . "\n\n";
            $message .= "📋 *تفاصيل الطلب:*\n";
            $message .= "• *رقم الطلب:* #{$tripRequest->id}\n";
            $message .= "• *العميل:* {$customerName} ({$customerPhone})\n";
            $message .= "• *المركبة:* {$vehicleType}\n";
            $message .= "• *من:* {$startAddress}\n";
            $message .= "• *إلى:* {$endAddress}\n";
            $message .= "• *السعر:* {$finalPrice} ريال\n\n";

            if ($driversCount > 0) {
                $message .= "👨‍✈️ *السائقون الذين تم إرسال الإشعار إليهم ({$driversCount}):*\n";
                foreach ($notifiedDrivers->values() as $index => $driver) {
                    $num = $index + 1;
                    $driverName = $driver->name ?? 'سائق #' . $driver->id;
                    $driverPhone = !empty($driver->phone) ? " ({$driver->phone})" : '';
                    $distanceText = isset($driver->distance) ? ' - يبعد ' . round((float) $driver->distance, 1) . ' كم' : '';
                    $message .= "{$num}. {$driverName}{$driverPhone}{$distanceText}\n";
                }
            } else {
                $message .= "🔴 *السائقون الذين تم إشعارهم:* لا يوجد أي سائق قريب متصل حالياً.\n";
            }

            try {
                $url = route('request.show', $tripRequest->id);
                $message .= "\n🔗 *رابط الطلب:* {$url}";
            } catch (\Throwable $e) {
                // تجاهل في حال عدم توفر الرابط في سياق CLI
            }

            $this->evolutionApiService->sendText($customerServicePhone, $message);
        } catch (\Throwable $e) {
            Log::error("Failed to send auto-dispatch WhatsApp alert for request #{$tripRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * إرسال إشعار للداشبورد (حسابات الأدمن) عبر Firebase وحفظه في قاعدة البيانات
     */
    protected function notifyAdmins($tripRequest): void
    {
        $admins = User::whereIn('type', ['admin', 'superAdmin'])
            ->where('is_banned', false)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $title = 'طلب رحلة جديد #' . $tripRequest->id;
        $body  = 'يوجد طلب رحلة جديد من (' . ($tripRequest->start_address ?? 'غير محدد') . ') إلى (' . ($tripRequest->end_address ?? 'غير محدد') . ') بانتظار تعيين سائق.';
        $url   = route('request.show', $tripRequest->id);

        $data = [
            'new_request'     => 'true',
            'new_message'     => 'false',
            'request_id'      => (string) $tripRequest->id,
            'start_latitude'  => (string) ($tripRequest->start_latitude ?? ''),
            'start_longitude' => (string) ($tripRequest->start_longitude ?? ''),
            'start_address'   => (string) ($tripRequest->start_address ?? ''),
            'end_address'     => (string) ($tripRequest->end_address ?? ''),
            'url'             => (string) $url,
        ];

        $sentTokens = [];

        foreach ($admins as $admin) {
            try {
                $admin->notify(new AdminNotification($tripRequest, $title, $body, $url));
            } catch (\Exception $e) {
                Log::error("Failed to store database notification for admin {$admin->id}: " . $e->getMessage());
            }

            if (!empty($admin->fcm_token) && !in_array($admin->fcm_token, $sentTokens, true)) {
                $sentTokens[] = $admin->fcm_token;
                try {
                    $this->firebaseService->sendNotification($admin->fcm_token, $title, $body, $data);
                } catch (\Exception $e) {
                    Log::error("Failed to send FCM to admin {$admin->id}: " . $e->getMessage());
                }
            }
        }
    }
}