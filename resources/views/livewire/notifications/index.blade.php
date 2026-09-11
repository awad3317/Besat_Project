<div class="py-6 w-full">

    {{-- =========================================================
        رسائل الخطأ
    ========================================================== --}}
    @if (session()->has('error'))
        <div
            class="flex gap-3 items-start p-4 mb-6 rounded-xl border shadow-sm transition-all bg-error-50 border-error-200 dark:bg-error-500/10 dark:border-error-500/20 animate-fade-in">

            <svg class="mt-0.5 w-5 h-5 text-error-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>

            </svg>

            <div class="text-sm font-semibold leading-relaxed text-error-800 dark:text-error-400">
                {{ session('error') }}
            </div>

        </div>
    @endif


    {{-- =========================================================
        رسائل النجاح
    ========================================================== --}}
    @if (session()->has('success'))
        <div
            class="flex gap-3 items-center p-4 mb-6 rounded-xl border shadow-sm transition-all bg-success-50 border-success-200 dark:bg-success-500/10 dark:border-success-500/20 animate-fade-in">

            <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>

            </svg>

            <h3 class="text-sm font-bold text-success-800 dark:text-success-400">

                {{ session('success') }}

            </h3>

        </div>
    @endif



    {{-- =========================================================
        نموذج إرسال الإشعار
    ========================================================== --}}
    <form wire:submit.prevent="send" class="space-y-6">


        {{-- =====================================================
            كروت اختيار المستلمين
        ====================================================== --}}
        {{-- =========================================================
    كروت اختيار المستلمين
========================================================= --}}
        <div class="flex gap-5 w-full">

            {{-- ================= جميع المستلمين ================= --}}
            <label class="block flex-1 cursor-pointer group">

                <input type="radio" wire:model.live="target" value="all" class="sr-only">

                <div
                    class="flex flex-col justify-between w-full h-36 p-5
                   bg-white border rounded-2xl
                   dark:bg-gray-900
                   transition-all duration-200

                   {{ $target === 'all'
                       ? 'border-brand-500 shadow-sm'
                       : 'border-gray-200 dark:border-gray-800 hover:border-brand-300' }}">

                    {{-- الأيقونة --}}
                    <div class="flex justify-end">

                        <div
                            class="flex items-center justify-center w-10 h-10
                           rounded-xl bg-gray-100 dark:bg-gray-800
                           transition-colors

                           {{ $target === 'all' ? 'text-brand-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-brand-500' }}">

                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857
                               M17 20H7
                               m10 0v-2
                               c0-.656-.126-1.283-.356-1.857
                               M7 20H2v-2
                               a3 3 0 015.356-1.857
                               M7 20v-2
                               c0-.656.126-1.283.356-1.857
                               m0 0a5.002 5.002 0 019.288 0
                               M15 7a3 3 0 11-6 0
                               3 3 0 016 0">
                                </path>

                            </svg>

                        </div>

                    </div>


                    {{-- النص والعدد --}}
                    <div class="text-right">

                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            جميع المستلمين
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->allCount }}
                        </p>

                    </div>

                </div>

            </label>



            {{-- ================= السائقين ================= --}}
            <label class="block flex-1 cursor-pointer group">

                <input type="radio" wire:model.live="target" value="drivers" class="sr-only">

                <div
                    class="flex flex-col justify-between w-full h-36 p-5
                   bg-white border rounded-2xl
                   dark:bg-gray-900
                   transition-all duration-200

                   {{ $target === 'drivers'
                       ? 'border-brand-500 shadow-sm'
                       : 'border-gray-200 dark:border-gray-800 hover:border-brand-300' }}">

                    {{-- الأيقونة --}}
                    <div class="flex justify-end">

                        <div
                            class="flex items-center justify-center w-10 h-10
                           rounded-xl bg-gray-100 dark:bg-gray-800
                           transition-colors

                           {{ $target === 'drivers' ? 'text-brand-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-brand-500' }}">

                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 18.75a1.5 1.5 0 01-3 0
                               m3 0a1.5 1.5 0 00-3 0
                               m3 0h6
                               m-9 0H3.375
                               a1.125 1.125 0 01-1.125-1.125V14.25
                               m17.25 4.5a1.5 1.5 0 01-3 0
                               m3 0a1.5 1.5 0 00-3 0
                               m3 0h1.125
                               c.621 0 1.129-.504 1.09-1.124
                               a17.902 17.902 0 00-3.213-9.193
                               2.056 2.056 0 00-1.58-.86H14.25">
                                </path>

                            </svg>

                        </div>

                    </div>


                    {{-- النص والعدد --}}
                    <div class="text-right">

                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            السائقين
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->driversCount }}
                        </p>

                    </div>

                </div>

            </label>



            {{-- ================= العملاء ================= --}}
            <label class="block flex-1 cursor-pointer group">

                <input type="radio" wire:model.live="target" value="users" class="sr-only">

                <div
                    class="flex flex-col justify-between w-full h-36 p-5
                   bg-white border rounded-2xl
                   dark:bg-gray-900
                   transition-all duration-200

                   {{ $target === 'users'
                       ? 'border-brand-500 shadow-sm'
                       : 'border-gray-200 dark:border-gray-800 hover:border-brand-300' }}">

                    {{-- الأيقونة --}}
                    <div class="flex justify-end">

                        <div
                            class="flex items-center justify-center w-10 h-10
                           rounded-xl bg-gray-100 dark:bg-gray-800
                           transition-colors

                           {{ $target === 'users' ? 'text-brand-500' : 'text-gray-500 dark:text-gray-400 group-hover:text-brand-500' }}">

                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6
                               a3.75 3.75 0 11-7.5 0
                               3.75 3.75 0 017.5 0z
                               M4.501 20.118
                               a7.5 7.5 0 0114.998 0
                               A17.933 17.933 0 0112 21.75
                               c-2.676 0-5.216-.584-7.499-1.632z">
                                </path>

                            </svg>

                        </div>

                    </div>


                    {{-- النص والعدد --}}
                    <div class="text-right">

                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            العملاء
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->usersCount }}
                        </p>

                    </div>

                </div>

            </label>

        </div>


        @error('target')
            <span class="block mt-2 text-sm font-semibold text-error-500">
                {{ $message }}
            </span>
        @enderror


        {{-- خطأ اختيار الفئة --}}
        @error('target')
            <span class="block mt-2 text-sm font-semibold text-error-500">

                {{ $message }}

            </span>
        @enderror



        {{-- =====================================================
            كرت تفاصيل الإشعار
        ====================================================== --}}
        <div
            class="overflow-hidden bg-white rounded-2xl border border-gray-200 shadow-theme-sm dark:bg-gray-900 dark:border-gray-800">


            {{-- =================================================
                رأس البطاقة
            ================================================== --}}
            <div
                class="flex gap-4 items-center px-6 py-5 border-b border-gray-100 bg-gray-50/50 dark:bg-gray-800/30 dark:border-gray-800">


                {{-- أيقونة الإشعار --}}
                <div
                    class="flex justify-center items-center w-11 h-11 rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 shrink-0">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.405-1.405
                               A2.032 2.032 0 0118 14.158V11
                               a6.002 6.002 0 00-4-5.659V5
                               a2 2 0 10-4 0v.341
                               C7.67 6.165 6 8.388 6 11v3.159
                               c0 .538-.214 1.055-.595 1.436L4 17h5
                               m6 0v1a3 3 0 11-6 0v-1
                               m6 0H9">
                        </path>

                    </svg>

                </div>


                <div>

                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">

                        تفاصيل الإشعار

                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">

                        اكتب محتوى الإشعار الذي سيصل إلى الفئة المحددة أعلاه.

                    </p>

                </div>


            </div>



            {{-- =================================================
                الحقول
            ================================================== --}}
            <div class="p-6 space-y-6 md:p-8">


                {{-- عنوان الإشعار --}}
                <div>

                    <label for="title" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">

                        عنوان الإشعار

                        <span class="text-error-500">*</span>

                    </label>


                    <input type="text" id="title" wire:model="title"
                        class="px-4 py-3 w-full text-sm text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all placeholder:text-gray-400 focus:bg-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500/10 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder:text-gray-500 dark:focus:bg-gray-900 dark:focus:border-brand-500"
                        placeholder="مثال: عرض خاص بمناسبة العيد!">


                    @error('title')
                        <span class="flex gap-1 items-center mt-2 text-xs font-semibold text-error-500">

                            {{ $message }}

                        </span>
                    @enderror

                </div>



                {{-- محتوى الإشعار --}}
                <div>

                    <label for="body" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">

                        محتوى الإشعار

                        <span class="text-error-500">*</span>

                    </label>


                    <textarea id="body" wire:model="body" rows="5"
                        class="px-4 py-3 w-full text-sm leading-7 text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all resize-y placeholder:text-gray-400 focus:bg-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500/10 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder:text-gray-500 dark:focus:bg-gray-900 dark:focus:border-brand-500"
                        placeholder="اكتب التفاصيل التي ستظهر في الإشعار للمستخدمين..."></textarea>


                    @error('body')
                        <span class="flex gap-1 items-center mt-2 text-xs font-semibold text-error-500">

                            {{ $message }}

                        </span>
                    @enderror

                </div>


            </div>



            {{-- =================================================
                Footer
            ================================================== --}}
            <div
                class="flex justify-end items-center px-6 py-4 bg-gray-50 border-t border-gray-100 dark:bg-gray-800 dark:border-gray-800">


                <button type="submit" wire:loading.attr="disabled" wire:target="send"
                    class="
                        flex items-center justify-center
                        gap-2
                        px-12 py-3
                        w-full
                        md:w-auto
                        min-w-[220px]

                        text-sm
                        font-bold
                        text-white

                        rounded-xl
                        shadow-sm

                        bg-brand-500

                        transition-all
                        duration-200

                        hover:bg-brand-600
                        hover:shadow-md

                        focus:ring-2
                        focus:ring-offset-2
                        focus:ring-brand-500

                        disabled:opacity-70
                        disabled:cursor-not-allowed

                        dark:focus:ring-offset-gray-900
                    ">


                    {{-- الحالة الطبيعية --}}
                    <span wire:loading.remove wire:target="send" class="flex gap-2 items-center">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                            </path>

                        </svg>

                        إرسال الإشعار

                    </span>


                    {{-- أثناء الإرسال --}}
                    <span wire:loading wire:target="send" class="flex gap-2 items-center">

                        <svg class="w-4 h-4 text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">

                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>

                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0
                                   C5.373 0 0 5.373 0 12h4
                                   zm2 5.291A7.962 7.962 0 014 12H0
                                   c0 3.042 1.135 5.824 3 7.938
                                   l3-2.647z">
                            </path>

                        </svg>

                        جاري الإرسال...

                    </span>


                </button>


            </div>


        </div>


    </form>

</div>
