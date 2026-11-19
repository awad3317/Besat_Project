{{-- نافذة إلغاء الرحلة مع إدخال السبب --}}
<div x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail == 'cancel-request-modal' || $event.detail?.[0] == 'cancel-request-modal') { show = true; }"
    x-on:close-modal.window="if ($event.detail == 'cancel-request-modal' || $event.detail?.[0] == 'cancel-request-modal') { show = false; }"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-4 modal"
    style="display: none;">

    <style>
        .cancel-confirm-btn {
            background-color: #f04438;
            color: #ffffff;
        }
        .cancel-confirm-btn:hover:not(:disabled) {
            background-color: #d92d20;
        }
        .cancel-confirm-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    {{-- الخلفية الضبابية --}}
    <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

    {{-- محتوى النافذة --}}
    <div @click.outside="show = false"
        class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
        dir="rtl">

        <div class="text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-error-50 text-error-500 dark:bg-error-500/15 dark:text-error-500">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                تأكيد إلغاء الرحلة #{{ $cancellingRequestId }}
            </h4>

            <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
                يرجى توضيح سبب إلغاء هذه الرحلة لحفظه في سجل العمليات (اللوق).
            </p>
        </div>

        <form wire:submit.prevent="cancelRequest" class="flex flex-col gap-4">
            <div>
                <label class="mb-1.5 block text-right text-sm font-medium text-gray-700 dark:text-gray-400">
                    سبب الإلغاء <span class="text-error-500">*</span>
                </label>

                <textarea wire:model="cancellationReason" rows="3"
                    placeholder="اكتب سبب إلغاء الرحلة هنا..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-right text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-error-300 focus:outline-hidden focus:ring-3 focus:ring-error-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-error-800"></textarea>

                @error('cancellationReason')
                    <span class="mt-1 block text-right text-xs font-medium text-error-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2 flex items-center justify-center gap-3">
                <button type="button" @click="show = false"
                    class="flex-1 cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    تراجع
                </button>

                <button type="submit" wire:loading.attr="disabled"
                    class="cancel-confirm-btn flex-1 cursor-pointer rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition">
                    <span wire:loading.remove wire:target="cancelRequest">تأكيد الإلغاء</span>
                    <span wire:loading wire:target="cancelRequest">جاري الإلغاء...</span>
                </button>
            </div>
        </form>
    </div>
</div>
