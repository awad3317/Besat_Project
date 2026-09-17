<div class="mt-6">
    <!-- إحصائيات السائق الماليـة (البطاقة الثانية في صفحة التفاصيل) -->
    @if($this->stats)
    <div class="flex flex-col flex-wrap gap-4 mb-6 sm:flex-row md:gap-6">
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-lg dark:bg-gray-800">
               <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->total_trips }}
                </h4>
            </div>
        </div>
        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-lg dark:bg-gray-800">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الأرباح الصافية</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($this->stats['financial']['total_earnings'], 2) }} ري
                </h4>
            </div>
        </div>

        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-error-100 dark:border-error-500/20 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-error-50 dark:bg-error-500/10">
                <svg class="w-5 h-5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-error-500 dark:text-error-400">مديونية السائق للتطبيق</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($this->stats['financial']['driver_owes_app'], 2) }} ري
                </h4>
            </div>
        </div>

        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-success-100 dark:border-success-500/20 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-success-50 dark:bg-success-500/10">
                <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-success-500 dark:text-success-400">مستحقات السائق</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($this->stats['financial']['app_owes_driver'], 2) }} ري
                </h4>
            </div>
        </div>

        <div class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border @if($this->stats['financial']['net_balance'] < 0) border-error-500 @elseif($this->stats['financial']['net_balance'] > 0) border-success-500 @else border-gray-200 dark:border-gray-800 @endif dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 @if($this->stats['financial']['net_balance'] < 0) bg-error-50 dark:bg-error-500/10 @elseif($this->stats['financial']['net_balance'] > 0) bg-success-50 dark:bg-success-500/10 @else bg-gray-50 dark:bg-gray-800 @endif rounded-lg">
                <svg class="w-5 h-5 @if($this->stats['financial']['net_balance'] < 0) text-error-500 @elseif($this->stats['financial']['net_balance'] > 0) text-success-500 @else text-gray-500 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">صافي التصفية (الحالي)</span>
                <h4 class="mt-1 text-lg font-bold @if($this->stats['financial']['net_balance'] < 0) text-error-600 dark:text-error-400 @elseif($this->stats['financial']['net_balance'] > 0) text-success-600 dark:text-success-400 @else text-gray-800 dark:text-white/90 @endif">
                    {{ number_format(abs($this->stats['financial']['net_balance']), 2) }} ري
                    @if($this->stats['financial']['net_balance'] < 0)
                        <span class="text-xs font-normal">(مستحق للتطبيق)</span>
                    @elseif($this->stats['financial']['net_balance'] > 0)
                        <span class="text-xs font-normal">(مستحق للسائق)</span>
                    @else
                        <span class="text-xs font-normal">(متوازن)</span>
                    @endif
                </h4>
            </div>
        </div>
    </div>
    @endif

    <!-- جدول تصفيات الحسابات -->
    <div class="p-5 mb-6 rounded-2xl border border-gray-200 shadow-sm bg-dark-800 dark:border-gray-800 dark:bg-dark-800 lg:p-6">
        <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
            
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-500 text-brand-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        تصفيات الحسابات
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">سجل عمليات تسوية الحساب المالي للسائق</p>
                </div>
            </div>
            
            <div class="flex gap-4 items-center">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي التصفيات <span class="font-bold text-gray-800 dark:text-white">{{ $this->settlements->total() }}</span></span>
                
                @if($unsettledStats['trips_count'] > 0 && $unsettledStats['net_balance'] != 0)
                    <button wire:click="openSettlementModal" 
                        class="inline-flex gap-2 justify-center items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:bg-brand-500 dark:hover:bg-brand-500">
                        تصفية الحساب
                    </button>
                @else
                    <button disabled
                        class="inline-flex gap-2 justify-center items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">
                        تصفية الحساب
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-right">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">رقم العملية</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">المسؤول عن التصفية</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">عدد الرحلات</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">على السائق</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">للسائق</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">صافي التصفية</th>
                        <th class="px-4 py-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">اتجاه التصفية</th>
                        <th class="px-4 py-4 text-sm font-medium text-left text-gray-500 dark:text-gray-400">تاريخ التصفية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->settlements as $settlement)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-gray-800 dark:text-white/90">#{{ $settlement->id }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex flex-col justify-center items-center">
                                <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $settlement->admin ? $settlement->admin->name : 'نظام' }}</span>
                                <div class="flex gap-1 items-center mt-1 text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-xs">مسؤول النظام</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $settlement->trips_count }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-bold text-error-600 dark:text-error-400">{{ number_format($settlement->driver_owes_app, 2) }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-bold text-success-600 dark:text-success-400">{{ number_format($settlement->app_owes_driver, 2) }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex flex-col justify-center items-center">
                                <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ number_format(abs($settlement->net_balance), 2) }}</span>
                                <span class="mt-1 text-xs text-gray-400">صافي الحساب</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($settlement->settlement_action == 'driver_pays_app')
                                <div class="flex gap-2 justify-center items-center">
                                    <span class="text-xs font-bold text-error-600 dark:text-error-400">مستحق للتطبيق</span>
                                    <svg class="w-4 h-4 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            @else
                                <div class="flex gap-2 justify-center items-center">
                                    <span class="text-xs font-bold text-success-600 dark:text-success-400">مستحق للسائق</span>
                                    <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-left">
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $settlement->created_at->format('Y/m/d') }}</span>
                                <span class="mt-1 text-xs text-gray-400">{{ $settlement->created_at->format('A h:i') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center">
                            <div class="flex flex-col justify-center items-center text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    لا توجد تصفيات سابقة
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($this->settlements->count() > 0)
            <div class="flex justify-center items-center mt-6 w-full">
               
            </div>
            @endif

            <div class="mt-4">
                {{ $this->settlements->links() }}
            </div>
        </div>
    </div>

    <!-- نافذة تأكيد التصفية (Modal) باستخدام Alpine.js بنمط إضافة مستخدم -->
    <div x-data="{ isModalOpen: @entangle('showSettlementModal'), isLoading: false }">
        <div x-show="isModalOpen" class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999" style="display: none;">
            <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
    
            <div @click.outside="isModalOpen = false" x-show="isModalOpen" x-transition class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10 text-right">
                
                <form wire:submit.prevent="confirmSettlement" @submit="isLoading = true">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            تأكيد تصفية الحساب
                        </h4>
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 transition hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
    
                    <div class="p-6 mb-6 bg-gray-50 rounded-2xl dark:bg-gray-800">
                        <p class="mb-6 text-sm text-center text-gray-600 dark:text-gray-400">
                            هل أنت متأكد من رغبتك في إجراء عملية التصفية للسائق؟ سيتم تصفير الرصيد الحالي وتسجيل العملية.
                        </p>
    
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">على السائق (كاش):</span>
                                <span class="text-lg font-bold text-error-600 dark:text-error-400">{{ number_format($unsettledStats['driver_owes_app'], 2) }} ري</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">للسائق (إلكتروني):</span>
                                <span class="text-lg font-bold text-success-600 dark:text-success-400">{{ number_format($unsettledStats['app_owes_driver'], 2) }} ري</span>
                            </div>
                            
                            <div class="my-4 w-full h-px bg-gray-200 dark:bg-gray-700"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-800 dark:text-gray-200">الصافي:</span>
                                <div class="text-left">
                                    <span class="text-xl font-bold @if($unsettledStats['net_balance'] < 0) text-error-600 dark:text-error-400 @elseif($unsettledStats['net_balance'] > 0) text-success-600 dark:text-success-400 @else text-gray-800 dark:text-gray-200 @endif">
                                        {{ number_format(abs($unsettledStats['net_balance']), 2) }} ري
                                    </span>
                                    <div class="text-xs mt-1 @if($unsettledStats['net_balance'] < 0) text-error-500 dark:text-error-400 @elseif($unsettledStats['net_balance'] > 0) text-success-500 dark:text-success-400 @else text-gray-500 dark:text-gray-400 @endif">
                                        @if($unsettledStats['net_balance'] < 0) (مستحق للتطبيق) @elseif($unsettledStats['net_balance'] > 0) (مستحق للسائق) @else (متوازن) @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="flex gap-3 justify-end items-center mt-8 w-full">
                        <button @click="isModalOpen = false" type="button"
                            class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="submit" wire:loading.attr="disabled" :disabled="isLoading"
                            class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-500 bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed">
                            <svg wire:loading wire:target="confirmSettlement" class="w-5 h-5 text-white animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="confirmSettlement">تأكيد التصفية</span>
                            <span wire:loading wire:target="confirmSettlement">جاري التصفية...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
