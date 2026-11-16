@extends('layouts.app')
@section('title', 'كوبونات الخصم')
@section('Breadcrumb')


    <div class=" rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-title-sm font-bold text-gray-900 dark:text-white">
                    تفاصيل الكوبون: <span class="text-brand-500">{{ $coupon->code }}</span>
                </h2>
            </div>
        </div>
    </div>
@endsection
@section('style')

@endsection
@section('content')
    <div class="p-4 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl">
        <div class="max-w-[1400px] mx-auto space-y-6">

            <!-- Stats Cards -->
            <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">

                <!-- Discount Rate Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M12 21a9 9 0 110-18 9 9 0 010 18z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">معدل الخصم</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ number_format($coupon->discount_rate * 100, 2) }}%
                        </h4>
                    </div>
                </div>

                <!-- Max Discount Amount Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">السقف الأعلى للخصم</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ $coupon->max_discount_amount ? number_format($coupon->max_discount_amount, 2) . ' ريال' : 'بدون سقف' }}
                        </h4>
                    </div>
                </div>

                <!-- Min Trip Fare Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">الحد الأدنى لسعر الرحلة</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ $coupon->min_trip_fare ? number_format($coupon->min_trip_fare, 2) . ' ريال' : 'بدون حد أدنى' }}
                        </h4>
                    </div>
                </div>

                <!-- Current Uses Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">الحد الاقصى لكل مستخدم</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $coupon->usage_limit_per_user ?? 'غير محدود' }}</h4>
                    </div>
                </div>

                <!-- Max Uses Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">الحد الأقصى للاستخدام</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $coupon->max_uses }}</h4>
                    </div>
                </div>

                <!-- Status Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                        @if($coupon->is_active)
                            <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc6803" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <line x1="5.64" y1="5.64" x2="18.36" y2="18.36"></line>
                            </svg>
                        @endif
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">الحالة</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ $coupon->is_active ? 'نشط' : 'غير نشط' }}
                        </h4>
                    </div>
                </div>
            </div>


            <!-- Users Table -->
            <livewire:discount-codes.usage-users :coupon-id="$coupon->id" />
        </div>
    </div>
@endsection
@section('script')
@endsection