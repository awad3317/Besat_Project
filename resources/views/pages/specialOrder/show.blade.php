@extends('layouts.app')
@section('title', 'تفاصيل الطلب الخاص #' . $order->id)
@section('Breadcrumb', 'تفاصيل الطلب الخاص #' . $order->id)

@section('addButton')
  <div class="flex items-center gap-2">
    <a href="{{ route('specialOrder.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 h-10 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center gap-1.5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        <span>العودة للطلبات</span>
    </a>
  </div>
  <x-modals.success-modal />
  <x-modals.error-modal />
  <x-modals.delete-modal />
@endsection

@section('content')
<div class="mx-auto max-w-(--breakpoint-2xl) space-y-6" dir="rtl">

    {{-- بطاقات النظرة العامة العلوية --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- حالة الطلب --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <span class="text-xs text-gray-500 dark:text-gray-400">حالة الطلب</span>
            <div class="mt-2">
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $order->status_class }}">
                    {{ $order->status_text }}
                </span>
            </div>
        </div>

        {{-- تاريخ الإنشاء --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <span class="text-xs text-gray-500 dark:text-gray-400">تاريخ الطلب</span>
            <h4 class="mt-2 text-base font-bold text-gray-900 dark:text-white" dir="ltr">
                {{ $order->created_at->format('Y-m-d H:i') }}
            </h4>
        </div>

        {{-- تم الإنشاء بواسطة --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <span class="text-xs text-gray-500 dark:text-gray-400">جهة الإنشاء</span>
            <h4 class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                {{ $order->creator ? $order->creator->name . ' (مشرف)' : ($order->created_by === 'app' ? 'تطبيق العميل' : 'النظام') }}
            </h4>
        </div>

        {{-- السعر الإجمالي --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <span class="text-xs text-gray-500 dark:text-gray-400">السعر النهائي</span>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-2xl font-black text-brand-500">
                    {{ $order->price !== null ? number_format($order->price) : 'لم يحدد' }}
                </span>
                <span class="text-xs font-bold text-gray-400">{{ config('app.currency_symbol', 'ر.ي') }}</span>
            </div>
        </div>
    </div>

    {{-- المحتوى الرئيسي --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- العمود الأيمن (معلومات الرحلة، العميل، السائق) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- بطاقة المسار والعنوان --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $order->title }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">تفاصيل المسار والوجهة</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- نقطة الانطلاق --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">نقطة الانطلاق</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm mt-0.5">
                                {{ $order->start_address }}
                            </p>
                        </div>
                    </div>

                    {{-- نقطة الوصول --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-50 text-red-600 dark:bg-red-500/15 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">نقطة الوصول</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm mt-0.5">
                                {{ $order->end_address }}
                            </p>
                        </div>
                    </div>
                </div>

                @if ($order->description)
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">وصف الطلب / الملاحظات:</span>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/60 p-3 rounded-xl leading-relaxed">
                            {{ $order->description }}
                        </p>
                    </div>
                @endif

                @if ($order->cancellation_reason)
                    <div class="mt-4 p-3 rounded-xl bg-error-50 dark:bg-error-500/15 border border-error-200 dark:border-error-500/30">
                        <span class="block text-xs font-bold text-error-700 dark:text-error-400 mb-1">سبب الإلغاء:</span>
                        <p class="text-sm text-error-600 dark:text-error-300">
                            {{ $order->cancellation_reason }}
                        </p>
                        @if ($order->canceller)
                            <span class="block text-[11px] text-error-500 mt-1">بواسطة: {{ $order->canceller->name }}</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- بطاقة العميل والسائق --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- العميل --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">بيانات العميل</h4>
                        @if ($order->user_id)
                            <a href="{{ route('users.show', $order->user_id) }}" class="text-xs text-brand-500 hover:underline">الملف الشخصي</a>
                        @endif
                    </div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">الاسم:</span>
                            <span class="font-bold text-gray-800 dark:text-white">
                                {{ $order->customer_name ?: ($order->user?->name ?? 'غير محدد') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">الهاتف:</span>
                            <span class="font-medium text-gray-800 dark:text-white" dir="ltr">
                                {{ $order->customer_phone ?: ($order->user?->phone ?? '—') }}
                            </span>
                        </div>
                        @if ($order->customer_whatsapp || $order->user?->whatsapp_number)
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">واتساب:</span>
                                @php
                                    $wa = $order->customer_whatsapp ?: $order->user?->whatsapp_number;
                                @endphp
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-emerald-600 hover:underline font-semibold mt-0.5" dir="ltr">
                                    <span>{{ $wa }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- السائق --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">بيانات السائق</h4>
                        @if ($order->driver_id)
                            <a href="{{ route('drivers.show', $order->driver_id) }}" class="text-xs text-brand-500 hover:underline">الملف الشخصي</a>
                        @endif
                    </div>

                    @if ($order->driver)
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-200 shrink-0">
                                <img src="{{ $order->driver->driver_image && !str_contains($order->driver->driver_image, 'default_driver1.png') ? url($order->driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                     alt="{{ $order->driver->name }}" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-900 dark:text-white">{{ $order->driver->name }}</h5>
                                <span class="text-xs text-gray-500" dir="ltr">{{ $order->driver->phone }}</span>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                            @if ($order->driver->vehicle)
                                <div class="flex justify-between">
                                    <span>نوع المركبة:</span>
                                    <span class="font-bold text-gray-800 dark:text-white">{{ $order->driver->vehicle->type }}</span>
                                </div>
                            @endif
                            @if ($order->driver->plate_number)
                                <div class="flex justify-between">
                                    <span>رقم اللوحة:</span>
                                    <span class="font-bold text-gray-800 dark:text-white">{{ $order->driver->plate_number }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-6 text-center text-sm text-gray-400">
                            <p>لم يتم تعيين سائق بعد.</p>
                            <a href="{{ route('specialOrder.index') }}" class="inline-block mt-2 text-xs font-semibold text-brand-500 hover:underline">
                                الانتقال لجدول الطلبات لتعيين سائق
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- العمود الأيسر (تفاصيل السعر والإجراءات السريعة) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- كارت الفاتورة / السعر --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-brand-500"></div>

                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-gray-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">فاتورة الطلب</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">ملخص التكلفة المعتمدة</span>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-300">
                        <span>قيمة الطلب المتفق عليها:</span>
                        <span class="font-bold text-gray-900 dark:text-white">
                            {{ $order->price !== null ? number_format($order->price) : '0' }} {{ config('app.currency_symbol', 'ر.ي') }}
                        </span>
                    </div>

                    <div class="pt-3 border-t border-dashed border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <span class="font-bold text-gray-900 dark:text-white">الإجمالي:</span>
                        <span class="text-xl font-black text-brand-500">
                            {{ $order->price !== null ? number_format($order->price) : '0' }} {{ config('app.currency_symbol', 'ر.ي') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- عمليات إدارية سريعة --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-2">إجراءات إدارية</h4>

                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { action: '{{ route('specialOrder.destroy', $order->id) }}' } }))"
                        class="w-full h-10 px-4 rounded-xl text-xs font-semibold text-error-600 bg-error-50 hover:bg-error-100 dark:bg-error-500/15 dark:hover:bg-error-500/25 transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>حذف هذا الطلب نهائياً</span>
                </button>
            </div>

        </div>

    </div>

</div>
@endsection

