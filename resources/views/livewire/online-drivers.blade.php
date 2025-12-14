<div wire:poll.60s>
    <div class="col-span-1 sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            السائق <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
        </label>
        <select name="driver_id" required
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            <option value="" disabled @selected(old('driver_id') == '') class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                أختر السائق</option>
            @foreach($this->getOnlineDrivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)
                    class="text-brand-600 dark:text-brand-400 bg-white dark:bg-gray-900">
                    {{ $driver->name }} - {{ $driver->phone }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">
            سيتم إرسال اشعار الى السائق عبر التطبيق
        </p>
    </div>
</div>