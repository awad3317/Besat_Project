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
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم الواتساب</label>
        <input type="tel" placeholder="مثال: 967780236552" 
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('customer_whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <!-- سعر الرحلة -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">سعر الرحلة (ريال)</label>
        <input type="number"  placeholder="سيتم حسابه تلقائياً"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اختيار المركبة <span
                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span></label>
        <select wire:model="driver_id"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600">
            <option value="">اختر المركبه</option>
            @foreach($this->vehicles as $vehicle)
            <option value="{{ $vehicle->id }}">{{ $vehicle->type }}</option>
            @endforeach
        </select>
        @error('driver_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <!-- الوصف -->
    <div class="col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الوصف</label>
        <textarea placeholder="أدخل وصفاً للرحلة"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

</div>