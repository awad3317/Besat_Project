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
                <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 10h3v7H4zm6.5 0h3v7h-3zM17 10h3v7h-3zm-15 8h20v2H2zm10-17L1 6v2h22V6z"/>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي البنوك المتاحة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['total'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div wire:click.debounce.150ms="applyFilter('stopped')"
             wire:loading.class="opacity-50"
             wire:target="applyFilter"
             class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] 
                    @if ($activeFilter == 'stopped') border border-brand-500 dark:border-brand-500 @endif">
            
            <div wire:loading wire:target="applyFilter('stopped')" 
                 class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
            </div>
            
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">الحسابات المتوقفة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['stopped'] ?? 0 }}
                </h4>
            </div>
        </div>

        <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]"></div>
        <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]"></div>
    </div>

    <div class="space-y-5 sm:space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <div class="flex flex-col sm:flex-row gap-4 items-end justify-between">
                    <div class="flex-1 min-w-[250px]">
                        <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">البحث في الحسابات</label>
                        <div class="relative">
                            <input wire:model.live="search"
                                   type="text"
                                   class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                                   placeholder="ابحث باسم البنك، صاحب الحساب، رقم الحساب..." />
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
                <div wire:loading.class="opacity-50" 
                     wire:target="search,applyFilter,toggleStatus"
                     class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-5 py-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">البنك والمستفيد</th>
                                    <th class="px-5 py-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الحساب</th>
                                    <th class="px-5 py-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">حالة التنشيط</th>
                                    <th class="px-5 py-3 text-center font-medium text-gray-500 text-theme-xs dark:text-gray-400">الإجراءات</th>
                                </tr>
                            </thead>
                            
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($this->banks as $bank)
                                    <tr wire:key="bank-{{ $bank->id }}">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($bank->logo)
                                                    <img src="{{ url($bank->logo) }}" class="h-10 w-10 rounded-lg object-contain bg-gray-50 p-1 border dark:bg-gray-800 dark:border-gray-700" alt="logo">
                                                @else
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10">
                                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7h20L12 2zm0 5H2v2h20V7H12zm-8 4h3v7H4v-7zm6.5 0h3v7h-3v-7zM17 10h3v7h-3v-7zm-15 8h20v2H2v-2z"/></svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $bank->name }}</span>
                                                    <span class="block text-gray-400 text-theme-xs">{{ $bank->account_name ?? 'لا يوجد اسم مستفيد' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <span class="text-gray-600 font-mono text-sm dark:text-gray-300">{{ $bank->account_number }}</span>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <div x-data="{ isActive: {{ $bank->is_active ? 'true' : 'false' }} }"
                                                 wire:key="toggle-bank-{{ $bank->id }}-{{ $bank->is_active }}">
                                                <label :for="'toggle_bank_' + {{ $bank->id }}"
                                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                                    <div class="relative">
                                                        <input type="checkbox" 
                                                               :id="'toggle_bank_' + {{ $bank->id }}"
                                                               class="sr-only" 
                                                               x-model="isActive"
                                                               @change="$wire.toggleStatus({{ $bank->id }})" />
                                                        
                                                        <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                                             :class="isActive ? 'bg-success-500' : 'bg-gray-300 dark:bg-gray-700'">
                                                        </div>
                                                        <div :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                                             class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-300 ease-linear">
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('Bank.edit', $bank->id) }}"
                                                   class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    تعديل
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-6">
                                            <div class="flex flex-col items-center justify-center text-center">
                                                <div class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                                    <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2L2 7h20L12 2zm0 5H2v2h20V7H12z"/></svg>
                                                </div>
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">لا توجد بنوك مضافة في النظام حتى الآن</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        @if($this->banks->hasPages())
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        عرض {{ $this->banks->firstItem() ?? 0 }} إلى {{ $this->banks->lastItem() ?? 0 }} من {{ $this->banks->total() }} بنك
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        @if(!$this->banks->onFirstPage())
                                            <button wire:click="previousPage" wire:loading.attr="disabled" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">السابق</button>
                                        @endif
                                        
                                        @php
                                            $current = $this->banks->currentPage();
                                            $last = $this->banks->lastPage();
                                            $start = max(1, $current - 2);
                                            $end = min($last, $current + 2);
                                        @endphp
                                        
                                        @for($page = $start; $page <= $end; $page++)
                                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $page == $current ? 'bg-brand-500 text-white' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700' }}">{{ $page }}</button>
                                        @endfor
                                        
                                        @if($this->banks->hasMorePages())
                                            <button wire:click="nextPage" wire:loading.attr="disabled" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">التالي</button>
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