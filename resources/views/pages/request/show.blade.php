@extends('layouts.app')
@section('title', 'تفاصيل الطلب')
@section('Breadcrumb', "تفاصيل الطلب")
@section('addButton')
  <x-modals.success-modal />
  <x-modals.error-modal />
  <x-modals.delete-modal />
@endsection
@section('style')
<style>
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
        padding-right: 2rem;
        border-right: 2px solid #e5e7eb;
    }
    .timeline-item:last-child {
        border-right: none;
    }
    .timeline-dot {
        position: absolute;
        right: -9px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #fff;
        border: 4px solid var(--brand-500);
    }
    .dark .timeline-dot {
        background-color: #1f2937;
        border-color: var(--brand-500);
    }
</style>
@endsection

@section('content')

<div class="mx-auto max-w-(--breakpoint-2xl) space-y-6">

    <!-- Stats Overview -->

    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        <!-- Status Card -->
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                @if($request->status == 'completed')
                    <svg width="30" height="30" viewBox="0 0 1024 1024" fill="#dc6803" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <path d="M824.8 1003.2H203.2c-12.8 0-25.6-2.4-37.6-7.2-11.2-4.8-21.6-12-30.4-20.8-8.8-8.8-16-19.2-20.8-30.4-4.8-12-7.2-24-7.2-37.6V260c0-12.8 2.4-25.6 7.2-37.6 4.8-11.2 12-21.6 20.8-30.4 8.8-8.8 19.2-16 30.4-20.8 12-4.8 24-7.2 37.6-7.2h94.4v48H203.2c-26.4 0-48 21.6-48 48v647.2c0 26.4 21.6 48 48 48h621.6c26.4 0 48-21.6 48-48V260c0-26.4-21.6-48-48-48H730.4v-48H824c12.8 0 25.6 2.4 37.6 7.2 11.2 4.8 21.6 12 30.4 20.8 8.8 8.8 16 19.2 20.8 30.4 4.8 12 7.2 24 7.2 37.6v647.2c0 12.8-2.4 25.6-7.2 37.6-4.8 11.2-12 21.6-20.8 30.4-8.8 8.8-19.2 16-30.4 20.8-11.2 4.8-24 7.2-36.8 7.2z"></path>
                        <path d="M752.8 308H274.4V152.8c0-32.8 26.4-60 60-60h61.6c22.4-44 67.2-72.8 117.6-72.8 50.4 0 95.2 28.8 117.6 72.8h61.6c32.8 0 60 26.4 60 60v155.2m-430.4-48h382.4V152.8c0-6.4-5.6-12-12-12H598.4l-5.6-16c-12-33.6-43.2-56-79.2-56s-67.2 22.4-79.2 56l-5.6 16H334.4c-6.4 0-12 5.6-12 12v107.2zM432.8 792c-6.4 0-12-2.4-16.8-7.2L252.8 621.6c-4.8-4.8-7.2-10.4-7.2-16.8s2.4-12 7.2-16.8c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2L418.4 720c4 4 8.8 5.6 13.6 5.6s10.4-1.6 13.6-5.6l295.2-295.2c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2c9.6 9.6 9.6 24 0 33.6L449.6 784.8c-4.8 4-11.2 7.2-16.8 7.2z"></path>
                    </svg>
                @elseif($request->status == 'cancelled')
                    <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#dc6803">
                        <path d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"></path>
                    </svg>
                @else
                    <svg fill="#dc6803" width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.7,2c-0.1,0-0.1,0-0.2,0c0,0,0,0-0.1,0v0c-0.2,0-0.3,0-0.5,0l0.2,2c0.4,0,0.9,0,1.3,0c4,0.3,7.3,3.5,7.5,7.6 c0.2,4.4-3.2,8.2-7.6,8.4c0,0-0.1,0-0.2,0c-0.3,0-0.7,0-1,0L11,22c0.4,0,0.8,0,1.3,0c0.1,0,0.3,0,0.4,0v0c5.4-0.4,9.5-5,9.3-10.4 c-0.2-5.1-4.3-9.1-9.3-9.5v0c0,0,0,0,0,0c-0.2,0-0.3,0-0.5,0C12,2,11.9,2,11.7,2z M8.2,2.7C7.7,3,7.2,3.2,6.7,3.5l1.1,1.7 C8.1,5,8.5,4.8,8.9,4.6L8.2,2.7z M4.5,5.4c-0.4,0.4-0.7,0.9-1,1.3l1.7,1C5.4,7.4,5.7,7.1,6,6.7L4.5,5.4z M15.4,8.4l-4.6,5.2 l-2.7-2.1L7,13.2l4.2,3.2l5.8-6.6L15.4,8.4z M2.4,9c-0.2,0.5-0.3,1.1-0.3,1.6l2,0.3c0.1-0.4,0.1-0.9,0.3-1.3L2.4,9z M4.1,13l-2,0.2 c0,0.1,0,0.2,0,0.3c0.1,0.4,0.2,0.9,0.3,1.3l1.9-0.6c-0.1-0.3-0.2-0.7-0.2-1.1L4.1,13z M5.2,16.2l-1.7,1.1c0.3,0.5,0.6,0.9,1,1.3 L6,17.3C5.7,16.9,5.4,16.6,5.2,16.2z M7.8,18.8l-1.1,1.7c0.5,0.3,1,0.5,1.5,0.8l0.8-1.8C8.5,19.2,8.1,19,7.8,18.8z"></path>
                    </svg>
                @endif
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">حالة الطلب</span>
                <div class="mt-1">
                    <span class="{{ $request->status_class }} inline-flex rounded-full px-2.5 py-0.5 text-sm font-medium">
                        {{ $request->status_text }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Date Card -->
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5v-5z"/>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">تاريخ الطلب</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $request->created_at->format('Y-m-d H:i') }}
                </h4>
            </div>
        </div>

        <!-- Distance Card -->
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">المسافة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $request->distance_km }} كم
                </h4>
            </div>
        </div>

        <!-- Total Price Card -->
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">السعر النهائي</span>
                <h4 class="mt-1 text-lg font-bold text-brand-500">
                    {{ $request->final_price }} {{ config('app.currency_symbol', 'ر.ي') }}
                </h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Trip & People Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Route Info -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-6">مسار الرحلة</h3>
                <div class="relative px-2">
                    <!-- Start Point -->
                    <div class="timeline-item pb-8">
                        <div class="timeline-dot border-green-500"></div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 mb-1">نقطة الانطلاق</span>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $request->start_address }}</p>
                            @if($request->start_latitude && $request->start_longitude)
                                <a href="https://maps.google.com/?q={{$request->start_latitude}},{{$request->start_longitude}}" target="_blank" class="text-xs text-brand-500 hover:underline mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    عرض في الخريطة
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- End Point -->
                    <div class="timeline-item border-none pb-0">
                        <div class="timeline-dot border-red-500"></div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 mb-1">نقطة الوصول</span>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $request->end_address }}</p>
                            @if($request->end_latitude && $request->end_longitude)
                                <a href="https://maps.google.com/?q={{$request->end_latitude}},{{$request->end_longitude}}" target="_blank" class="text-xs text-brand-500 hover:underline mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    عرض في الخريطة
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">العميل</h3>
                         <a href="{{ route('users.show', $request->user_id) }}" class="text-xs text-brand-500 hover:text-brand-600">عرض الملف</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative h-14 w-14 shrink-0">
                            @if ($request->user->image)
                                <img src="{{ url($request->user->image) }}" class="h-full w-full rounded-full object-cover border border-gray-100 dark:border-gray-700" alt="{{ $request->user->name }}">
                            @else
                                <div class="h-full w-full rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-base font-medium text-gray-800 dark:text-white">{{ $request->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $request->user->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Driver Info -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                     <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">السائق</h3>
                        @if($request->driver_id)
                         <a href="{{ route('drivers.show', $request->driver_id) }}" class="text-xs text-brand-500 hover:text-brand-600">عرض الملف</a>
                        @endif
                    </div>
                    @if($request->driver)
                        <div class="flex items-center gap-4">
                            <div class="relative h-14 w-14 shrink-0">
                                @if ($request->driver->driver_image)
                                    <img src="{{ url($request->driver->driver_image) }}" class="h-full w-full rounded-full object-cover border border-gray-100 dark:border-gray-700" alt="{{ $request->driver->name }}">
                                @else
                                    <div class="h-full w-full rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                                         <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-base font-medium text-gray-800 dark:text-white">{{ $request->driver->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $request->driver->phone }}</p>
                            </div>
                        </div>
                        @if($request->vehicle)
                         <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-sm">
                            <span class="text-gray-500">المركبة</span>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $request->vehicle->name }}</span>
                         </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center py-2 text-center">
                            <div class="h-10 w-10 text-gray-300 mb-2">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                            </div>
                            <p class="text-sm text-gray-500">لم يتم تعيين سائق بعد</p>
                        </div>
                    @endif
                </div>
            </div>
            
             @if($request->notes)
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">ملاحظات</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ $request->notes }}</p>
            </div>
            @endif

        </div>

        <!-- Right Column: Price Breakdown -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] sticky top-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-5">تفاصيل السعر</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">سعر المسافة ({{ $request->distance_km }} كم)</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $request->original_price }} {{ config('app.currency_symbol', 'ر.ي') }}</span>
                    </div>

                     @if($request->surcharges->isNotEmpty())
                        <div class="py-2 border-y border-dashed border-gray-100 dark:border-gray-700">
                             <p class="text-xs font-semibold text-gray-500 mb-2">الزيادات والرسوم:</p>
                             @foreach($request->surcharges as $surcharge)
                                <div class="flex justify-between items-center text-sm mb-1">
                                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        {{ $surcharge->name }}
                                    </span>
                                    <span class="font-medium text-warning-600 dark:text-warning-500">+{{ $surcharge->pivot->amount }} {{ config('app.currency_symbol', 'ر.ي') }}</span>
                                </div>
                             @endforeach
                        </div>
                    @endif

                    @if($request->discountCode)
                    <div class="flex justify-between items-center text-sm text-green-600 dark:text-green-500">
                        <span class="flex items-center gap-1">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            خصم ({{ $request->discountCode->code }})
                        </span>
                        <span>-{{ $request->discount_amount }} {{ config('app.currency_symbol', 'ر.ي') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center text-sm border-t border-gray-100 dark:border-gray-800 pt-3">
                        <span class="text-gray-500 dark:text-gray-400">عمولة التطبيق</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $request->app_commission_amount }} {{ config('app.currency_symbol', 'ر.ي') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-4 mt-2">
                        <span>الإجمالي</span>
                        <span class="text-brand-500">{{ $request->final_price }} {{ config('app.currency_symbol', 'ر.ي') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection