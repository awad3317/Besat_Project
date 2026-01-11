<div>
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        <div wire:click.debounce.150ms="applyFilter('all')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md flex-1 bg-white dark:bg-white/[0.03] @if($activeFilter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('all')"
                class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('in_progress')" wire:loading.class="opacity-50"
            wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md flex-1 bg-white dark:bg-white/[0.03] @if($activeFilter == 'in_progress') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('in_progress')"
                class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="30" height="30" version="1.1" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.7,2c-0.1,0-0.1,0-0.2,0c0,0,0,0-0.1,0v0c-0.2,0-0.3,0-0.5,0l0.2,2c0.4,0,0.9,0,1.3,0c4,0.3,7.3,3.5,7.5,7.6 c0.2,4.4-3.2,8.2-7.6,8.4c0,0-0.1,0-0.2,0c-0.3,0-0.7,0-1,0L11,22c0.4,0,0.8,0,1.3,0c0.1,0,0.3,0,0.4,0v0c5.4-0.4,9.5-5,9.3-10.4 c-0.2-5.1-4.3-9.1-9.3-9.5v0c0,0,0,0,0,0c-0.2,0-0.3,0-0.5,0C12,2,11.9,2,11.7,2z M8.2,2.7C7.7,3,7.2,3.2,6.7,3.5l1.1,1.7 C8.1,5,8.5,4.8,8.9,4.6L8.2,2.7z M4.5,5.4c-0.4,0.4-0.7,0.9-1,1.3l1.7,1C5.4,7.4,5.7,7.1,6,6.7L4.5,5.4z M15.4,8.4l-4.6,5.2 l-2.7-2.1L7,13.2l4.2,3.2l5.8-6.6L15.4,8.4z M2.4,9c-0.2,0.5-0.3,1.1-0.3,1.6l2,0.3c0.1-0.4,0.1-0.9,0.3-1.3L2.4,9z M4.1,13l-2,0.2 c0,0.1,0,0.2,0,0.3c0.1,0.4,0.2,0.9,0.3,1.3l1.9-0.6c-0.1-0.3-0.2-0.7-0.2-1.1L4.1,13z M5.2,16.2l-1.7,1.1c0.3,0.5,0.6,0.9,1,1.3 L6,17.3C5.7,16.9,5.4,16.6,5.2,16.2z M7.8,18.8l-1.1,1.7c0.5,0.3,1,0.5,1.5,0.8l0.8-1.8C8.5,19.2,8.1,19,7.8,18.8z">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات الجارية</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['in_progress'] ?? 0 }}</h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('completed')" wire:loading.class="opacity-50"
            wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md flex-1 bg-white dark:bg-white/[0.03] @if($activeFilter == 'completed') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('completed')"
                class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="30" height="30" viewBox="0 0 1024 1024" fill="#dc6803" class="icon" version="1.1"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M824.8 1003.2H203.2c-12.8 0-25.6-2.4-37.6-7.2-11.2-4.8-21.6-12-30.4-20.8-8.8-8.8-16-19.2-20.8-30.4-4.8-12-7.2-24-7.2-37.6V260c0-12.8 2.4-25.6 7.2-37.6 4.8-11.2 12-21.6 20.8-30.4 8.8-8.8 19.2-16 30.4-20.8 12-4.8 24-7.2 37.6-7.2h94.4v48H203.2c-26.4 0-48 21.6-48 48v647.2c0 26.4 21.6 48 48 48h621.6c26.4 0 48-21.6 48-48V260c0-26.4-21.6-48-48-48H730.4v-48H824c12.8 0 25.6 2.4 37.6 7.2 11.2 4.8 21.6 12 30.4 20.8 8.8 8.8 16 19.2 20.8 30.4 4.8 12 7.2 24 7.2 37.6v647.2c0 12.8-2.4 25.6-7.2 37.6-4.8 11.2-12 21.6-20.8 30.4-8.8 8.8-19.2 16-30.4 20.8-11.2 4.8-24 7.2-36.8 7.2z">
                    </path>
                    <path
                        d="M752.8 308H274.4V152.8c0-32.8 26.4-60 60-60h61.6c22.4-44 67.2-72.8 117.6-72.8 50.4 0 95.2 28.8 117.6 72.8h61.6c32.8 0 60 26.4 60 60v155.2m-430.4-48h382.4V152.8c0-6.4-5.6-12-12-12H598.4l-5.6-16c-12-33.6-43.2-56-79.2-56s-67.2 22.4-79.2 56l-5.6 16H334.4c-6.4 0-12 5.6-12 12v107.2zM432.8 792c-6.4 0-12-2.4-16.8-7.2L252.8 621.6c-4.8-4.8-7.2-10.4-7.2-16.8s2.4-12 7.2-16.8c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2L418.4 720c4 4 8.8 5.6 13.6 5.6s10.4-1.6 13.6-5.6l295.2-295.2c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2c9.6 9.6 9.6 24 0 33.6L449.6 784.8c-4.8 4-11.2 7.2-16.8 7.2z">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات المكتملة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['completed'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('cancelled')" wire:loading.class="opacity-50"
            wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md flex-1 bg-white dark:bg-white/[0.03] @if($activeFilter == 'cancelled') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('cancelled')"
                class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
                    fill="#dc6803">
                    <path
                        d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات الملغية</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['cancelled'] ?? 0 }}
                </h4>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div class="mb-5">
            <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">البحث</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                    placeholder="ابحث باسم العميل، الهاتف، السائق..." />
                <div class="absolute left-0 top-0 bottom-0 flex items-center px-3">
                    <div wire:loading wire:target="search" class="mt-2">
                        <div class="h-5 w-5 animate-spin rounded-full border-2 border-brand-500 border-t-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div wire:loading.class="opacity-50" wire:target="search,applyFilter" class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 dark:border-gray-800">
                        <th class="py-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الطلب</p>
                        </th>
                        <th class="py-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السائق</p>
                        </th>
                        <th class="py-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">العميل</p>
                        </th>
                        <th class="py-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السعر</p>
                        </th>
                        <th class="py-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">حالة الطلب</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">من - الى</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">الإجراءات</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->orders as $order)
                        <tr wire:key="order-{{ $order->id }}">
                            <td class="py-3">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $loop->iteration }}</p>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                        <img src="{{ $order->driver->driver_image ? url($order->driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                            alt="Driver" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $order->driver->name }}</p>
                                        <span
                                            class="text-gray-500 text-theme-xs dark:text-gray-400">{{ $order->driver->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $order->customer_name ?? $order->user->name }}
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            {{ $order->customer_phone ?? $order->user->phone }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $order->price }}</p>
                            </td>
                            <td class="py-3">
                                <p class="rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $order->status_class }}">
                                    {{ $order->status_text }}
                                </p>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <p
                                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                        {{ $order->start_address }}</p>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <p
                                        class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                        {{ $order->end_address }}</p>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-center">
                                    {{-- Assuming you have a route for showing special order details --}}
                                    <a href="{{-- route('specialOrder.show', $order->id) --}}"
                                        class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        تفاصيل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                لا توجد طلبات تطابق البحث أو الفلتر الحالي.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $this->orders->links() }}
            </div>
        @endif
    </div>
</div>