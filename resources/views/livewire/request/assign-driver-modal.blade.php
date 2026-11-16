@php
    $req = $this->selectedRequest;
    $canAssign = $req ? $req->canAssignDriver() : false;
@endphp

<div x-data="assignDriverDispatch()"
    x-on:open-modal.window="if ($event.detail == 'assign-driver-modal') {
        show = true; isLoading = false; isSuccess = false; clearSelection();
        $nextTick(() => { initMap(); setTimeout(() => { invalidateMap(); }, 300); });
     }"
    x-on:close-modal.window="if ($event.detail == 'assign-driver-modal') { show = false; isLoading = false; clearSelection(); }"
    x-on:driver-assigned.window="isLoading = false; isSuccess = true; setTimeout(() => { show = false; isSuccess = false; @if($modalOnly ?? false) window.location.reload(); @else $wire.clearSelectedRequest(); @endif }, 2000)"
    x-on:sync-dispatch-map.window="syncMap($event.detail[0] || $event.detail)"
    x-on:close.stop="if (!isLoading && !isSuccess) { show = false; }"
    x-on:keydown.escape.window="if (!isLoading && !isSuccess) { show = false; }" x-show="show"
    class="flex overflow-y-auto fixed inset-0 justify-center items-center p-3 modal z-99999" style="display: none;">

    {{-- Backdrop (TailAdmin signature blur) --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="!isLoading && !isSuccess ? show = false : null"></div>

    {{-- Modal Box (Standard TailAdmin rounded-3xl card with split view) --}}
    <div @click.outside="!isLoading && !isSuccess ? show = false : null"
        class="overflow-hidden relative z-10 p-5 w-full bg-white rounded-3xl border border-gray-200 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800 dispatch-modal-box"
        dir="rtl">

        {{-- ════════════ Loading Overlay ════════════ --}}
        <div x-show="isLoading" class="flex absolute inset-0 z-50 flex-col justify-center items-center rounded-3xl"
            style="background-color: rgba(255, 255, 255, 0.92);">
            <div
                class="mb-4 w-12 h-12 rounded-full border-4 border-solid animate-spin border-brand-500 border-t-transparent">
            </div>
            <p class="text-base font-bold text-gray-800 dark:text-white">جاري تعيين السائق للطلب...</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يتم الآن تحديث الحالة وإرسال الإشعارات</p>
        </div>

        {{-- ════════════ Success Overlay ════════════ --}}
        <div x-show="isSuccess"
            class="flex absolute inset-0 z-50 flex-col justify-center items-center bg-white rounded-3xl dark:bg-gray-900">
            <div
                class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-success-50 text-success-500 dark:bg-success-500/15">
                <svg class="w-10 h-10 text-success-500" fill="none" stroke="currentColor" stroke-width="3"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h4 class="mb-2 text-xl font-bold text-gray-800 dark:text-white">تم تعيين السائق بنجاح!</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">تم إسناد الطلب وإشعار السائق والعميل بنجاح...</p>
        </div>

        {{-- ════════════ Header ════════════ --}}
        <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-100 dark:border-gray-800"
            style="flex-shrink: 0;">
            <div class="flex gap-3 items-center">
                <div
                    class="flex justify-center items-center w-11 h-11 rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex flex-wrap gap-2 items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            تعيين سائق للطلب #{{ $selectedRequestId }}
                        </h4>
                        @if ($req && $req->canChangeVehicle())
                            <div class="inline-flex gap-1.5 items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">نوع المركبة:</span>
                                <select
                                    x-on:change="
                                            const newId = $event.target.value;
                                            const newText = $event.target.options[$event.target.selectedIndex].text.trim();
                                            $event.target.value = '{{ $req->vehicle_id ?? '' }}';
                                            if (newId && String(newId) !== String('{{ $req->vehicle_id ?? '' }}')) {
                                                $dispatch('confirm-vehicle-change', {
                                                    requestId: {{ $req->id }},
                                                    oldVehicleType: '{{ addslashes($req->vehicle?->type ?? 'غير محدد') }}',
                                                    newVehicleId: newId,
                                                    newVehicleType: newText
                                                });
                                            }
                                        "
                                    wire:loading.attr="disabled" wire:target="changeRequestVehicle"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border cursor-pointer vehicle-select border-brand-500/40 bg-brand-50 text-brand-600 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
                                    @foreach ($this->vehicles as $vehicleOption)
                                        <option value="{{ $vehicleOption->id }}" class="vehicle-option"
                                            @selected((int) $req->vehicle_id === (int) $vehicleOption->id)>
                                            {{ $vehicleOption->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($req?->vehicle)
                            <span
                                class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-light-50 text-blue-light-500">
                                {{ $req->vehicle->type }}
                            </span>
                        @endif
                        @if ($req)
                            <span
                                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400">
                                السعر: {{ $req->final_price }} ريال
                            </span>
                        @endif
                    </div>
                    @if ($req)
                        <p class="flex gap-2 items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-success-600">من:
                                {{ $req->start_address ?? 'نقطة الانطلاق' }}</span>
                            <span>←</span>
                            <span class="font-semibold text-error-500">إلى: {{ $req->end_address ?? 'الوجهة' }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <button x-on:click="!isLoading && !isSuccess ? show = false : null"
                class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ════════════ Split-View Content (Side-by-side in RTL) ════════════ --}}
        <div class="dispatch-split-container">

            {{-- ──────── RIGHT PANEL: Driver List & Filters ──────── --}}
            <div
                class="p-3 bg-gray-50 rounded-2xl border border-gray-200 dispatch-drivers-panel dark:border-gray-800 dark:bg-gray-900">

                {{-- Search & Filters: Uses flex-col with gap-3 (TailAdmin compliant, replaces missing space-y-2.5) --}}
                <div class="flex flex-col gap-3 mb-3" style="flex-shrink: 0;">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="driverSearch" type="text"
                            placeholder="ابحث بالاسم أو الهاتف أو اللوحة..."
                            class="px-4 py-2.5 pr-10 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 hover:border-brand-500">
                        <div class="flex absolute inset-y-0 right-0 items-center pr-3 text-gray-400"
                            style="pointer-events: none;">
                            <svg wire:loading.remove wire:target="driverSearch" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <div wire:loading wire:target="driverSearch"
                                class="w-5 h-5 rounded-full border-2 border-solid animate-spin border-brand-500 border-t-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    <div class="flex gap-2 items-center">
                        <label
                            class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white rounded-lg border border-gray-200 cursor-pointer select-none shadow-theme-xs hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="onlineDriversOnly"
                                class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                                style="accent-color: #f79009; outline: none;" />
                            <span class="w-2 h-2 rounded-full bg-success-500" style="flex-shrink: 0;"></span>
                            <span class="dark:text-white">المتصلون فقط</span>
                        </label>
                        <label
                            class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white rounded-lg border border-gray-200 cursor-pointer select-none shadow-theme-xs hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="matchVehicleOnly"
                                class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                                style="accent-color: #f79009; outline: none;" />
                            <span class="truncate dark:text-white">نفس المركبة{{ $req?->vehicle?->type ? ' (' . $req->vehicle->type . ')' : '' }}</span>
                        </label>
                    </div>

                    {{-- Counter Bar --}}
                    <div class="flex justify-between items-center px-1 text-gray-500 text-theme-xs dark:text-gray-300">
                        <span>السائقون المتاحون ({{ count($this->drivers) }})</span>
                        <span class="text-xs">مرتبة حسب الأقرب</span>
                    </div>
                </div>

                {{-- Driver List --}}
                <div class="overflow-y-auto flex-1 px-1 py-1 space-y-1 custom-scrollbar"
                    wire:loading.class="opacity-75"
                    wire:target="driverSearch, onlineDriversOnly, matchVehicleOnly, changeRequestVehicle">
                    @forelse($this->drivers as $driver)
                        @php
                            $distKm = isset($driver->distance_km) ? round($driver->distance_km, 1) : null;
                            $driverImg = $driver->driver_image;
                            $driverImage =
                                !$driverImg || str_contains($driverImg, 'default_driver1.png')
                                    ? asset('tailadmin/build/src/images/user/SO.jpg')
                                    : url($driverImg);
                            $vehicleTypeName = $driver->vehicle?->type ?? 'غير محدد';
                            $isSameVehicle = !$req?->vehicle_id || (int) $driver->vehicle_id === (int) $req->vehicle_id;
                        @endphp
                        <div @click="flyToDriver({{ $driver->latitude ?? 'null' }}, {{ $driver->longitude ?? 'null' }}, {{ $driver->id }}, {
                                id: {{ $driver->id }},
                                name: '{{ addslashes($driver->name) }}',
                                phone: '{{ $driver->phone }}',
                                plate_number: '{{ $driver->plate_number }}',
                                vehicle_id: {{ $driver->vehicle_id ?? 'null' }},
                                vehicle_type: '{{ addslashes($vehicleTypeName) }}',
                                driver_image: '{{ $driverImage }}',
                                is_online: {{ $driver->is_online ? 'true' : 'false' }},
                                latitude: {{ $driver->latitude ?? 'null' }},
                                longitude: {{ $driver->longitude ?? 'null' }},
                                distance_km: {{ $distKm ?? 'null' }}
                             })"
                            :class="{
                                'border-brand-500 bg-brand-50 dark:bg-brand-950 dark:border-brand-500': selectedDriverId ===
                                    {{ $driver->id }},
                                'border-gray-200 bg-white hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800': selectedDriverId !==
                                    {{ $driver->id }}
                            }"
                            class="flex justify-between items-center p-3 rounded-xl border transition cursor-pointer driver-card-item shadow-theme-xs">

                            <div class="flex flex-1 gap-3 items-center" style="min-width: 0;">
                                <div class="relative" style="flex-shrink: 0;">
                                    <div
                                        class="h-11 w-11 overflow-hidden rounded-full border-2 {{ $driver->is_online ? 'border-success-500' : 'border-gray-200 dark:border-gray-700' }}">
                                        <img src="{{ $driverImage }}" alt="{{ $driver->name }}"
                                            class="w-full h-full" style="object-fit: cover;"
                                            onerror="this.onerror=null;this.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}';">
                                    </div>
                                    @if ($driver->is_online)
                                        <span
                                            class="absolute rounded-full border-2 border-white bg-success-500 dark:border-gray-900"
                                            style="width: 12px; height: 12px; bottom: -1px; right: -1px;"
                                            title="متصل"></span>
                                    @else
                                        <span
                                            class="absolute bg-gray-400 rounded-full border-2 border-white dark:border-gray-900"
                                            style="width: 12px; height: 12px; bottom: -1px; right: -1px;"
                                            title="غير متصل"></span>
                                    @endif
                                </div>
                                <div class="flex-1" style="min-width: 0;">
                                    <div class="flex gap-1.5 items-center">
                                        <p class="text-sm font-bold text-gray-800 dark:text-white/90"
                                            style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $driver->name }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex gap-1.5 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $driver->phone }}</span>
                                        @if ($driver->vehicle)
                                            <span>•</span>
                                            <span
                                                class="font-medium text-gray-700 dark:text-gray-400">{{ $driver->vehicle->type }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center mt-1">
                                        @if ($distKm !== null)
                                            <span
                                                class="inline-flex gap-1 items-center px-2 py-0.5 text-xs font-bold rounded-md border bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400 border-warning-500">
                                                <svg fill="currentColor" viewBox="0 0 20 20"
                                                    style="width: 12px; height: 12px; flex-shrink: 0;">
                                                    <path fill-rule="evenodd"
                                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span>يبعد {{ $distKm }} كم</span>
                                                <span class="text-gray-400">•</span>
                                                <span>≈ <span
                                                        x-text="estimateArrival({{ $distKm }})"></span></span>
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex gap-1 items-center px-2 py-0.5 text-xs text-gray-400 bg-gray-100 rounded-md dark:bg-gray-700">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    style="width: 12px; height: 12px; flex-shrink: 0;">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>الموقع غير محدد</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($canAssign)
                                @if ($isSameVehicle)
                                    <button @click.stop="isLoading = true" wire:click="assignDriver({{ $driver->id }})"
                                        wire:loading.attr="disabled"
                                        class="px-4 py-2 mr-2 text-xs font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-600 shadow-theme-xs"
                                        style="flex-shrink: 0;">
                                        <span>تعيين</span>
                                    </button>
                                @else
                                    <button type="button"
                                        @click.stop="$dispatch('vehicle-mismatch-blocked', {
                                            driverName: '{{ addslashes($driver->name) }}',
                                            driverVehicle: '{{ addslashes($vehicleTypeName) }}',
                                            requestVehicle: '{{ addslashes($req?->vehicle?->type ?? 'غير محدد') }}'
                                        })"
                                        class="px-4 py-2 mr-2 text-xs font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-600 shadow-theme-xs"
                                        style="flex-shrink: 0;">
                                        <span>تعيين</span>
                                    </button>
                                @endif
                            @else
                                <span
                                    class="px-3 py-1.5 mr-2 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-400"
                                    style="flex-shrink: 0;">
                                    غير متاح
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col justify-center items-center py-8 text-center bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800"
                            style="border-style: dashed;">
                            <div
                                class="flex justify-center items-center mb-3 w-12 h-12 text-gray-400 bg-gray-100 rounded-full dark:bg-brand-500">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-400">لا يوجد سائقين مطابقين</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-400">جرب تعديل خيارات البحث</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ──────── LEFT PANEL: Leaflet Map (Side-by-side) ──────── --}}
            <div
                class="bg-gray-100 rounded-2xl border border-gray-200 dispatch-map-panel dark:border-gray-800 dark:bg-gray-800">
                {{-- Map Container --}}
                <div wire:ignore id="dispatch-map" x-ref="dispatchMap"></div>

                {{-- Map Legend --}}
                <div class="flex absolute z-30 flex-col gap-1" style="top: 12px; right: 12px;">
                    <div
                        class="flex gap-2 items-center px-3 py-1.5 text-xs bg-white rounded-xl border border-gray-200 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex gap-1 items-center">
                            <span class="w-2 h-2 rounded-full bg-success-500"></span>
                            <span class="font-medium text-gray-700 dark:text-white/90">العميل</span>
                        </div>
                        <div class="flex gap-1 items-center">
                            <span class="w-2 h-2 rounded-full bg-error-500"></span>
                            <span class="font-medium text-gray-700 dark:text-white/90">الوجهة</span>
                        </div>
                        <div class="flex gap-1 items-center">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            <span class="font-medium text-gray-700 dark:text-white/90">السائقون</span>
                        </div>
                    </div>
                </div>

                {{-- Recenter Button on Pickup --}}
                <button @click="recenterOnPickup()" title="الرجوع لموقع العميل"
                    class="flex absolute z-30 gap-1.5 items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white rounded-xl border border-gray-200 transition shadow-theme-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:hover:bg-gray-800"
                    style="top: 12px; left: 12px;">
                    <svg class="text-brand-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>موقع العميل</span>
                </button>

                {{-- Floating Driver Action Card --}}
                <div x-show="selectedDriver" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="absolute z-40"
                    style="bottom: 12px; right: 12px; left: 12px; max-width: 380px;" dir="rtl">
                    <div
                        class="relative p-4 bg-white rounded-2xl border border-gray-200 shadow-theme-lg dark:border-gray-700 dark:bg-gray-900">
                        <button @click="clearSelection()"
                            class="flex absolute justify-center items-center w-7 h-7 text-gray-400 bg-gray-100 rounded-lg transition hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                            style="top: 10px; left: 10px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="flex gap-3 items-center">
                            <div class="relative" style="flex-shrink: 0;">
                                <img :src="selectedDriver?.driver_image && !selectedDriver?.driver_image.includes(
                                        'default_driver1.png') ? selectedDriver.driver_image :
                                    '{{ asset('tailadmin/build/src/images/user/SO.jpg') }}'"
                                    :alt="selectedDriver?.name"
                                    x-on:error="$event.target.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}'"
                                    class="w-12 h-12 rounded-xl border-2 border-brand-500 shadow-theme-xs"
                                    style="object-fit: cover;" />
                                <template x-if="selectedDriver?.is_online">
                                    <span
                                        class="absolute rounded-full border-2 border-white bg-success-500 dark:border-gray-900"
                                        style="width: 12px; height: 12px; bottom: -2px; right: -2px;"></span>
                                </template>
                            </div>
                            <div class="flex-1" style="min-width: 0;">
                                <h5 class="text-sm font-bold text-gray-800 dark:text-white/90"
                                    style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    x-text="selectedDriver?.name"></h5>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="selectedDriver?.phone"></span> • <span
                                        x-text="selectedDriver?.vehicle_type"></span>
                                </p>
                                <template x-if="selectedDriver?.distance_km !== null">
                                    <span
                                        class="inline-block mt-1 text-xs font-bold text-warning-600 dark:text-warning-400"
                                        x-text="'يبعد ' + selectedDriver?.distance_km + ' كم (≈ ' + estimateArrival(selectedDriver?.distance_km) + ')'"></span>
                                </template>
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-t border-gray-100 dark:border-gray-800">
                            @if ($canAssign)
                                <button type="button"
                                    x-on:click="
                                        if ({{ $req?->vehicle_id ? 'true' : 'false' }} && Number(selectedDriver?.vehicle_id) !== Number({{ $req?->vehicle_id ?? 0 }})) {
                                            $dispatch('vehicle-mismatch-blocked', {
                                                driverName: selectedDriver?.name || '',
                                                driverVehicle: selectedDriver?.vehicle_type || 'غير محدد',
                                                requestVehicle: '{{ addslashes($req?->vehicle?->type ?? 'غير محدد') }}'
                                            });
                                        } else {
                                            isLoading = true;
                                            $wire.assignDriver(selectedDriverId);
                                        }
                                    "
                                    wire:loading.attr="disabled"
                                    class="flex gap-2 justify-center items-center w-full h-10 text-xs font-medium text-white rounded-lg transition bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                        style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>تعيين هذا السائق للرحلة الآن</span>
                                </button>
                            @else
                                <div
                                    class="px-3 py-2 text-xs font-medium text-center rounded-lg text-error-600 bg-error-50 dark:bg-error-500/15 dark:text-error-400">
                                    لا يمكن تعيين سائق لرحلة مكتملة أو ملغية.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════ دايلوق منع تعيين سائق بمركبة مختلفة ════════════ --}}
        <div x-data="{
                mismatchOpen: false,
                driverName: '',
                driverVehicle: '',
                requestVehicle: ''
             }"
             x-on:vehicle-mismatch-blocked.window="
                isLoading = false;
                driverName = $event.detail?.driverName || '';
                driverVehicle = $event.detail?.driverVehicle || 'غير محدد';
                requestVehicle = $event.detail?.requestVehicle || '{{ addslashes($req?->vehicle?->type ?? 'غير محدد') }}';
                mismatchOpen = true;
             "
             x-show="mismatchOpen"
             x-transition.opacity.duration.200ms
             class="flex absolute inset-0 z-50 justify-center items-center p-4 bg-gray-900/50 backdrop-blur-xs rounded-3xl"
             style="display: none;">
            <div @click.outside="mismatchOpen = false"
                 class="p-6 w-full max-w-md text-center bg-white rounded-2xl border border-gray-200 shadow-2xl dark:bg-gray-900 dark:border-gray-800"
                 dir="rtl">
                <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 rounded-full bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm9-3.75a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">
                    لا يمكن تعيين هذا السائق!
                </h4>

                <p class="text-sm leading-6 text-gray-600 dark:text-gray-300">
                    مركبة السائق
                    <template x-if="driverName">
                        <span class="font-bold text-gray-800 dark:text-white" x-text="'(' + driverName + ')'"></span>
                    </template>
                    هي
                    <span class="px-2 py-0.5 font-bold rounded-md bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400" x-text="driverVehicle"></span>
                    بينما نوع المركبة المطلوبة في الطلب هو
                    <span class="px-2 py-0.5 font-bold rounded-md bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400" x-text="requestVehicle"></span>.
                </p>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    يجب اختيار سائق يمتلك نفس نوع المركبة المطلوبة، أو تغيير نوع المركبة في الطلب أولاً من أعلى النافذة.
                </p>

                <div class="flex justify-center mt-5">
                    <button type="button"
                            @click="mismatchOpen = false"
                            class="px-6 py-2 text-sm font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-600 shadow-theme-xs">
                        حسناً، فهمت
                    </button>
                </div>
            </div>
        </div>
    </div>

    @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .z-99999 {
                z-index: 99999 !important;
            }

            .dispatch-modal-box {
                width: 95vw;
                max-width: 1200px;
                height: 88vh;
                max-height: 840px;
                display: flex;
                flex-direction: column;
            }

            .dispatch-split-container {
                display: flex;
                flex-direction: row;
                gap: 16px;
                flex: 1 1 0%;
                min-height: 0;
                overflow: hidden;
            }

            .dispatch-drivers-panel {
                width: 410px;
                min-width: 360px;
                max-width: 430px;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                min-height: 0;
                overflow: hidden;
            }

            .dark .dispatch-drivers-panel {
                background-color: rgba(255, 255, 255, 0.03) !important;
                border-color: #1f2a37 !important;
            }

            .dispatch-filter-pill {
                transition: all 0.2s ease;
            }

            .driver-card-item {
                transition: all 0.2s ease;
            }
            .dark .driver-card-item {
                background-color: #1a2231 !important;
                border-color: #2e394b !important;
                color: rgba(255, 255, 255, 0.9) !important;
            }
            .dark .driver-card-item:hover {
                border-color: #f79009 !important;
                box-shadow: 0 4px 14px rgba(247, 144, 9, 0.15);
            }

            .dispatch-map-panel {
                flex: 1 1 0%;
                position: relative;
                min-height: 0;
                border-radius: 16px;
                overflow: hidden;
            }

            #dispatch-map {
                width: 100%;
                height: 100%;
                position: absolute;
                inset: 0;
            }

            @media (max-width: 860px) {
                .dispatch-modal-box {
                    width: 100vw;
                    height: 94vh;
                    max-height: none;
                }

                .dispatch-split-container {
                    flex-direction: column-reverse;
                }

                .dispatch-drivers-panel {
                    width: 100%;
                    max-width: none;
                    height: 48%;
                }

                .dispatch-map-panel {
                    height: 52%;
                }
            }

            @keyframes radar-ripple {
                0% {
                    transform: scale(0.6);
                    opacity: 0.9;
                }
                70% {
                    transform: scale(2.2);
                    opacity: 0;
                }
                100% {
                    transform: scale(2.4);
                    opacity: 0;
                }
            }

            .pickup-pulse-wrapper {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .pickup-pulse-ring {
                position: absolute;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background-color: rgba(16, 185, 129, 0.35);
                animation: radar-ripple 2.2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            }

            .driver-map-marker {
                background: none !important;
                border: none !important;
                transition: transform 0.2s ease;
            }
            .driver-map-marker:hover {
                transform: scale(1.15);
                z-index: 1000 !important;
            }

            .pickup-marker-icon,
            .dropoff-marker-icon,
            .stop-marker-icon {
                background: none !important;
                border: none !important;
            }

            .map-tooltip-rtl {
                direction: rtl;
                font-family: inherit;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: none;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: rgba(0,0,0,0.12);
                border-radius: 10px;
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: rgba(255,255,255,0.15);
            }

            .leaflet-pane { z-index: 1 !important; }
            .leaflet-top, .leaflet-bottom { z-index: 2 !important; }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            if (typeof window !== 'undefined' && typeof window.assignDriverDispatch !== 'function') {
                const patchLeafletTooltip = () => {
                    if (typeof L !== 'undefined' && L.Tooltip && !L.Tooltip.prototype._patchedSafeUpdate) {
                        const originalUpdatePosition = L.Tooltip.prototype._updatePosition;
                        L.Tooltip.prototype._updatePosition = function () {
                            if (!this._map) return;
                            try {
                                originalUpdatePosition.apply(this, arguments);
                            } catch (e) {}
                        };
                        L.Tooltip.prototype._patchedSafeUpdate = true;
                    }
                };
                patchLeafletTooltip();
                window.addEventListener('load', patchLeafletTooltip);

                let dispatchMap = null;
                let dispatchMarkers = [];
                let dispatchPickupMarker = null;
                let dispatchDropoffMarker = null;
                let dispatchStopMarkers = [];
                let dispatchRadarCircle = null;
                let dispatchPickupLocation = null;

                const DEFAULT_DRIVER_AVATAR = '{{ asset("tailadmin/build/src/images/user/SO.jpg") }}';

                function safeRemoveLayer(layer) {
                    if (!layer || !dispatchMap) return;
                    try {
                        if (typeof layer.closeTooltip === 'function') layer.closeTooltip();
                        if (typeof layer.unbindTooltip === 'function') layer.unbindTooltip();
                        if (typeof layer.closePopup === 'function') layer.closePopup();
                        if (typeof layer.unbindPopup === 'function') layer.unbindPopup();
                        if (dispatchMap.hasLayer(layer)) {
                            dispatchMap.removeLayer(layer);
                        }
                    } catch (err) {
                        console.warn('Error removing Leaflet layer:', err);
                    }
                }

                function clearAllMapLayers() {
                    if (dispatchMarkers && dispatchMarkers.length) {
                        dispatchMarkers.forEach(m => safeRemoveLayer(m));
                        dispatchMarkers = [];
                    }
                    if (dispatchPickupMarker) {
                        safeRemoveLayer(dispatchPickupMarker);
                        dispatchPickupMarker = null;
                    }
                    if (dispatchDropoffMarker) {
                        safeRemoveLayer(dispatchDropoffMarker);
                        dispatchDropoffMarker = null;
                    }
                    if (dispatchRadarCircle) {
                        safeRemoveLayer(dispatchRadarCircle);
                        dispatchRadarCircle = null;
                    }
                    if (dispatchStopMarkers && dispatchStopMarkers.length) {
                        dispatchStopMarkers.forEach(m => safeRemoveLayer(m));
                        dispatchStopMarkers = [];
                    }
                }

                window.assignDriverDispatch = function assignDriverDispatch() {
                    return {
                        show: false,
                        isLoading: false,
                        isSuccess: false,
                        selectedDriverId: null,
                        selectedDriver: null,

                        initMap() {
                            if (typeof L === 'undefined') {
                                setTimeout(() => this.initMap(), 150);
                                return;
                            }

                            const mapEl = this.$refs.dispatchMap;
                            if (!mapEl) return;

                            if (dispatchMap) {
                                try {
                                    dispatchMap.invalidateSize();
                                } catch (e) {}
                                return;
                            }

                            dispatchMap = L.map(mapEl, {
                                center: [15.3694, 44.191],
                                zoom: 13,
                                zoomControl: false,
                                attributionControl: false,
                            });

                            const googleKey = @json(config('services.google.maps_key') ?: env('GOOGLE_MAP_KEY'));
                            const tileUrl = googleKey
                                ? 'https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=ar&gl=YE&key=' + encodeURIComponent(googleKey)
                                : 'https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=ar&gl=YE';

                            L.tileLayer(tileUrl, {
                                maxZoom: 20,
                                subdomains: ['0', '1', '2', '3'],
                                attribution: 'Google Maps'
                            }).addTo(dispatchMap);

                            L.control.zoom({ position: 'bottomleft' }).addTo(dispatchMap);

                            setTimeout(() => {
                                if (dispatchMap) dispatchMap.invalidateSize();
                            }, 250);
                        },

                        invalidateMap() {
                            if (dispatchMap) {
                                try {
                                    dispatchMap.invalidateSize();
                                } catch (e) {}
                            }
                        },

                        syncMap(data) {
                            if (!data) return;
                            if (typeof L === 'undefined' || !dispatchMap) {
                                setTimeout(() => this.syncMap(data), 200);
                                return;
                            }

                            try {
                                dispatchMap.stop();
                            } catch (e) {}

                            clearAllMapLayers();

                            const bounds = L.latLngBounds();
                            let hasValidPickup = false;

                            if (data.pickup && data.pickup.latitude && data.pickup.longitude) {
                                const pickupLatLng = [Number(data.pickup.latitude), Number(data.pickup.longitude)];
                                dispatchPickupLocation = pickupLatLng;
                                hasValidPickup = true;

                                dispatchPickupMarker = L.marker(pickupLatLng, {
                                    icon: L.divIcon({
                                        className: 'pickup-marker-icon',
                                        html: '<div class="pickup-pulse-wrapper">' +
                                                '<div class="pickup-pulse-ring"></div>' +
                                                '<div class="flex relative z-10 justify-center items-center w-10 h-10 text-white bg-emerald-500 rounded-full border-white shadow-xl border-3">' +
                                                    '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>' +
                                                '</div>' +
                                              '</div>',
                                        iconSize: [40, 40],
                                        iconAnchor: [20, 20],
                                    })
                                }).addTo(dispatchMap);

                                if (data.pickup.address) {
                                    dispatchPickupMarker.bindTooltip('موقع العميل: ' + data.pickup.address, { direction: 'top', offset: [0, -20], className: 'map-tooltip-rtl' });
                                }

                                dispatchRadarCircle = L.circle(pickupLatLng, {
                                    radius: 1200,
                                    color: '#10b981',
                                    fillColor: '#10b981',
                                    fillOpacity: 0.08,
                                    weight: 2,
                                    dashArray: '5, 5',
                                }).addTo(dispatchMap);

                                bounds.extend(pickupLatLng);
                            }

                            if (data.dropoff && data.dropoff.latitude && data.dropoff.longitude) {
                                const dropoffLatLng = [Number(data.dropoff.latitude), Number(data.dropoff.longitude)];

                                dispatchDropoffMarker = L.marker(dropoffLatLng, {
                                    icon: L.divIcon({
                                        className: 'dropoff-marker-icon',
                                        html: '<div class="flex justify-center items-center w-9 h-9 text-white bg-red-500 rounded-full border-white shadow-xl border-3">' +
                                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>' +
                                              '</div>',
                                        iconSize: [36, 36],
                                        iconAnchor: [18, 18],
                                    })
                                }).addTo(dispatchMap);

                                if (data.dropoff.address) {
                                    dispatchDropoffMarker.bindTooltip('الوجهة: ' + data.dropoff.address, { direction: 'top', offset: [0, -18], className: 'map-tooltip-rtl' });
                                }

                                bounds.extend(dropoffLatLng);
                            }

                            if (data.stops && data.stops.length) {
                                data.stops.forEach((stop, i) => {
                                    if (!stop.latitude || !stop.longitude) return;
                                    const stopLatLng = [Number(stop.latitude), Number(stop.longitude)];
                                    const stopMarker = L.marker(stopLatLng, {
                                        icon: L.divIcon({
                                            className: 'stop-marker-icon',
                                            html: '<div class="flex justify-center items-center w-7 h-7 text-xs font-bold text-white bg-amber-500 rounded-full border-2 border-white shadow-lg">' + (i + 1) + '</div>',
                                            iconSize: [28, 28],
                                            iconAnchor: [14, 14],
                                        })
                                    }).addTo(dispatchMap);
                                    dispatchStopMarkers.push(stopMarker);
                                    bounds.extend(stopLatLng);
                                });
                            }

                            let nearbyDriversCount = 0;
                            if (data.drivers && data.drivers.length) {
                                data.drivers.forEach(driver => {
                                    if (!driver.latitude || !driver.longitude) return;
                                    const driverLatLng = [Number(driver.latitude), Number(driver.longitude)];

                                    const distBadge = driver.distance_km !== null
                                        ? '<div class="bg-gray-900/90 text-white font-bold text-[10px] px-2 py-0.5 rounded-full shadow-md whitespace-nowrap mb-1 inline-block">' + driver.distance_km + ' كم</div>'
                                        : '';

                                    const onlineRing = driver.is_online ? 'border-emerald-500 ring-2 ring-emerald-400' : 'border-gray-300';
                                    const onlineDot = driver.is_online
                                        ? '<div class="absolute -right-0.5 -bottom-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></div>'
                                        : '<div class="absolute -right-0.5 -bottom-0.5 w-3.5 h-3.5 bg-gray-400 rounded-full border-2 border-white"></div>';

                                    let avatarSrc = driver.driver_image || '';
                                    if (!avatarSrc || avatarSrc.includes('default_driver1.png')) {
                                        avatarSrc = DEFAULT_DRIVER_AVATAR;
                                    }

                                    const marker = L.marker(driverLatLng, {
                                        icon: L.divIcon({
                                            className: 'driver-map-marker',
                                            html: '<div class="flex flex-col items-center cursor-pointer">' +
                                                    distBadge +
                                                    '<div class="relative">' +
                                                        '<img src="' + avatarSrc + '" class="w-10 h-10 rounded-full object-cover border-2 shadow-lg ' + onlineRing + '" onerror="this.onerror=null;this.src=\'' + DEFAULT_DRIVER_AVATAR + '\';" />' +
                                                        onlineDot +
                                                    '</div>' +
                                                  '</div>',
                                            iconSize: [44, 56],
                                            iconAnchor: [22, 50],
                                        })
                                    }).addTo(dispatchMap);

                                    marker.on('click', () => {
                                        this.selectDriver(driver);
                                    });

                                    dispatchMarkers.push(marker);

                                    if (driver.distance_km !== null && driver.distance_km <= 35) {
                                        bounds.extend(driverLatLng);
                                        nearbyDriversCount++;
                                    }
                                });
                            }

                            try {
                                if (hasValidPickup) {
                                    if (nearbyDriversCount > 0 && bounds.isValid()) {
                                        dispatchMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 15, animate: false });
                                    } else {
                                        dispatchMap.setView(dispatchPickupLocation, 14, { animate: false });
                                    }
                                } else if (bounds.isValid()) {
                                    dispatchMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 14, animate: false });
                                }
                            } catch (err) {
                                console.warn('Error adjusting map bounds:', err);
                            }
                        },

                        selectDriver(driver) {
                            this.selectedDriverId = driver.id;
                            this.selectedDriver = driver;

                            if (driver.latitude && driver.longitude && dispatchMap) {
                                try {
                                    dispatchMap.stop();
                                    dispatchMap.flyTo([Number(driver.latitude), Number(driver.longitude)], 15, { duration: 0.8 });
                                } catch (e) {
                                    dispatchMap.setView([Number(driver.latitude), Number(driver.longitude)], 15);
                                }
                            }
                        },

                        recenterOnPickup() {
                            if (dispatchPickupLocation && dispatchMap) {
                                try {
                                    dispatchMap.stop();
                                    dispatchMap.flyTo(dispatchPickupLocation, 14, { duration: 0.7 });
                                } catch (e) {
                                    dispatchMap.setView(dispatchPickupLocation, 14);
                                }
                            }
                        },

                        clearSelection() {
                            this.selectedDriverId = null;
                            this.selectedDriver = null;
                        },

                        flyToDriver(lat, lng, driverId, driverData) {
                            this.selectDriver(driverData);
                        },

                        estimateArrival(distanceKm) {
                            const km = parseFloat(distanceKm);
                            if (!km || isNaN(km) || km <= 0) return '—';

                            const avgSpeedKmPerMin = 0.5;
                            const mins = Math.round(km / avgSpeedKmPerMin);

                            if (mins < 1) return 'أقل من دقيقة';
                            if (mins === 1) return 'دقيقة واحدة';
                            if (mins === 2) return 'دقيقتان';

                            if (mins < 60) {
                                if (mins <= 10) return mins + ' دقائق';
                                return mins + ' دقيقة';
                            }

                            const hours = Math.floor(mins / 60);
                            const remMins = mins % 60;

                            let hourStr = '';
                            if (hours === 1) hourStr = 'ساعة';
                            else if (hours === 2) hourStr = 'ساعتان';
                            else if (hours >= 3 && hours <= 10) hourStr = hours + ' ساعات';
                            else hourStr = hours + ' ساعة';

                            if (remMins === 0) {
                                return hourStr;
                            }

                            let minStr = '';
                            if (remMins === 1) minStr = 'دقيقة واحدة';
                            else if (remMins === 2) minStr = 'دقيقتان';
                            else if (remMins <= 10) minStr = remMins + ' دقائق';
                            else minStr = remMins + ' دقيقة';

                            return hourStr + ' و ' + minStr;
                        }
                    };
                };
            }
        </script>
    @endassets
</div>
