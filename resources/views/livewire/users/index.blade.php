<div>

    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        
        <div wire:click.debounce.150ms="applyFilter('all')"
             wire:loading.class="opacity-50"
             wire:target="applyFilter"
             class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] 
                    @if ($activeFilter == 'all') border border-brand-500 dark:border-brand-500 @endif">
            
            <div wire:loading wire:target="applyFilter('all')" 
                 class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المستخدمين</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['total'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('banned')"
             wire:loading.class="opacity-50"
             wire:target="applyFilter"
             class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] 
                    @if ($activeFilter == 'banned') border border-brand-500 dark:border-brand-500 @endif">
            <div wire:loading wire:target="applyFilter('banned')" 
                 class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
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
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">المحظورين</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['banned'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>
        <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>
    </div>

    <div class="space-y-5 sm:space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <div class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1 min-w-[250px]">
                        <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            البحث
                        </label>
                        <div class="relative">
                            <input wire:model.live="search"
                                   type="text"
                                   class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                                   placeholder="ابحث بالاسم أو الهاتف أو الواتساب..." />
                            <div class="absolute left-0 top-0 bottom-0 flex items-center px-3">
                                <div wire:loading wire:target="search" class="mt-2">
                                    <div class="h-5 w-5 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                <!-- الجدول -->
                <div wire:loading.class="opacity-50" 
                     wire:target="search,applyFilter,toggleBan"
                     class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full">
                            <!-- رأس الجدول -->
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الأسم
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                رقم الواتساب
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الحظر
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الطلبات
                                            </p>
                                        </div>
                                    </th>
                                    <th class="py-3">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الإجراءات
                                            </p>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- نهاية رأس الجدول -->
                            
                            <!-- جسم الجدول -->
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($this->users as $user)
                                    <tr wire:key="user-{{ $user->id }}">
                                        <!-- الاسم -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <div class="flex items-center gap-3">
                                                    <div>
                                                        <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                            {{ $user->name }}
                                                        </span>
                                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                            {{ $user->phone }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- رقم الواتساب -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $user->whatsapp_number ?? 'لا يوجد' }}
                                                </p>
                                            </div>
                                        </td>
                                        
                                        <!-- زر الحظر -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div x-data="{ isBanned: {{ $user->is_banned ? 'true' : 'false' }} }"
                                                 wire:key="toggle-{{ $user->id }}-{{ $user->is_banned }}">
                                                <label :for="'toggle_{{ $user->id }}'"
                                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                                    <div class="relative">
                                                        <input type="checkbox" 
                                                               :id="'toggle_{{ $user->id }}'"
                                                               class="sr-only" 
                                                               x-model="isBanned"
                                                               @change="$wire.toggleBan({{ $user->id }})" />
                                                        
                                                        <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                                            :class="isBanned ? 'bg-error-500' : 'bg-success-500'">
                                                        </div>
                                                        <div :class="isBanned ? 'translate-x-0' : 'translate-x-full'"
                                                            class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-300 ease-linear">
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                        
                                        <!-- عدد الطلبات -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $user->requests()->count() }}
                                                </p>
                                            </div>
                                        </td>
                                        
                                        <!-- الإجراءات -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-center">
                                                <a href="{{ route('users.show', $user->id) }}"
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
                            <td colspan="7" class="p-2">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div
                                        class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                        <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z"
                                                fill="" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                        لا يوجد مسئولين حتى الآن
                                    </p>
                                </div>
                            </td>
                        </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <!-- الترقيم الصفحي -->
                        @if($this->users->hasPages())
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        عرض {{ $this->users->firstItem() ?? 0 }} إلى {{ $this->users->lastItem() ?? 0 }} من {{ $this->users->total() }} عنصر
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        @if(!$this->users->onFirstPage())
                                            <button wire:click="previousPage"
                                                    wire:loading.attr="disabled"
                                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                السابق
                                            </button>
                                        @endif
                                        
                                        @php
                                            $current = $this->users->currentPage();
                                            $last = $this->users->lastPage();
                                            $start = max(1, $current - 2);
                                            $end = min($last, $current + 2);
                                            
                                            if ($end - $start < 4) {
                                                $start = max(1, $end - 4);
                                                $end = min($last, $start + 4);
                                            }
                                        @endphp
                                        
                                        @if($start > 1)
                                            <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">...</span>
                                        @endif
                                        
                                        @for($page = $start; $page <= $end; $page++)
                                            <button wire:click="gotoPage({{ $page }})"
                                                    wire:loading.attr="disabled"
                                                    class="px-3 py-1.5 text-sm font-medium rounded-md {{ $page == $current ? 'bg-brand-500 text-white' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                                {{ $page }}
                                            </button>
                                        @endfor
                                        
                                        @if($end < $last)
                                            <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">...</span>
                                        @endif
                                        
                                        @if($this->users->hasMorePages())
                                            <button wire:click="nextPage"
                                                    wire:loading.attr="disabled"
                                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                التالي
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
