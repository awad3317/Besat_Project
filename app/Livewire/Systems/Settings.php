<?php

namespace App\Livewire\Systems;

use App\Models\app_setting;
use App\Services\Evolution\EvolutionApiService;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Log;
use App\Models\Surcharge;
use Livewire\Component;

class Settings extends Component
{
    // خصائص لحفظ حالة الإعدادات والرسوم
    public array $settings = [];
    public $surcharges;

    // خصائص لإدارة النافذة المنبثقة (Modal)
    public $modalOpen = false;
    public $modalTitle = '';
    public $modalDesc = '';
    public $modalType = 'simple';

    // خصائص للنافذة البسيطة
    public $simpleLabel = '';
    public $simpleValue = '';
    public $simpleKey = '';
    public $simpleInputType = 'text';

    // خصائص لنموذج الرسوم الإضافية
    public array $surchargeForm = [];
    public $isTimeEditable = true;

    // خصائص إدارة اتصال WhatsApp
    public string $whatsappStatus = 'close';
    public ?string $whatsappQrCode = null;
    public bool $whatsappLoading = false;
    public ?string $whatsappError = null;

    // يتم استدعاؤها عند تحميل المكون لأول مرة
    public function mount()
    {
        $this->loadData();
        $this->loadWhatsAppStatus();
    }

    // دالة لتحميل/إعادة تحميل البيانات من قاعدة البيانات
    public function loadData()
    {
        // استخدم firstOrFail لضمان وجود سجل إعدادات
        $app_settings = app_setting::firstOrFail(); 
        $this->settings = [
            'commission_rate'    => $app_settings->commission_rate,
            'auto_assign'        => (bool) $app_settings->auto_assign_to_drivers,
            'whatsapp_support'   => $app_settings->company_whatsapp,
            'maintenance_mode'   => (bool) $app_settings->maintenance_mode,
            'app_version'        => $app_settings->version,
            'android_update_url' => $app_settings->android_update_url,
            'ios_update_url'     => $app_settings->ios_update_url,
            'company_website'    => $app_settings->company_website,
            'company_email'      => $app_settings->company_email,
            'ref_no'             => $app_settings->ref_no,
            'otp_enabled'        => (bool) $app_settings->otp_enabled,
        ];
        $this->surcharges = Surcharge::all();
    }

    // دالة لفتح النافذة المنبثقة البسيطة
    public function openSimpleModal($title, $desc, $label, $key, $type = 'text')
    {
        $this->resetErrorBag();
        $this->modalType = 'simple';
        $this->modalTitle = $title;
        $this->modalDesc = $desc;
        $this->simpleLabel = $label;
        $this->simpleKey = $key;
        $this->simpleValue = $this->settings[$key];
        $this->simpleInputType = $type;
        $this->modalOpen = true;
    }

    // دالة لفتح نافذة الرسوم الإضافية (للإنشاء أو التعديل)
    public function openSurchargeModal($surchargeId = null)
    {
        $this->resetErrorBag();
        $this->modalType = 'surcharge';
        
        if ($surchargeId) {
            $surcharge = Surcharge::find($surchargeId);
            $this->modalTitle = 'تعديل قاعدة تسعير';
            $this->modalDesc = 'قم بتعديل بيانات قاعدة التسعير الحالية';
            $this->surchargeForm = $surcharge->toArray();
            $this->isTimeEditable = !is_null($surcharge->time_from) && !is_null($surcharge->time_to);
        } else {
            $this->modalTitle = 'إضافة قاعدة جديدة';
            $this->modalDesc = 'قم بإضافة قاعدة تسعير جديدة للنظام';
            $this->modalTitle = 'إضافة قاعدة جديدة';
            $this->modalDesc = 'قم بإضافة قاعدة تسعير جديدة للنظام';
            $this->surchargeForm = ['id' => null, 'name' => '', 'type' => 'percentage', 'time_from' => '', 'time_to' => '', 'amount' => '', 'is_active' => true];
            $this->isTimeEditable = true;
        }
        $this->modalOpen = true;
    }

    // دالة لحفظ التغييرات من النافذة المنبثقة
    public function saveSettings()
    {
        $appSettingsModel = app_setting::firstOrFail();

        if ($this->modalType === 'simple') {
            // يمكنك إضافة validation هنا
            $db_key = match($this->simpleKey) {
                'auto_assign' => 'auto_assign_to_drivers', 'whatsapp_support' => 'company_whatsapp', 'app_version' => 'version', default => $this->simpleKey
            };
            $appSettingsModel->update([$db_key => $this->simpleValue]);
        } else { // modalType is 'surcharge'
            // يمكنك إضافة validation هنا
            if (!empty($this->surchargeForm['id'])) {
                Surcharge::find($this->surchargeForm['id'])->update($this->surchargeForm);
            } else {
                Surcharge::create($this->surchargeForm);
            }
        }
        Cache::forget('app_settings');
        
        $this->dispatch('notify', ['message' => 'تم حفظ التغييرات بنجاح']);
        $this->loadData(); // إعادة تحميل البيانات لعرضها محدثة
        $this->modalOpen = false;
    }

    // يتم استدعاؤها تلقائياً عند تغيير قيمة أي مفتاح في $settings عبر wire:model.live
    public function updatedSettings($value, $key)
    {
        $db_key = match($key) {
            'auto_assign' => 'auto_assign_to_drivers',
            'maintenance_mode' => 'maintenance_mode',
            'otp_enabled'      => 'otp_enabled',
            default => null
        };
        
        if ($db_key) {
            app_setting::firstOrFail()->update([$db_key => $value]);
            Cache::forget('app_settings');
            $this->dispatch('notify', ['message' => 'تم التحديث']);
            $this->loadData();
        }
    }
    
    public function toggleSurcharge($id)
    {
        $surcharge = Surcharge::find($id);
        if ($surcharge) {
            $surcharge->update(['is_active' => !$surcharge->is_active]);
            $this->loadData();
        }
    }

    public function deleteSurcharge($id)
    {
        Surcharge::destroy($id);
        $this->loadData();
        $this->dispatch('notify', ['message' => 'تم حذف القاعدة بنجاح']);
    }

    // ─── WhatsApp Connection Management ───────────────────────────────

    /**
     * تحميل حالة اتصال WhatsApp من Evolution API
     * تُستدعى عند mount وعند الضغط على زر التحديث
     */
    public function loadWhatsAppStatus(): void
    {
        $this->whatsappLoading = true;
        $this->whatsappError = null;

        try {
            $apiService = app(EvolutionApiService::class);
            $stateResponse = $apiService->checkConnectionState();
            $this->whatsappStatus = $stateResponse['instance']['state'] ?? 'close';

            // حفظ الحالة في الكاش
            Cache::put('whatsapp_status', $this->whatsappStatus, now()->addDays(7));

            // جلب QR Code فقط إذا كان الحساب غير متصل
            if ($this->whatsappStatus !== 'open') {
                $qrResponse = $apiService->getQrCode();
                $this->whatsappQrCode = $qrResponse['base64'] ?? null;
            } else {
                $this->whatsappQrCode = null;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Status Check Error: ' . $e->getMessage());
            $this->whatsappStatus = Cache::get('whatsapp_status', 'close');
            $this->whatsappError = 'تعذر الاتصال بسيرفر WhatsApp. يرجى التحقق من إعدادات السيرفر.';
        }

        $this->whatsappLoading = false;
    }

    /**
     * تحديث حالة الاتصال - تُستدعى من الواجهة وعبر wire:poll
     */
    public function refreshWhatsAppStatus(): void
    {
        $this->loadWhatsAppStatus();
    }

    /**
     * قطع اتصال WhatsApp عبر Evolution API
     */
    public function disconnectWhatsApp(EvolutionApiService $apiService)
{
    try {
        // 1. إرسال طلب الفصل للسيرفر
        $success = $apiService->logoutInstance();

        if ($success) {
            // 2. مسح الكاش المحلي فوراً
            Cache::forget('whatsapp_status');
            $this->whatsappStatus = 'close';

            // 3. الانتظار ثانية واحدة ليتنفس السيرفر بعد الفصل
            sleep(1);

            // 4. استدعاء دالة التحديث لإجبار السيرفر على توليد QR جديد وحفظه في المتغير
            $this->refreshWhatsAppStatus($apiService);

            // إرسال تنبيه نجاح للواجهة
            $this->dispatch('notify', ['type' => 'success', 'message' => 'تم قطع اتصال الحساب بنجاح، وجاري تجهيز كود جديد.']);
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'فشل في قطع الاتصال من السيرفر.']);
        }
    } catch (\Exception $e) {
        \Log::error("WhatsApp Disconnect Error: " . $e->getMessage());
        $this->dispatch('notify', ['type' => 'error', 'message' => 'حدث خطأ أثناء محاولة فصل الحساب.']);
    }
}

    public function render()
    {
        // استخدم layout الرئيسي للتطبيق
        return view('livewire.systems.settings');
    }
}


