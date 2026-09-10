<div class="grid grid-cols-1 gap-y-5 gap-x-6 mt-2 sm:grid-cols-2">
    <!-- عنوان الرحلة -->
    {{-- <div>
        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">عنوان الرحلة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <input type="text" placeholder="مثال: رحلة خاصة" name="title"
            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('title')
            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
    </div> --}}

    {{-- Column for the Customer Search Component --}}
    <div class="relative col-span-1">
        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
            ابحث عن العميل (بالاسم أو رقم الجوال) <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
        </label>
        <div class="flex gap-2">
            <input id="customer_search" type="tel" placeholder="أبحث برقم الهاتف أو الاسم"
                wire:model.live.debounce.300ms="customer_phone" autocomplete="off"
                @if ($selected_customer_id) disabled @endif
                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed dark:disabled:bg-gray-800">
            @if ($selected_customer_id)
                <button type="button" wire:click="resetCustomer"
                    class="flex justify-center px-4 py-3 text-sm font-medium text-white rounded-lg hover:bg-brand-600 bg-brand-500"
                    title="إعادة تعيين">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            @else
                <button type="button" @click="$dispatch('open-user-modal')"
                    class="flex justify-center px-4 py-3 text-sm font-medium text-white rounded-lg hover:bg-brand-600 bg-brand-500"
                    title="إضافة عميل جديد">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            @endif
        </div>
        @if (!empty($customers_list))
            <div
                class="overflow-y-auto absolute z-10 mt-1 w-full max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                <div>
                    @forelse($customers_list as $customer)
                        <div wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->phone }}','{{ $customer->name }}', {{ $customer->wallet_balance ?? 0 }})"
                            class="flex gap-3 items-center p-2 px-4 border-b transition-colors duration-150 cursor-pointer border-gray-200/50 dark:border-gray-700/50 hover:bg-sky-50 dark:hover:bg-gray-700">
                            <div
                                class="flex flex-shrink-0 justify-center items-center w-8 h-8 font-bold bg-sky-100 rounded-full dark:bg-sky-900/50 text-warning-500 dark:text-warning/90">
                                {{ Str::upper(Str::substr($customer->name, 0, 2)) }}
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $customer->name }}
                                </p>
                                <p class="text-xs tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ $customer->phone }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-sm text-center text-gray-500">لا توجد نتائج مطابقة</div>
                    @endforelse
                </div>
            </div>
        @endif
        @error('user_id')
            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
    </div>
    <input type="hidden" name="user_id" id="user_id" value="{{ $selected_customer_id }}">

    <!-- اختيار المركبة -->
    <div>
        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">اختيار المركبة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning-500">*</span></label>

        <!-- تم تغيير dark:text-gray-300 إلى dark:text-white -->
        <select id="vehicle_id" name="vehicle_id" wire:model.live="vehicle_id"
            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

            <!-- تمت إضافة كلاس text-gray-900 للخيارات للحفاظ على لونها الداكن -->
            <option value='' class="text-gray-900 dark:text-gray-900">اختر المركبة</option>
            @foreach ($this->vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" class="text-gray-900 dark:text-gray-900">{{ $vehicle->type }}
                </option>
            @endforeach
        </select>

        @error('vehicle_id')
            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
    </div>
    <!-- خيارات التكييف (تظهر فقط إذا كانت المركبة تدعم التكييف) -->
    @if ($show_ac_options)
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">خيارات التكييف <span
                    class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
            <div class="flex gap-4">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="wants_ac" value="1" wire:model.live="wants_ac" class="sr-only">
                    <div
                        class="px-4 py-3 w-full text-center text-sm font-bold rounded-xl border-2 transition-all {{ $wants_ac == true ? 'bg-brand-50 border-brand-500 text-brand-600 dark:bg-brand-900/20 dark:border-brand-500 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:border-brand-300 dark:bg-dark-900 dark:border-gray-700 dark:text-gray-300' }}">
                        مع تكييف
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="wants_ac" value="0" wire:model.live="wants_ac" class="sr-only">
                    <div
                        class="px-4 py-3 w-full text-center text-sm font-bold rounded-xl border-2 transition-all {{ $wants_ac == false ? 'bg-brand-50 border-brand-500 text-brand-600 dark:bg-brand-900/20 dark:border-brand-500 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:border-brand-300 dark:bg-dark-900 dark:border-gray-700 dark:text-gray-300' }}">
                        بدون تكييف
                    </div>
                </label>
            </div>
        </div>
    @endif

    <!-- تاريخ الرحلة -->
    <div class="col-span-1 sm:col-span-2">
        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">تاريخ الرحلة</label>
        <input type="date" name="trip_date" wire:model.live="trip_date"
            onclick="this.showPicker && this.showPicker()"
            class="cursor-pointer px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white [&::-webkit-calendar-picker-indicator]:cursor-pointer">
    </div>

    <!-- حاوية تجمع وقت الانطلاق وكوبون الخصم في صف واحد -->
    <div class="grid grid-cols-1 col-span-1 gap-4 items-start sm:col-span-2 sm:grid-cols-2">

        <!-- وقت الانطلاق -->
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">وقت الانطلاق</label>
            <input type="time" name="trip_time" wire:model.live="trip_time"
                onclick="this.showPicker && this.showPicker()"
                class="cursor-pointer px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white [&::-webkit-calendar-picker-indicator]:cursor-pointer">
        </div>

        <!-- كوبون الخصم -->
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">كوبون الخصم
                (اختياري)</label>
            <div class="flex gap-2">
                <input id="discount_code" type="text" name="discount_code" wire:model.defer="coupon_code"
                    placeholder="أدخل كود الخصم"
                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

                <button type="button" wire:click="applyCoupon" wire:loading.attr="disabled"
                    class="flex justify-center items-center px-6 py-2.5 text-sm font-medium text-white rounded-lg transition-colors shrink-0 hover:bg-brand-600 bg-brand-500">
                    <span wire:loading.remove wire:target="applyCoupon">تطبيق</span>
                    <span wire:loading wire:target="applyCoupon" class="flex gap-2 items-center">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
            @if ($coupon_message)
                <p
                    class="mt-2 text-sm {{ strpos($coupon_message, 'صالح') !== false ? 'text-success-500' : 'text-error-500' }}">
                    {{ $coupon_message }}
                </p>
            @endif
        </div>

    </div>
    <!-- طريقة الدفع (Radio Cards) -->
    <div class="col-span-1 sm:col-span-2" style="grid-column: 1 / -1;">
        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">طريقة الدفع <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <div class="flex flex-col gap-4 w-full sm:flex-row">
            <!-- كاش -->
            <label class="flex-1 cursor-pointer">
                <input type="radio" name="payment_method" value="cash" wire:model.live="payment_method"
                    class="sr-only">
                <div
                    class="p-5 rounded-2xl border-2 transition-all duration-200 h-full flex flex-col items-center justify-center gap-3 {{ $payment_method === 'cash' ? 'border-brand-500 bg-brand-50 dark:border-brand-500 dark:text-white dark:bg-gray-800 shadow-sm' : 'border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <i
                        class="fas fa-money-bill-wave text-3xl {{ $payment_method === 'cash' ? 'text-brand-500' : 'text-gray-400' }}"></i>
                    <span
                        class="text-sm font-bold {{ $payment_method === 'cash' ? 'text-brand-600 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300' }}">الدفع
                        نقداً</span>
                </div>
            </label>

            <!-- محفظة -->
            {{-- <label class="flex-1 cursor-pointer">
                <input type="radio" name="payment_method" value="wallet" wire:model.live="payment_method"
                    class="sr-only">
                <div
                    class="p-5 rounded-2xl border-2 transition-all duration-200 h-full flex flex-col items-center justify-center gap-3 {{ $payment_method === 'wallet' ? 'border-brand-500 bg-brand-50 dark:border-brand-500 dark:bg-brand-900/20 shadow-sm' : 'border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800/80' }}">
                    <i
                        class="fas fa-wallet text-3xl {{ $payment_method === 'wallet' ? 'text-brand-500' : 'text-gray-400' }}"></i>
                    <span
                        class="text-sm font-bold {{ $payment_method === 'wallet' ? 'text-brand-600 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300' }}">المحفظة</span>
                    @if ($selected_customer_id)
                        <span
                            class="text-xs font-medium px-2 py-1 rounded-md {{ $payment_method === 'wallet' ? 'bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">الرصيد:
                            {{ number_format($wallet_balance, 2) }}</span>
                    @else
                        <span class="text-xs text-gray-400">الرجاء اختيار عميل</span>
                    @endif
                </div>
            </label> --}}

            <!-- إلكتروني -->
            {{-- <label class="flex-1 cursor-pointer">
                <input type="radio" name="payment_method" value="deposit" wire:model.live="payment_method"
                    class="sr-only">
                <div
                    class="p-5 rounded-2xl border-2 transition-all duration-200 h-full flex flex-col items-center justify-center gap-3 {{ $payment_method === 'deposit' ? 'border-brand-500 bg-brand-50 dark:border-brand-500 dark:bg-brand-900/20 shadow-sm' : 'border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 hover:border-brand-300 hover:bg-gray-50 dark:hover:bg-gray-800/80' }}">
                    <i
                        class="fas fa-credit-card text-3xl {{ $payment_method === 'deposit' ? 'text-brand-500' : 'text-gray-400' }}"></i>
                    <span
                        class="text-sm font-bold {{ $payment_method === 'deposit' ? 'text-brand-600 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300' }}">الدفع
                        الإلكتروني</span>
                </div>
            </label> --}}
        </div>

        <!-- حقول الدفع الإلكتروني تظهر عند اختياره -->
        @if ($payment_method === 'deposit')
            <div
                class="grid grid-cols-1 gap-2 p-4 mt-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700 sm:grid-cols-2">
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">البنك <span
                            class="text-warning-500">*</span></label>
                    <select name="bank_name" wire:model="bank_name"
                        class="px-4 py-2.5 w-full h-11 text-sm bg-white rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-600 focus:border-brand-500 dark:border-gray-600 dark:text-gray-50">
                        <option value="">اختر البنك</option>
                        <option value="kuraimi">مصرف الكريمي</option>
                        <option value="qutaibi">بنك القطيبي</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم الحساب /
                        الجوال <span class="text-warning-500">*</span></label>
                    <input type="text" name="account_number" wire:model="account_number"
                        placeholder="أدخل رقم الحساب المحول منه"
                        class="px-4 py-2.5 w-full h-11 text-sm bg-white rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-600 focus:border-brand-500 dark:border-gray-600 dark:text-white">
                </div>
            </div>
        @endif
    </div>



    <!-- الوصف -->
    <div class="col-span-1 sm:col-span-2" style="grid-column: 1 / -1;">
        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الوصف</label>
        <textarea placeholder="أدخل ملاحظات للسائق" name="notes" rows="2"
            class="px-4 py-2.5 w-full text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
    </div>

    <!-- Price Breakdown Card -->
    @if ($price_details)
        <!-- بطاقة تفاصيل التكلفة التقديرية -->
        <div class="overflow-hidden relative col-span-1 mt-6 bg-white rounded-2xl border border-gray-200 transition-all duration-300 sm:col-span-2 dark:bg-gray-800 dark:border-gray-800 shadow-theme-sm"
            style="grid-column: 1 / -1;">
            <!-- شريط التمييز العلوي (Accent Line) -->
            <div class="absolute inset-x-0 top-0 h-1.5 bg-brand-500 dark:bg-gray-800"></div>

            <div class="p-6">
                <!-- الترويسة -->
                <div class="flex gap-3 items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                    <div
                        class="flex justify-center items-center w-11 h-11 rounded-full bg-brand-50 dark:bg-brand-500/15 text-brand-500 dark:text-brand-400">
                        <!-- أيقونة الفاتورة (Receipt) SVG -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تفاصيل التكلفة التقديرية</h3>
                        <p class="mt-0.5 text-gray-500 text-theme-xs dark:text-gray-400">تسعيرة الرحلة المباشرة</p>
                    </div>
                </div>

                <!-- تفاصيل الأسعار -->
                <div class="space-y-3.5 text-theme-sm">
                    <!-- السعر الأساسي -->
                    <div class="flex justify-between items-center text-gray-600 dark:text-white">
                        <span class="flex gap-2 items-center">
                            <!-- أيقونة المسار (Route) SVG -->
                            <svg class="w-4 h-4 text-warning-500 dark:text-warning-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                </path>
                            </svg>
                            السعر الأساسي <span
                                class="text-gray-400 text-theme-xs">({{ $price_details['distance_km'] }} كم)</span>
                        </span>
                        <span
                            class="font-semibold text-gray-900 dark:text-white">{{ number_format($price_details['base_price'], 2) }}</span>
                    </div>

                    <!-- رسوم التكييف -->
                    @if ($price_details['ac_cost'] > 0)
                        <div class="flex justify-between items-center text-gray-600 dark:text-white">
                            <span class="flex gap-2 items-center">
                                <!-- أيقونة التكييف (Snowflake) SVG -->
                                <svg class="w-4 h-4 text-blue-light-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v18m-9-9h18m-11.364-6.364l12.728 12.728m-12.728 0l12.728-12.728"></path>
                                </svg>
                                رسوم التكييف
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white">+
                                {{ number_format($price_details['ac_cost'], 2) }}</span>
                        </div>
                    @endif

                    <!-- الإضافات الأخرى (Surcharges) -->
                    @foreach ($price_details['surcharges_details'] as $surcharge)
                        @if ($surcharge['id'] !== 'ac_cost' && $surcharge['id'] !== 'discount')
                            <div class="flex justify-between items-center text-gray-600 dark:text-white">
                                <span class="flex gap-2 items-center">
                                    <!-- أيقونة الإضافات (Layers) SVG -->
                                    <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>

                                    {{ $surcharge['name'] }}
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">+
                                    {{ number_format($surcharge['amount'], 2) }}</span>
                            </div>
                        @endif
                    @endforeach

                    <!-- الخصم -->
                    @if ($price_details['discount_amount'] > 0)
                        <!-- قسم الخصم بتصميم احترافي (Premium) -->
                        <div
                            class="flex overflow-hidden relative justify-between items-center p-4 mt-3 bg-gradient-to-l to-white rounded-xl border shadow-sm transition-all duration-300 border-success-200 dark:border-success-500/20 from-success-50 dark:from-success-500/10 dark:to-gray-900 hover:shadow-md">

                            <!-- تأثير ديكوري في الخلفية (Glow) -->
                            <div
                                class="absolute -top-4 -left-4 w-16 h-16 rounded-full blur-xl pointer-events-none bg-success-400/10 dark:bg-success-500/10">
                            </div>

                            <div class="flex relative z-10 gap-3 items-center">
                                <!-- صندوق الأيقونة -->
                                <div
                                    class="flex justify-center items-center w-8 h-8 rounded-lg bg-success-100 dark:bg-success-500/20 text-success-600 dark:text-success-400 shadow-theme-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                </div>

                                <!-- النصوص والشارة (Badge) -->
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <span class="text-sm font-bold text-success-700 dark:text-success-400">تم تطبيق
                                        الخصم</span>
                                    @if ($coupon_code)
                                        <!-- شارة كود الخصم -->
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-black tracking-wider text-success-600 dark:text-success-400 bg-white dark:bg-gray-800 border border-success-200 dark:border-success-500/30 uppercase shadow-sm">
                                            {{ $coupon_code }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- قيمة الخصم -->
                            <div class="relative z-10 text-base font-black text-success-600 dark:text-success-400"
                                dir="ltr">
                                - {{ number_format($price_details['discount_amount'], 2) }}
                            </div>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <div class="flex gap-2 items-center">
                            <svg class="w-4 h-4 text-warning-500 dark:text-warning-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-white">سعرالكيلو متر الواحد</span>
                        </div>

                        <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $price_details['price_per_km'] }}</span>
                    </div>
                    <!-- الإجمالي النهائي -->
                    <div
                        class="flex justify-between items-center pt-4 mt-4 border-t border-gray-200 border-dashed dark:border-gray-700">
                        <span class="text-base font-bold text-gray-900 dark:text-white">الإجمالي المتوقع</span>

                        <!-- تم استخدام flex و items-baseline لضبط المحاذاة، و gap-1 للمسافة -->
                        <div class="flex gap-1 items-baseline">
                            <span
                                class="text-2xl font-black text-brand-600 dark:text-brand-400">{{ number_format($price_details['final_price'], 0) }}</span>
                            <span class="font-bold text-gray-400 text-theme-xs dark:text-gray-500">ريال</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden Input -->
                <input type="hidden" name="final_price_client" value="{{ $price_details['final_price'] }}">
            </div>
        </div>
    @elseif($calculation_error)
        <!-- حالة الخطأ (Error State) -->
        <div class="flex col-span-1 gap-3 items-start p-4 mt-6 rounded-2xl border transition-all sm:col-span-2 bg-error-50 dark:bg-error-500/15 border-error-300 dark:border-error-500/30"
            style="grid-column: 1 / -1;">
            <!-- أيقونة الخطأ SVG -->
            <svg class="mt-0.5 w-5 h-5 text-error-500 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="font-medium leading-relaxed text-theme-sm text-error-700 dark:text-error-500">
                {{ $calculation_error }}
            </div>
        </div>
    @else
        <!-- الحالة الفارغة (Empty State) -->
        <div class="col-span-1 mt-6 sm:col-span-2" style="grid-column: 1 / -1;">
            <div
                class="flex flex-col gap-4 justify-center items-center p-10 text-center bg-gray-50 rounded-2xl border-2 border-gray-200 border-dashed transition-all dark:bg-gray-800 dark:border-gray-700">
                <!-- أيقونة بارزة للآلة الحاسبة SVG -->
                <div
                    class="flex justify-center items-center w-16 h-16 bg-white rounded-full dark:bg-gray-900 shadow-theme-sm">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <!-- نصوص -->
                <div>
                    <h4 class="text-base font-bold text-gray-800 dark:text-white">التكلفة التقديرية</h4>
                    <p class="mx-auto mt-2 max-w-sm leading-relaxed text-gray-500 text-theme-sm dark:text-gray-400">
                        الرجاء تحديد مسار الرحلة عبر الخريطة واختيار المركبة لتتمكن من رؤية تفاصيل التكلفة التقديرية
                        هنا.
                    </p>
                </div>
            </div>
        </div>
    @endif
    <!-- نافذة إضافة مستخدم جديد -->
    <div x-data="{ isUserModalOpen: false }" @open-user-modal.window="isUserModalOpen = true"
        @close-user-modal.window="isUserModalOpen = false">

        <div x-show="isUserModalOpen"
            class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-[99999]"
            style="display: none;">
            <div class="fixed inset-0 w-full h-full modal-close-btn bg-gray-400/50 backdrop-blur-[32px]"></div>

            <div @click.outside="isUserModalOpen = false" @keydown.enter.prevent="$wire.storeNewCustomer()"
                class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

                <!-- استخدام wire:submit.prevent لمنع تحديث الصفحة وتنفيذ دالة Livewire -->
                <div>
                    <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">إضافة مستخدم جديد</h4>

                    <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-2">
                        <!-- الاسم -->
                        <div class="sm:col-span-2">
                            <label for="new_name"
                                class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                أسم المستخدم <span class="mt-1 text-xs text-warning-500">*</span>
                            </label>
                            <input type="text" id="new_name" wire:model="new_user_name"
                                placeholder="مثال: أحمد شرجبي"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                            @error('new_user_name')
                                <span class="text-xs text-error-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- رقم الجوال -->
                        <div class="col-span-1">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                رقم الجوال <span class="mt-1 text-xs text-warning-500">*</span>
                            </label>
                            <div x-data="{
                                open: false,
                                search: '',
                                countries: [{ name: 'Yemen', code: 'YE', dial_code: '+967' }, { name: 'Saudi Arabia', code: 'SA', dial_code: '+966' }],
                                selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                localPhoneNumber: '',
                                init() {
                                    this.$watch('localPhoneNumber', value => { $wire.set('new_user_phone', this.selectedCountry.dial_code.replace('+', '') + value); });
                                    this.$watch('selectedCountry', value => { $wire.set('new_user_phone', value.dial_code.replace('+', '') + this.localPhoneNumber); });
                                }
                            }" class="relative">
                                <div
                                    class="flex w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">
                                    <button type="button" @click="open = !open"
                                        class="flex gap-2 items-center px-3 bg-gray-50 rounded-r-lg border-l border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                        <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                            class="w-5 h-auto">
                                    </button>
                                    <input type="tel" x-model="localPhoneNumber" placeholder="780236551"
                                        dir="ltr"
                                        class="flex-grow px-3 text-sm text-left text-gray-800 bg-transparent rounded-l-lg border-none dark:text-white focus:outline-none focus:ring-0">
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">سيتم استخدام هذا الرقم لتسجيل
                                الدخول.</p>
                            @error('new_user_phone')
                                <span class="text-xs text-error-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- رقم الواتساب -->
                        <div class="col-span-1">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                رقم الواتساب <span class="text-gray-500">(اختياري)</span>
                            </label>
                            <div x-data="{
                                open: false,
                                search: '',
                                countries: [{ name: 'Yemen', code: 'YE', dial_code: '+967' }, { name: 'Saudi Arabia', code: 'SA', dial_code: '+966' }],
                                selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                localPhoneNumber: '',
                                init() {
                                    this.$watch('localPhoneNumber', value => { $wire.set('new_user_whatsapp', this.selectedCountry.dial_code.replace('+', '') + value); });
                                    this.$watch('selectedCountry', value => { $wire.set('new_user_whatsapp', value.dial_code.replace('+', '') + this.localPhoneNumber); });
                                }
                            }" class="relative">
                                <div
                                    class="flex w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">
                                    <button type="button" @click="open = !open"
                                        class="flex gap-2 items-center px-3 bg-gray-50 rounded-r-lg border-l border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                        <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                            class="w-5 h-auto">
                                    </button>
                                    <input type="tel" x-model="localPhoneNumber" placeholder="780236551"
                                        dir="ltr"
                                        class="flex-grow px-3 text-sm text-left text-gray-800 bg-transparent rounded-l-lg border-none dark:text-white focus:outline-none focus:ring-0">
                                </div>
                            </div>
                            @error('new_user_whatsapp')
                                <span class="text-xs text-error-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- كلمة السر -->
                        <div class="sm:col-span-2">
                            <label for="new_password"
                                class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                كلمة السر <span class="mt-1 text-xs text-warning-500">*</span>
                            </label>
                            <input type="text" id="new_password" wire:model="new_user_password"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                            @error('new_user_password')
                                <span class="text-xs text-error-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex gap-3 justify-end items-center mt-6 w-full">
                        <button @click="isUserModalOpen = false" type="button"
                            class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="button" wire:click="storeNewCustomer" wire:loading.attr="disabled"
                            wire:target="storeNewCustomer"
                            class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-600 bg-brand-500 disabled:opacity-75">
                            <svg wire:loading wire:target="storeNewCustomer" class="w-5 h-5 text-white animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="storeNewCustomer">إنشاء المستخدم</span>
                            <span wire:loading wire:target="storeNewCustomer">جاري الإنشاء...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
