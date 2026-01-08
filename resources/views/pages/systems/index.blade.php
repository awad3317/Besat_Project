@extends('layouts.app')

@section('title', 'إدارة النظام')
@section('Breadcrumb', 'إدارة النظام')

@section('content')

{{-- <div class="flex items-stretch gap-6" x-data="{ 
    activeSection: 'general',
    modalOpen: false,
    modalTitle: '',
    modalDesc: '',
    modalType: 'simple', // 'simple' or 'surcharge'
    
    // Simple Modal Fields
    simpleLabel: '',
    simpleValue: '',
    simpleKey: '',
    simpleInputType: 'text',
    
    // Surcharge Fields
    surchargeForm: {
        id: null,
        name: '',
        type: 'percentage',
        time_from: '',
        time_to: '',
        amount: '',
        is_active: true
    },

    // App Settings State (Mock for now, would be from DB)
    settings: {
        commission_rate: @json($settings->commission_rate),
        auto_assign: @json($settings->auto_assign),
        whatsapp_support: @json($settings->whatsapp_support),
        maintenance_mode: @json($settings->maintenance_mode),
        app_version: @json($settings->app_version)    
    },

    openSimpleModal(title, desc, label, key, type = 'text') {
        this.modalType = 'simple';
        this.modalTitle = title;
        this.modalDesc = desc;
        this.simpleLabel = label;
        this.simpleKey = key;
        this.simpleValue = this.settings[key];
        this.simpleInputType = type;
        this.modalOpen = true;
    },

    openSurchargeModal(surcharge = null) {
        this.modalType = 'surcharge';
        if (surcharge) {
            this.modalTitle = 'تعديل قاعدة تسعير';
            this.modalDesc = 'قم بتعديل بيانات قاعدة التسعير الحالية';
            this.surchargeForm = { ...surcharge };
        } else {
            this.modalTitle = 'إضافة قاعدة جديدة';
            this.modalDesc = 'قم بإضافة قاعدة تسعير جديدة للنظام';
            this.surchargeForm = {
                id: null,
                name: '',
                type: 'percentage',
                time_from: '',
                time_to: '',
                amount: '',
                is_active: true
            };
        }
        this.modalOpen = true;
    },

    saveSettings() {
        if (this.modalType === 'simple') {
            this.settings[this.simpleKey] = this.simpleValue;
        } else {
            // Surcharge saving logic would go here
            console.log('Saving surcharge:', this.surchargeForm);
        }
        this.modalOpen = false;
    }
}">
  <!-- Sidebar Navigation (Left Side) -->
  <div class="flex-shrink-0 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
    style="width: 250px;">
    <div class="sticky top-6">
      <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white">أقسام الإعدادات</h3>
      </div>
      <nav class="p-2">
        <button @click="activeSection = 'general'" :class="activeSection === 'general' ? 'menu-item-active' : 'menu-item-inactive'"
          class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium transition-colors">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          الإعدادات العامة
        </button>
        <button @click="activeSection = 'surcharges'" :class="activeSection === 'surcharges' ? 'menu-item-active' : 'menu-item-inactive'"
          class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium transition-colors">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          قواعد التسعير
        </button>
        <button @click="activeSection = 'special'" :class="activeSection === 'special' ? 'menu-item-active' : 'menu-item-inactive'"
          class="flex w-full m-2 items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium transition-colors">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
          </svg>
          الإعدادات الخاصة
        </button>
      </nav>
    </div>
  </div>

  <!-- Content Area (Right Side) -->
  <div class="flex-1 relative">
    
    <!-- General Settings Section -->
    <div x-show="activeSection === 'general'" x-transition
      class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">الإعدادات العامة</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إدارة إعدادات التطبيق الأساسية</p>
      </div>

      <div class="divide-y divide-gray-200 dark:divide-gray-800">
        <!-- Commission Rate Setting -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">نسبة العمولة</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">النسبة المئوية للعمولة على كل عملية</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-gray-800 dark:text-white" x-text="settings.commission_rate + '%'"></span>
              <button @click="openSimpleModal('تعديل نسبة العمولة', 'يرجى إدخال النسبة المئوية الجديدة للعمولة', 'نسبة العمولة (%)', 'commission_rate', 'number')"
                class="rounded-lg bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                تعديل
              </button>
            </div>
          </div>
        </div>

        <!-- Auto Assign Setting -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">التوزيع التلقائي للسائقين</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تفعيل التوزيع التلقائي للطلبات على السائقين</p>
              </div>
            </div>
            <label class="flex cursor-pointer items-center gap-3">
              <div class="relative">
                <input type="checkbox" class="sr-only" x-model="settings.auto_assign">
                <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                  :class="settings.auto_assign ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300 ease-in-out"
                  :class="{ 'translate-x-full': settings.auto_assign }"></div>
              </div>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Surcharges Section -->
    <div x-show="activeSection === 'surcharges'" x-transition
      class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">قواعد التسعير الإضافية</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إدارة الزيادات السعرية حسب الوقت والظروف</p>
          </div>
          <button @click="openSurchargeModal()"
            class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            <div class="flex items-center gap-2">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              إضافة قاعدة جديدة
            </div>
          </button>
        </div>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">اسم القاعدة</th>
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">النوع</th>
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">من الساعة</th>
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">إلى الساعة</th>
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">المبلغ</th>
                <th class="pb-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">الحالة</th>
                <th class="pb-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr>
                <td class="py-4 text-sm text-gray-800 dark:text-white">زيادة الفترة الليلية</td>
                <td class="py-4">
                  <span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">نسبة مئوية</span>
                </td>
                <td class="py-4 text-sm text-gray-600 dark:text-gray-400">22:00</td>
                <td class="py-4 text-sm text-gray-600 dark:text-gray-400">06:00</td>
                <td class="py-4 text-sm font-semibold text-gray-800 dark:text-white">25%</td>
                <td class="py-4">
                   <label class="flex cursor-pointer items-center gap-2">
                      <div class="relative">
                        <input type="checkbox" class="sr-only" checked>
                        <div class="block h-6 w-11 rounded-full bg-success-500 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm translate-x-full transition-transform duration-300"></div>
                      </div>
                    </label>
                </td>
                <td class="py-4">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openSurchargeModal({id: 1, name: 'زيادة الفترة الليلية', type: 'percentage', time_from: '22:00', time_to: '06:00', amount: '25', is_active: true})"
                      class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Special Settings Section -->
    <div x-show="activeSection === 'special'" x-transition
      class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">الإعدادات الخاصة</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تخصيص الخيارات المتقدمة والروابط</p>
      </div>

      <div x-init="settings.company_website = 'https://example.com'; settings.update_url = 'https://update.example.com'" class="divide-y divide-gray-200 dark:divide-gray-800">
        <!-- Company Website -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">الموقع الإلكتروني</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">رابط الموقع الرسمي للشركة</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-gray-800 dark:text-white" x-text="settings.company_website"></span>
              <button @click="openSimpleModal('تعديل الموقع الإلكتروني', 'يرجى إدخال رابط الموقع الإلكتروني الجديد للشركة', 'الموقع الإلكتروني', 'company_website', 'url')"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                تعديل
              </button>
            </div>
          </div>
        </div>

        <!-- Update URL -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">رابط تحديث التطبيق</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">الرابط المستخدم لتنزيل أحدث نسخة من التطبيق</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-gray-800 dark:text-white" x-text="settings.update_url"></span>
              <button @click="openSimpleModal('تعديل رابط التحديث', 'يرجى إدخال رابط تحديث التطبيق الجديد', 'رابط التحديث', 'update_url', 'url')"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                تعديل
              </button>
            </div>
          </div>
        </div>

        <!-- Social Media Links -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">رقم الواتساب</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">رقم التواصل مع الدعم الفني</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-gray-800 dark:text-white" x-text="settings.whatsapp_support"></span>
              <button @click="openSimpleModal('تعديل رقم الواتساب', 'يرجى إدخال رقم الواتساب الجديد للدعم الفني', 'رقم الواتساب', 'whatsapp_support', 'text')"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                تعديل
              </button>
            </div>
          </div>
        </div>

        <!-- App Version -->
        <div class="px-6 py-5 ">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">إصدار التطبيق</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">رقم الإصدار الحالي للتطبيق</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-gray-800 dark:text-white" x-text="settings.app_version"></span>
              <button @click="openSimpleModal('تعديل إصدار التطبيق', 'يرجى إدخال رقم الإصدار الجديد للتطبيق', 'رقم الإصدار', 'app_version', 'text')"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                تعديل
              </button>
            </div>
          </div>
        </div>

        <!-- Maintenance Mode Toggle -->
        <div class="px-6 py-5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">وضع الصيانه</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إغلاق التطبيق للصيانة المؤقتة</p>
              </div>
            </div>
            <label class="flex cursor-pointer items-center gap-2">
                <div class="relative">
                  <input type="checkbox" class="sr-only" x-model="settings.maintenance_mode">
                  <div class="block h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700" :class="settings.maintenance_mode ? 'bg-error-500' : ''"></div>
                  <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-300" :class="settings.maintenance_mode ? 'translate-x-full' : ''"></div>
                </div>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Centralized Modal Overlay -->
    <div x-show="modalOpen" x-cloak 
      class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
      x-transition:enter="transition ease-out duration-300" 
      x-transition:enter-start="opacity-0" 
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-200" 
      x-transition:leave-start="opacity-100" 
      x-transition:leave-end="opacity-0">
      
      <div @click.away="modalOpen = false" 
        class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95">
        
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="modalDesc"></p>
        </div>

        <!-- Modal Content: Simple Input -->
        <div x-show="modalType === 'simple'" class="space-y-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400" x-text="simpleLabel"></label>
            <input :type="simpleInputType" x-model="simpleValue"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
          </div>
        </div>

        <!-- Modal Content: Surcharge Form -->
        <div x-show="modalType === 'surcharge'" class="space-y-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم القاعدة</label>
            <input type="text" x-model="surchargeForm.name" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:border-gray-600 dark:text-white">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">من الساعة</label>
              <input type="time" x-model="surchargeForm.time_from" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:border-gray-600 dark:text-white">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">إلى الساعة</label>
              <input type="time" x-model="surchargeForm.time_to" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:border-gray-600 dark:text-white">
            </div>
          </div>
          <div>
             <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">المبلغ / النسبة</label>
             <input type="number" x-model="surchargeForm.amount" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 dark:border-gray-600 dark:text-white">
          </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-800 mt-6">
          <button @click="modalOpen = false"
            class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            إلغاء
          </button>
          <button @click="saveSettings()"
            class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            حفظ التغييرات
          </button>
        </div>
      </div>
    </div>

  </div>
</div> --}}

<livewire:systems.settings />

@endsection

@section('script')
@endsection