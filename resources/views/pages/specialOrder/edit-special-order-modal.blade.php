<div x-data="{
    isModalEditOpen: @if(session('openModalEdit')) true @else false @endif,
    isLoading: false
}">
    <div x-show="isModalEditOpen"
        class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
        style="display: none;">
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
            @click="isModalEditOpen = false">
        </div>

        <div @click.outside="isModalEditOpen = false"
            class="relative w-full max-w-2xl rounded-3xl bg-white p-6 dark:bg-gray-900 shadow-theme-lg border border-gray-100 dark:border-gray-800 lg:p-8 max-h-[92vh] overflow-y-auto"
            dir="rtl">
            @php
                $order = session('order');
                $vehiclesList = session('vehicles') ?? \App\Models\Vehicle::select(['id', 'type'])->get();
            @endphp
            @if($order)
                <div class="flex justify-between items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                                تعديل حجز الخدمة والتسعيرة #{{ $order->id }}
                            </h4>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                تحديث بيانات الحجز، نوع الخدمة، التسعيرة وحالة الطلب
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="isModalEditOpen = false"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('specialOrder.update', $order->id) }}" @submit="isLoading = true" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- 1. نوع الخدمة --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                نوع الخدمة <span class="text-warning-500">*</span>
                            </label>
                            <select name="service_type" required
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-brand-500">
                                @foreach (\App\Models\ServiceBooking::serviceTypes() as $typeKey => $typeLabel)
                                    <option value="{{ $typeKey }}" @selected(old('service_type', $order->service_type) === $typeKey)>
                                        {{ $typeLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_type') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 2. اسم العميل --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                اسم العميل <span class="text-warning-500">*</span>
                            </label>
                            <input type="text" name="client_name" required
                                value="{{ old('client_name', $order->client_name ?? ($order->user?->name ?? '')) }}"
                                placeholder="اسم العميل"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('client_name') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3. نوع المركبة --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                نوع المركبة
                            </label>
                            <select name="vehicle_id"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-brand-500">
                                <option value="">اختر المركبة (اختياري)</option>
                                @foreach ($vehiclesList as $v)
                                    <option value="{{ $v->id }}" @selected((int) old('vehicle_id', $order->vehicle_id) === (int) $v->id)>
                                        {{ $v->type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 4. السعر الإجمالي --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                السعر الإجمالي ({{ config('app.currency_symbol', 'ر.ي') }})
                            </label>
                            <input type="number" step="0.01" min="0" name="total_price"
                                value="{{ old('total_price', $order->total_price) }}"
                                placeholder="أدخل السعر الإجمالي"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('total_price') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 5. حالة الدفع --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                حالة الدفع <span class="text-warning-500">*</span>
                            </label>
                            <select name="payment_status" required
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-brand-500">
                                <option value="unpaid" @selected(old('payment_status', $order->payment_status) === 'unpaid')>غير مدفوع</option>
                                <option value="partially_paid" @selected(old('payment_status', $order->payment_status) === 'partially_paid')>مدفوع جزئياً</option>
                                <option value="paid" @selected(old('payment_status', $order->payment_status) === 'paid')>مدفوع</option>
                            </select>
                            @error('payment_status') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 6. حالة الحجز --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                حالة الحجز <span class="text-warning-500">*</span>
                            </label>
                            <select name="status" required
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-brand-500">
                                <option value="pending" @selected(old('status', $order->status) === 'pending')>قيد الانتظار</option>
                                <option value="approved" @selected(old('status', $order->status) === 'approved')>تمت الموافقة</option>
                                <option value="in_progress" @selected(old('status', $order->status) === 'in_progress')>قيد التنفيذ</option>
                                <option value="completed" @selected(old('status', $order->status) === 'completed')>مكتملة</option>
                                <option value="rejected" @selected(old('status', $order->status) === 'rejected')>مرفوض</option>
                                <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>ملغية</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 7. عدد الركاب --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                عدد الركاب <span class="text-warning-500">*</span>
                            </label>
                            <input type="number" min="1" name="passenger_count" required
                                value="{{ old('passenger_count', $order->passenger_count ?? 1) }}"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('passenger_count') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 8. عدد المركبات --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                عدد المركبات <span class="text-warning-500">*</span>
                            </label>
                            <input type="number" min="1" name="vehicles_count" required
                                value="{{ old('vehicles_count', $order->vehicles_count ?? 1) }}"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('vehicles_count') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 9. نقطة الانطلاق --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                نقطة الانطلاق <span class="text-warning-500">*</span>
                            </label>
                            <input type="text" name="start_address" required
                                value="{{ old('start_address', $order->start_address) }}"
                                placeholder="مثال: صنعاء - حدة"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('start_address') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 10. نقطة الوصول --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">
                                نقطة الوصول <span class="text-warning-500">*</span>
                            </label>
                            <input type="text" name="end_address" required
                                value="{{ old('end_address', $order->end_address) }}"
                                placeholder="مثال: مطار عدن الدولي"
                                class="px-3 py-2 w-full h-11 text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500" />
                            @error('end_address') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 11. تكييف المركبة --}}
                        <div class="pt-2 sm:col-span-2">
                            <label class="flex gap-2.5 items-center text-sm font-medium text-gray-700 cursor-pointer dark:text-gray-400">
                                <input type="hidden" name="wants_ac" value="0">
                                <input type="checkbox" name="wants_ac" value="1"
                                    @checked(old('wants_ac', $order->wants_ac))
                                    class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800" />
                                <span>طلب تكييف للمركبة (AC)</span>
                            </label>
                        </div>

                        {{-- 12. الملاحظات --}}
                        <div class="sm:col-span-2">
                            <label class="block mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                            <textarea name="notes" rows="3"
                                placeholder="ملاحظات وتفاصيل إضافية حول الحجز..."
                                class="px-3 py-2 w-full text-sm bg-transparent rounded-lg border border-gray-300 dark:border-gray-700 dark:text-white focus:border-brand-500">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end pt-5 mt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="isModalEditOpen = false"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400">
                            إلغاء
                        </button>
                        <button type="submit" :disabled="isLoading"
                            class="inline-flex gap-2 items-center px-6 py-2.5 text-sm font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-600 disabled:opacity-50">
                            <svg x-show="isLoading" class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isLoading ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
