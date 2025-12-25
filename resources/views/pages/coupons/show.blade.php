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
                    <div
                        class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">معدل الخصم</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                            {{ number_format($coupon->discount_rate * 100, 2) }}%
                        </h4>
                    </div>
                </div>

                <!-- Current Uses Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 w-full">
                        <span class="text-xs text-gray-500 dark:text-gray-400">مرات الاستخدام</span>
                        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $coupon->current_uses }}</h4>
                    </div>
                </div>

                <!-- Max Uses Card -->
                <div
                    class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                        <svg class="w-6 h-6"  fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
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
                    <div
                        class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                        @if($coupon->is_active)
                            <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" fill="#dc6803" stroke="#dc6803">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <title>cancelled</title>
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="add" fill="#dc6803" transform="translate(42.666667, 42.666667)">
                                            <path
                                                d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"
                                                id="Combined-Shape"> </path>
                                        </g>
                                    </g>
                                </g>
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
            <div
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">سجل استخدام الكوبون</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="p-4 text-right font-bold text-gray-600 dark:text-gray-300">#</th>
                                <th class="p-4 text-right font-bold text-gray-600 dark:text-gray-300">اسم المستخدم</th>
                                <th class="p-4 text-right font-bold text-gray-600 dark:text-gray-300">رقم الهاتف</th>
                                <th class="p-4 text-right font-bold text-gray-600 dark:text-gray-300">تاريخ الاستخدام</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="p-4 font-mono text-gray-500">{{ $user->id }}</td>
                                    <td class="p-4 font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</td>
                                    <td class="p-4 text-gray-600 dark:text-gray-400">{{ $user->phone }}</td>
                                    <td class="p-4 text-gray-600 dark:text-gray-400">
                                        {{ $user->pivot->created_at->format('Y-m-d H:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-gray-500">
                                        لم يتم استخدام هذا الكوبون من قبل أي مستخدم حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if ($users->hasPages())
                    <div class="p-5 border-t border-gray-100 dark:border-gray-800">
                        {{-- هذا سيستخدم تلقائيًا ملف الترقيم المعدل الذي أنشأناه سابقًا --}}
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection