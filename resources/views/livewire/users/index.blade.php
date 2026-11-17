<div>

   @if ($passwordSuccessMessage)
       <div class="flex gap-3 justify-between items-center p-4 mb-4 rounded-xl border shadow-sm bg-success-50 border-success-200 dark:bg-success-500/10 dark:border-success-500/20">
           <div class="flex gap-3 items-center">
               <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               <span class="text-sm font-semibold text-success-800 dark:text-success-400">
                   {{ $passwordSuccessMessage }}
               </span>
           </div>
           <button type="button" wire:click="$set('passwordSuccessMessage', '')"
               class="text-xs font-medium text-success-700 hover:underline dark:text-success-400">
               إغلاق
           </button>
       </div>
   @endif

   <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        
    {{-- كارت إجمالي المستخدمين --}}
    <div wire:click="applyFilter('all')"
         wire:loading.class="opacity-50"
         wire:target="applyFilter('all')"
         class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] 
                @if ($activeFilter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
        
        <div wire:loading wire:target="applyFilter('all')" 
             class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
            <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
        </div>
        
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
            <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div class="mt-3 w-full">
            <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المستخدمين</span>
            <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                {{ $this->stats['total'] ?? 0 }}
            </h4>
        </div>
    </div>

    {{-- كارت المحظورين --}}
    <div wire:click="applyFilter('banned')"
         wire:loading.class="opacity-50"
         wire:target="applyFilter('banned')"
         class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] 
                @if ($activeFilter == 'banned') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
        
        <div wire:loading wire:target="applyFilter('banned')" 
             class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
            <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
        </div>
        
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc6803" stroke-width="2">
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="5.64" y1="5.64" x2="18.36" y2="18.36"></line>
            </svg>
        </div>
        <div class="mt-3 w-full">
            <span class="text-xs text-gray-500 dark:text-gray-400">المحظورين</span>
            <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                {{ $this->stats['banned'] ?? 0 }}
            </h4>
        </div>
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
                     class="overflow-visible rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    
                    <div class="max-w-full overflow-x-auto table-responsive-container">
                        <table class="min-w-full">
                            <!-- رأس الجدول -->
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                رقم المستخدم
                                            </p>
                                        </div>
                                    </th>
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
                                        <!-- رقم المستخدم -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $user->id }}
                                                </p>
                                            </div>
                                        </td>

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
                                        
                                        <!-- حالة الحظر -->
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                @if ($user->is_banned)
                                                    <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                                        محظور
                                                    </span>
                                                @else
                                                    <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                                        نشط
                                                    </span>
                                                @endif
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

                                                    <!-- عرض التفاصيل -->
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                        class="actions-dropdown-item">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        عرض التفاصيل
                                                    </a>

                                                    @if (Auth::user()?->type === 'superAdmin')
                                                        <!-- تغيير كلمة المرور -->
                                                        <button type="button"
                                                            wire:click="openPasswordModal({{ $user->id }})"
                                                            @click="openOptions = false"
                                                            class="actions-dropdown-item">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                                            </svg>
                                                            تغيير كلمة المرور
                                                        </button>
                                                    @endif

                                                    <!-- الحظر / فك الحظر -->
                                                    @if($user->is_banned)
                                                        <button type="button" wire:click="toggleBan({{ $user->id }})"
                                                            class="actions-dropdown-item text-success-600 dark:text-success-500 hover:bg-success-50 dark:hover:bg-success-950/20">
                                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                            </svg>
                                                            فك الحظر
                                                        </button>
                                                    @else
                                                        <button type="button" wire:click="toggleBan({{ $user->id }})"
                                                            class="actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                            <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                            حظر
                                                        </button>
                                                    @endif

                                                </div>
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

    @if (Auth::user()?->type === 'superAdmin')
        {{-- مودال تغيير كلمة المرور (للسوبر أدمن فقط) --}}
        <div x-data="{ isOpen: false, showPassword: false }"
            x-on:open-user-password-modal.window="showPassword = false; isOpen = true"
            x-on:close-user-password-modal.window="isOpen = false"
            x-show="isOpen"
            class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
            style="display: none;">
            <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]"
                @click="isOpen = false"></div>

            <div class="relative w-full max-w-md rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8 shadow-xl" dir="rtl">
                <form wire:submit.prevent="updatePassword">
                    <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        تغيير كلمة المرور
                    </h4>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                        تعيين كلمة مرور جديدة للحساب: <span class="font-semibold text-gray-800 dark:text-white/90">{{ $passwordModalUserName }}</span>
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                كلمة المرور الجديدة <span class="text-xs text-warning-500">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" wire:model="newPassword"
                                    autocomplete="new-password" placeholder="أدخل 8 أحرف على الأقل"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="flex absolute inset-y-0 left-0 items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            @error('newPassword')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                تأكيد كلمة المرور <span class="text-xs text-warning-500">*</span>
                            </label>
                            <input :type="showPassword ? 'text' : 'password'" wire:model="newPasswordConfirmation"
                                autocomplete="new-password" placeholder="أعد كتابة كلمة المرور"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white" />
                            @error('newPasswordConfirmation')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end items-center mt-6 w-full">
                        <button @click="isOpen = false" type="button"
                            class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                            class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-600 bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed sm:w-auto">
                            <span wire:loading wire:target="updatePassword"
                                class="w-4 h-4 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                            <span>حفظ كلمة المرور</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
