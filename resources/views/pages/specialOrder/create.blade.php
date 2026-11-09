@extends('layouts.app')
@section('title', 'إنشاء طلب خاص جديد')
@section('Breadcrumb', 'إنشاء طلب خاص جديد')

@section('addButton')
  <div class="flex items-center gap-2">
    <a href="{{ route('specialOrder.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 h-10 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center gap-1.5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        <span>العودة للطلبات الخاصة</span>
    </a>
  </div>
  <x-modals.success-modal />
  <x-modals.error-modal />
@endsection

@section('content')
<div class="p-6 w-full bg-white rounded-3xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm" dir="rtl">
    <form method="POST" action="{{ route('specialOrder.store') }}">
        @csrf

        {{-- حقول المسار الأساسية --}}
        <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-2 mb-5">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                    نقطة البداية / الانطلاق <span class="text-xs text-warning-500">*</span>
                </label>
                <input type="text" name="start_address" value="{{ old('start_address') }}" required
                    placeholder="مثال: المنصورة - شارع التسعين"
                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                @error('start_address')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                    نقطة النهاية / الوصول <span class="text-xs text-warning-500">*</span>
                </label>
                <input type="text" name="end_address" value="{{ old('end_address') }}" required
                    placeholder="مثال: المعلا - الشارع الرئيسي"
                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                @error('end_address')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                    السعر المتفق عليه (اختياري)
                </label>
                <input type="number" name="price" value="{{ old('price') }}"
                    placeholder="أدخل السعر بالريال"
                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                @error('price')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                    رقم الواتساب للعميل (اختياري)
                </label>
                <input type="text" name="customer_whatsapp" value="{{ old('customer_whatsapp') }}" dir="ltr"
                    placeholder="9677xxxxxxxx"
                    class="px-4 py-2.5 w-full h-11 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                @error('customer_whatsapp')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- تفاصيل العميل ونوع المركبة والكوبون عبر مكون Livewire --}}
        <livewire:special-orders.create />

        <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('specialOrder.index') }}"
                class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                إلغاء
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600 shadow-xs transition">
                حفظ وإنشاء الطلب
            </button>
        </div>
    </form>
</div>
@endsection
