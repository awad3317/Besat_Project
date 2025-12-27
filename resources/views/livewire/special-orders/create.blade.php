<div class="grid grid-cols-1 gap-x-6 gap-y-5 mt-2 sm:grid-cols-2">
    <!-- عنوان الرحلة -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">عنوان الرحلة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        {{-- ✅ ربط الحقل بخاصية title في Livewire --}}
        <input type="text" placeholder="مثال: رحلة خاصة" 
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <!-- اسم العميل -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم العميل <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <input type="text" placeholder="مثال: أحمد شرجبي" 
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('customer_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <!-- رقم جوال العميل -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم جوال العميل <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <input type="tel" placeholder="مثال: 967780236552" 
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('customer_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <!-- رقم الواتساب -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم واتساب العميل</label>
        <input type="tel" placeholder="مثال: 967780236552" 
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('customer_whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اختيار المركبة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <select id="vehicle_id" name="vehicle_id"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <option>اختر المركبه</option>
            @foreach($this->vehicles as $vehicle)
            <option class="" value="{{ $vehicle->id }}">{{ $vehicle->type }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-span-1">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">كوبون الخصم (اختياري)</label>
        <div class="flex gap-2">
            <input id="coupon_code" type="text" name="coupon_code" wire:model.defer="coupon_code" placeholder="أدخل كود الخصم" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <button type="button" wire:click="applyCoupon" wire:loading.attr="disabled" class="flex justify-center hover:bg-brand-600 px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
                <span wire:loading.remove wire:target="applyCoupon">تحقق</span>
                <span wire:loading wire:target="applyCoupon">جاري...</span>
            </button>
        </div>
        @if($coupon_message)
            <p class="mt-1 text-l text-warning-500 dark:text-warning/90">
                {{ $coupon_message }}
            </p>
        @endif
    </div>

    <!-- الوصف -->
    <div class="col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الوصف</label>
        <textarea placeholder="أدخل وصفاً للرحلة"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

</div>