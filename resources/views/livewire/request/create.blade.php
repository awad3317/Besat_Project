<div class="grid grid-cols-1 gap-x-6 gap-y-5 mt-2 sm:grid-cols-2">
    <!-- عنوان الرحلة -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">عنوان الرحلة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>

        <input type="text" placeholder="مثال: رحلة خاصة" name="title"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('title')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Column for the Customer Search Component --}}
    <div class="col-span-1 relative">

        {{-- Label for the search input --}}
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            ابحث عن العميل (بالاسم أو رقم الجوال)
        </label>

        {{-- Flex container for the input and the button --}}
        <div class="flex gap-2">

            {{-- The search input field --}}
            <input id="customer_search" type="tel" placeholder="أبحث برقم الهاتف أو الاسم" {{-- This is the crucial
                part: binding to the correct properties for live search --}}
                wire:model.live.debounce.300ms="customer_phone" autocomplete="off" @if($selected_customer_id) disabled
                @endif
                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed dark:disabled:bg-gray-800">

            {{-- Reset Button (Shows when a customer is selected) --}}
            @if($selected_customer_id)
                <button type="button" wire:click="resetCustomer"
                    class="flex justify-center hover:bg-brand-600 px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500"
                    title="إعادة تعيين">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            @else
                {{-- Create User Button (Shows when no customer is selected) --}}
                <a href="{{ route('users.create') }}"
                    class="flex justify-center hover:bg-brand-600 px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500"
                    title="البحث يعمل تلقائياً أثناء الكتابة">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            @endif
        </div>

        {{-- Display validation rrors for the search input --}}
        @error('customer_phone')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror

        {{-- The dropdown list for search results --}}
        @if (!empty($customers_list))
            <div
                class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60">
                <div>
                    @forelse($customers_list as $customer)
                        {{-- When a customer is clicked, call the selectCustomer method --}}
                        <div wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->phone }}','{{$customer->name}}')"
                            class="flex items-center gap-3 p-2 px-4 transition-colors duration-150 border-b cursor-pointer border-gray-200/50 dark:border-gray-700/50 hover:bg-sky-50 dark:hover:bg-gray-700">

                            {{-- Avatar with initials --}}
                            <div
                                class="flex items-center justify-center flex-shrink-0 w-8 h-8 font-bold  bg-sky-100 rounded-full dark:bg-sky-900/50 text-warning-500 dark:text-warning/90">
                                {{-- Assuming you have a getInitials() method on your User model --}}
                                {{ Str::upper(Str::substr($customer->name, 0, 2)) }}
                            </div>

                            {{-- Name and Phone --}}
                            <div class="flex-grow">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $customer->name }}
                                </p>
                                <p class="text-xs tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ $customer->phone }}
                                </p>
                            </div>

                            {{-- Optional selection icon --}}
                            <div class="flex-shrink-0">
                                <i class="text-xs text-gray-400 fa-solid fa-arrow-up-left"></i>
                            </div>

                        </div>
                    @empty
                        <div class="p-3 text-sm text-center text-gray-500">لا توجد نتائج مطابقة</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
    <input type="hidden" name="user_id" id="user_id" value="{{$selected_customer_id}}">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اختيار المركبة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <select id="vehicle_id" name="vehicle_id"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <option value=''>اختر المركبه</option>
            @foreach ($this->vehicles as $vehicle)
                <option class="" value="{{ $vehicle->id }}">{{ $vehicle->type }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">طريقة الدفع <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <select name="payment_method"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <option value="cash" selected>كاش</option>
            <option value="deposit">ايداع بنكي</option>
        </select>
    </div>

    <div class="col-span-1">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">كوبون الخصم (اختياري)</label>
        <div class="flex gap-2">
            <input id="discount_code" type="text" name="discount_code" wire:model.defer="coupon_code"
                placeholder="أدخل كود الخصم" value=' '
                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <button type="button" wire:click="applyCoupon" wire:loading.attr="disabled"
                class="flex justify-center hover:bg-brand-600 px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
                <span wire:loading.remove wire:target="applyCoupon">تحقق</span>
                <span wire:loading wire:target="applyCoupon">جاري...</span>
            </button>
        </div>
        @if ($coupon_message)
            <p class="mt-1 text-l text-warning-500 dark:text-warning/90">
                {{ $coupon_message }}
            </p>
        @endif
    </div>
    <!-- الوصف -->
    <div class="col-span-1">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الوصف</label>
        <textarea placeholder="أدخل وصفاً للرحلة" name="notes"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
    </div>

</div>