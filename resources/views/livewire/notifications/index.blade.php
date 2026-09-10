<div class="py-6 mx-auto max-w-5xl">
    
    <!-- رسائل التنبيه (تظهر خارج البطاقة أو داخلها حسب تفضيلك، هنا جعلناها مدمجة وأنيقة) -->
    @if (session()->has('error'))
        <div class="flex gap-3 items-start p-4 mb-6 rounded-xl border shadow-sm transition-all bg-error-50 dark:bg-error-500/10 border-error-200 dark:border-error-500/20 animate-fade-in">
            <svg class="mt-0.5 w-5 h-5 text-error-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="text-sm font-semibold leading-relaxed text-error-800 dark:text-error-400">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="flex gap-3 items-center p-4 mb-6 rounded-xl border shadow-sm transition-all bg-success-50 dark:bg-success-500/10 border-success-200 dark:border-success-500/20 animate-fade-in">
            <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-sm font-bold text-success-800 dark:text-success-400">{{ session('success') }}</h3>
        </div>
    @endif

    <!-- الحاوية الرئيسية -->
    <div class="overflow-hidden bg-white rounded-2xl border border-gray-200 dark:bg-gray-900 shadow-theme-sm dark:border-gray-800">
        
        <!-- الترويسة -->
        <div class="flex gap-4 items-center px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex justify-center items-center w-12 h-12 rounded-xl shadow-sm bg-brand-50 dark:bg-gray-800 text-brand-500 dark:text-brand-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">إرسال إشعار جديد</h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">قم بصياغة رسالتك وحدد الفئة التي ترغب بإرسال الإشعار إليها.</p>
            </div>
        </div>

        <form wire:submit.prevent="send">
            <!-- جسم النموذج -->
            <div class="p-6 space-y-8 md:p-8">
                
                <!-- الشريحة المستهدفة -->
                <div>
                    <label class="block mb-4 text-sm font-bold text-gray-700 dark:text-gray-300">
                        الشريحة المستهدفة <span class="text-error-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        
                        <!-- الجميع -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" wire:model.live="target" value="all" class="sr-only">
                            <div class="flex flex-col items-center justify-center p-5 text-center rounded-xl border-2 transition-all duration-200 {{ $target === 'all' ? 'border-brand-500 bg-brand-50/50 dark:border-brand-500 dark:bg-brand-900/20' : 'border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="w-8 h-8 mb-3 {{ $target === 'all' ? 'text-brand-500' : 'text-gray-400 group-hover:text-brand-400 dark:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="block text-sm font-bold {{ $target === 'all' ? 'text-brand-700 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">الجميع</span>
                                <span class="mt-1 block text-xs {{ $target === 'all' ? 'text-brand-500 dark:text-brand-500/70' : 'text-gray-400 dark:text-gray-500' }}">السائقين والعملاء</span>
                            </div>
                        </label>

                        <!-- السائقين فقط -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" wire:model.live="target" value="drivers" class="sr-only">
                            <div class="flex flex-col items-center justify-center p-5 text-center rounded-xl border-2 transition-all duration-200 {{ $target === 'drivers' ? 'border-brand-500 bg-brand-50/50 dark:border-brand-500 dark:bg-brand-900/20' : 'border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="w-8 h-8 mb-3 {{ $target === 'drivers' ? 'text-brand-500' : 'text-gray-400 group-hover:text-brand-400 dark:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                </svg>
                                <span class="block text-sm font-bold {{ $target === 'drivers' ? 'text-brand-700 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">السائقين فقط</span>
                                <span class="mt-1 block text-xs {{ $target === 'drivers' ? 'text-brand-500 dark:text-brand-500/70' : 'text-gray-400 dark:text-gray-500' }}">تنبيهات وإعلانات للسائقين</span>
                            </div>
                        </label>

                        <!-- العملاء فقط -->
                        <label class="relative cursor-pointer group">
                            <input type="radio" wire:model.live="target" value="users" class="sr-only">
                            <div class="flex flex-col items-center justify-center p-5 text-center rounded-xl border-2 transition-all duration-200 {{ $target === 'users' ? 'border-brand-500 bg-brand-50/50 dark:border-brand-500 dark:bg-brand-900/20' : 'border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="w-8 h-8 mb-3 {{ $target === 'users' ? 'text-brand-500' : 'text-gray-400 group-hover:text-brand-400 dark:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span class="block text-sm font-bold {{ $target === 'users' ? 'text-brand-700 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">العملاء فقط</span>
                                <span class="mt-1 block text-xs {{ $target === 'users' ? 'text-brand-500 dark:text-brand-500/70' : 'text-gray-400 dark:text-gray-500' }}">عروض ترويجية للعملاء</span>
                            </div>
                        </label>

                    </div>
                    @error('target') <span class="flex gap-1 items-center mt-2 text-xs font-semibold text-error-500"><i class="fas fa-info-circle"></i> {{ $message }}</span> @enderror
                </div>

                <hr class="border-gray-100 dark:border-gray-800">

                <!-- عنوان ومحتوى الإشعار -->
                <div class="space-y-5">
                    <div>
                        <label for="title" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-50">
                            عنوان الإشعار <span class="text-error-500">*</span>
                        </label>
                        <input type="text" id="title" wire:model="title" 
                            class="px-4 py-3 w-full text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:bg-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:text-white dark:focus:bg-gray-900 placeholder:text-gray-400" 
                            placeholder="مثال: عرض خاص بمناسبة العيد!">
                        @error('title') <span class="flex gap-1 items-center mt-2 text-xs font-semibold text-error-500"><i class="fas fa-info-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="body" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            محتوى الإشعار <span class="text-error-500">*</span>
                        </label>
                        <textarea id="body" wire:model="body" rows="4" 
                            class="px-4 py-3 w-full text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:bg-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:text-white dark:focus:bg-gray-900 placeholder:text-gray-400" 
                            placeholder="اكتب التفاصيل التي ستظهر في الإشعار للمستخدمين..."></textarea>
                        @error('body') <span class="flex gap-1 items-center mt-2 text-xs font-semibold text-error-500"><i class="fas fa-info-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <!-- الفوتر: زر الإرسال -->
            <div class="flex justify-end items-center px-5 py-4 bg-gray-50 border-t border-gray-100 dark:bg-gray-800 dark:border-gray-800">
                <button type="submit" wire:loading.attr="disabled"
                    class="flex justify-center items-center px-12 py-3 w-full text-sm font-bold text-white rounded-xl shadow-sm transition-all duration-200 bg-brand-500 hover:bg-brand-600 hover:shadow focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-70 disabled:cursor-not-allowed dark:focus:ring-offset-gray-900">
                    
                    <!-- أيقونة الإرسال (تختفي وقت التحميل) -->
                    <span wire:loading.remove wire:target="send" class="flex gap-2 items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        إرسال الإشعار الآن
                    </span>

                    <!-- التحميل (يظهر وقت الإرسال) -->
                    <span wire:loading wire:target="send" class="flex gap-2 items-center">
                        <svg class="w-4 h-4 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري المعالجة والإرسال...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>