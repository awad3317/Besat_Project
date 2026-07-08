<div>
    <!-- الكروت العلوية (الإحصائيات) -->
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        <div wire:click.debounce.150ms="applyFilter('all')"
             class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px]
                    @if ($activeFilter == 'all') border border-brand-500 @endif">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M4 10h3v7H4zm6.5 0h3v7h-3zM17 10h3v7h-3zm-15 8h20v2H2zm10-17L1 6v2h22V6z"/>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي طرق الدفع</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] ?? 0 }}</h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('stopped')"
             class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px]
                    @if ($activeFilter == 'stopped') border border-brand-500 @endif">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc6803" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">الطرق المتوقفة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['stopped'] ?? 0 }}</h4>
            </div>
        </div>
         <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>
        <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>

    </div>

    <!-- الفلترة والجدول -->
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
                               placeholder="ابحث باسم البنك أو مفتاح الطريقة..." />
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
                 wire:target="search,applyFilter,toggleStatus"
                 class="overflow-visible rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                
                <div class="max-w-full overflow-x-auto table-responsive-container">
                    <table class="min-w-full">
                        <!-- رأس الجدول -->
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 sm:px-6">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            اسم البنك / بوابة الدفع
                                        </p>
                                    </div>
                                </th>
                                <th class="px-5 py-3 sm:px-6">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            الحالة
                                        </p>
                                    </div>
                                </th>
                                <th class="px-5 py-3 sm:px-6">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            الخطوات المضافة
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
                        
                        <!-- جسم الجدول -->
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($this->banks as $bank)
                                <tr wire:key="bank-{{ $bank->id }}">
                                    <!-- البنك والشعار -->
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            @if($bank->logo)
                                                <!-- <img src="{{ url($bank->logo) }}" class="h-10 w-10 rounded-lg object-contain bg-gray-50 p-1 border dark:bg-gray-800 dark:border-gray-700" alt="logo"> -->
                                            @else
                                                <!-- <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10">
                                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7h20L12 2zm0 5H2v2h20V7H12z"/></svg>
                                                </div> -->
                                            @endif
                                            <div>
                                                <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                    {{ $bank->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- حالة التنشيط -->
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center">
                                            @if ($bank->is_active)
                                                <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                                    نشط
                                                </span>
                                            @else
                                                <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                                    متوقف
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- عدد الخطوات -->
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                {{ $bank->steps()->count() }} خطوات
                                            </p>
                                        </div>
                                    </td>
                                    
                                    <!-- عمود النقاط الثلاثة المحدث بالكامل -->
                                    <td class="px-6 py-4 text-center align-middle" x-data="{ openOptions: false }">
                                        <div class="flex relative justify-center items-center">
                                            <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                                                class="actions-trigger-btn">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="2"></circle>
                                                    <circle cx="12" cy="12" r="2"></circle>
                                                    <circle cx="12" cy="19" r="2"></circle>
                                                </svg>
                                            </button>

                                            <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                                                class="actions-dropdown-menu">

                                                <!-- إدارة الخطوات والحقول -->
                                                <a href=""
                                                    class="actions-dropdown-item">
                                                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    إدارة الخطوات والحقول
                                                </a>

                                                <!-- تفعيل / إيقاف التنشيط الفوري -->
                                                <button type="button" wire:click="toggleStatus({{ $bank->id }})"
                                                    class="actions-dropdown-item {{ $bank->is_active ? 'text-error-600 dark:text-error-500 hover:bg-error-50' : 'text-success-600 dark:text-success-500 hover:bg-success-50' }}">
                                                    @if($bank->is_active)
                                                        <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                        إيقاف التنشيط
                                                    @else
                                                        <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>
                                                        تفعيل الحساب
                                                    @endif
                                                </button>

                                                <!-- تعديل البيانات الأساسية -->
                                                <a href="{{ route('Bank.edit', $bank->id) }}" class="actions-dropdown-item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    تعديل البيانات الأساسية
                                                </a>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-2">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                                <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2L2 7h20L12 2zm0 5H2v2h20V7H12z"/>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                                لا توجد بنوك أو بوابات دفع مضافة حتى الآن
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- كود الصفحات المتطور والمنظم لقائمة البنوك -->
                    @if($this->banks->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    عرض {{ $this->banks->firstItem() ?? 0 }} إلى {{ $this->banks->lastItem() ?? 0 }} من {{ $this->banks->total() }} عنصر
                                </div>
                                
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    @if(!$this->banks->onFirstPage())
                                        <button wire:click="previousPage"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            السابق
                                        </button>
                                    @endif
                                    
                                    @php
                                        $current = $this->banks->currentPage();
                                        $last = $this->banks->lastPage();
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
                                    
                                    @if($this->banks->hasMorePages())
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