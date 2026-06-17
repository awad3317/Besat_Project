<div class="p-4 md:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl">
    <div class="max-w-[1400px] mx-auto space-y-6">

        {{-- Stats Cards --}}
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-1">

            {{-- Total Ads Card --}}
            <div wire:click="applyFilter('all')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('all')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="26" height="26" fill="none" stroke="#dc6803" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">إجمالي الإعلانات</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] }}</h4>
                </div>
            </div>

            {{-- Active Ads Card --}}
            <div wire:click="applyFilter('active')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'active') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('active')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div
                    class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg fill="#dc6803" height="30" width="30" version="1.1"
                        xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" xml:space="preserve"
                        stroke="#dc6803" stroke-width="0.00024000000000000003">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g id="active">
                                <path d="M8.6,20.1l-7.8-8l1.4-1.4l6.4,6.5L21.8,3.9l1.4,1.4L8.6,20.1z"></path>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">الإعلانات النشطة</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['active'] }}</h4>
                </div>
            </div>

            {{-- Inactive Ads Card --}}
            <div wire:click="applyFilter('inactive')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'inactive') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                <div wire:loading wire:target="applyFilter('inactive')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
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
                </div>
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">الإعلانات المتوقفة</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['inactive'] }}</h4>
                </div>
            </div>

            <div
                class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>

        </div>

        {{-- Ads Grid Section --}}
        <div class="pt-4 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl">
            <div class="max-w-[1400px] mx-auto space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-6">
                <div wire:loading.class.delay="opacity-50" wire:target="applyFilter, nextPage, previousPage, gotoPage"
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 transition-opacity">

                    @forelse ($this->ads as $ad)
                    <div wire:key="ad-{{ $ad->id }}"
                        class="group bg-white dark:bg-gray-dark rounded-xl border border-gray-200 dark:border-gray-700 shadow-theme-sm hover:shadow-theme-md transition-all duration-300 overflow-visible flex flex-col">

                        <div class="relative w-full h-44 overflow-hidden rounded-t-xl bg-gray-100 dark:bg-gray-800">
                            <img src="{{ asset($ad->image_path) }}" alt="إعلان" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                           
                        </div>

                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div class="space-y-2 text-right">
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between" x-data="{ openOptions: false }">
                                <div>
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider shadow-sm {{ $ad->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' }}">
                                        {{ $ad->is_active ? 'مفعل' : 'غير مفعل' }}
                                    </span>
                                </div>

                                <div class="flex relative justify-center items-center flex-shrink-0">
                                    <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                                        class="actions-trigger-btn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="5" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="19" r="2"></circle>
                                        </svg>
                                    </button>

                                    <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                                        class="actions-dropdown-menu left-auto right-0">

                                        <!-- تعديل الإعلان -->
                                        <a href="{{ route('ads.edit', $ad->id) }}"
                                            class="actions-dropdown-item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل الإعلان
                                        </a>

                                        <!-- تفعيل / إلغاء التفعيل -->
                                        @if($ad->is_active)
                                            <button type="button" wire:click="toggleActive({{ $ad->id }})"
                                                class="actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                إلغاء التفعيل
                                            </button>
                                        @else
                                            <button type="button" wire:click="toggleActive({{ $ad->id }})"
                                                class="actions-dropdown-item text-success-600 dark:text-success-500 hover:bg-success-50 dark:hover:bg-success-950/20">
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                تفعيل الإعلان
                                            </button>
                                        @endif

                                        <!-- حذف الإعلان -->
                                        <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                حذف الإعلان
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-1 md:col-span-2 xl:col-span-3 2xl:col-span-4 text-center py-16">
                        <p class="text-lg font-medium text-gray-500 dark:text-gray-400">لا توجد إعلانات مضافة حالياً.</p>
                        <p class="text-sm text-gray-400 mt-1">اضغط على زر "إضافة إعلان جديد" بالأعلى للبدء.</p>
                    </div>
                    @endforelse

                </div>

                @if ($this->ads->hasPages())
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $this->ads->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
</div>
