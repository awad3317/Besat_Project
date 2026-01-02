@if ($paginator->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
            <div class="flex items-center space-x-1 rtl:space-x-reverse">

                {{-- زر السابق --}}
                @if ($paginator->onFirstPage())
                    <button disabled
                            class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                        السابق
                    </button>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        السابق
                    </button>
                @endif

                {{-- أرقام الصفحات --}}
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);

                    // هذا يضمن عرض 5 أزرار للصفحات كحد أدنى، مع نقاط (...) إذا لزم الأمر
                    if ($end - $start < 4 && $last > 4) { // فقط إذا كان هناك ما يكفي من الصفحات
                        if ($current <= 3) { // إذا كنت في الصفحات الأولى
                            $start = 1;
                            $end = min($last, 5);
                        } elseif ($current >= $last - 2) { // إذا كنت في الصفحات الأخيرة
                            $start = max(1, $last - 4);
                            $end = $last;
                        } else { // في المنتصف
                            $start = $current - 2;
                            $end = $current + 2;
                        }
                    }
                @endphp

                {{-- زر الصفحة الأولى (مع ...) إذا لم تكن ضمن النطاق) --}}
                @if($start > 1)
                    <button wire:click="gotoPage(1)" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">1</button>
                    @if($start > 2)
                        <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">...</span>
                    @endif
                @endif

                {{-- أزرار الصفحات في النطاق المحدد --}}
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-sm font-medium text-white bg-brand-500 dark:bg-brand-500 rounded-md">
                            {{ $page }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endfor

                {{-- زر الصفحة الأخيرة (مع ...) إذا لم تكن ضمن النطاق) --}}
                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">...</span>
                    @endif
                    <button wire:click="gotoPage({{ $last }})" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">{{ $last }}</button>
                @endif

                {{-- زر التالي --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        التالي
                    </button>
                @else
                    <button disabled
                            class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                        التالي
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
