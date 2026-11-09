@php
    $req = $this->selectedRequest;
@endphp

<div x-data="assignDriverDispatch()"
     x-on:open-modal.window="if ($event.detail == 'assign-driver-modal') {
        show = true; isLoading = false; isSuccess = false; clearSelection();
        $nextTick(() => { initMap(); setTimeout(() => { invalidateMap(); }, 300); });
     }"
     x-on:close-modal.window="if ($event.detail == 'assign-driver-modal') { show = false; clearSelection(); }"
     x-on:driver-assigned.window="isLoading = false; isSuccess = true; setTimeout(() => { show = false; isSuccess = false; $wire.clearSelectedRequest(); }, 2000)"
     x-on:sync-dispatch-map.window="syncMap($event.detail[0] || $event.detail)"
     x-on:close.stop="if (!isLoading && !isSuccess) { show = false; }"
     x-on:keydown.escape.window="if (!isLoading && !isSuccess) { show = false; }"
     x-show="show"
     class="flex overflow-y-auto fixed inset-0 justify-center items-center p-3 modal z-99999"
     style="display: none;">

    {{-- Backdrop (TailAdmin signature blur) --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
         @click="!isLoading && !isSuccess ? show = false : null"></div>

    {{-- Modal Box (Standard TailAdmin rounded-3xl card with split view) --}}
    <div @click.outside="!isLoading && !isSuccess ? show = false : null"
         class="overflow-hidden relative z-10 p-5 w-full bg-white rounded-3xl border border-gray-200 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800 dispatch-modal-box"
         dir="rtl">

        {{-- ════════════ Loading Overlay ════════════ --}}
        <div x-show="isLoading"
             class="flex absolute inset-0 z-50 flex-col justify-center items-center rounded-3xl"
             style="background-color: rgba(255, 255, 255, 0.92);">
            <div class="mb-4 w-12 h-12 rounded-full border-4 border-solid animate-spin border-brand-500 border-t-transparent"></div>
            <p class="text-base font-bold text-gray-800 dark:text-white">جاري تعيين السائق للطلب...</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يتم الآن تحديث الحالة وإرسال الإشعارات</p>
        </div>

        {{-- ════════════ Success Overlay ════════════ --}}
        <div x-show="isSuccess"
             class="flex absolute inset-0 z-50 flex-col justify-center items-center bg-white rounded-3xl dark:bg-gray-900">
            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-success-50 text-success-500 dark:bg-success-500/15">
                <svg class="w-10 h-10 text-success-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h4 class="mb-2 text-xl font-bold text-gray-800 dark:text-white">تم تعيين السائق بنجاح!</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">تم إسناد الطلب وإشعار السائق والعميل بنجاح...</p>
        </div>

        {{-- ════════════ Header ════════════ --}}
        <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-100 dark:border-gray-800" style="flex-shrink: 0;">
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-11 h-11 rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex gap-2 items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            تعيين سائق للطلب #{{ $selectedRequestId }}
                        </h4>
                        @if($req?->vehicle)
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-light-50 text-blue-light-500">
                                {{ $req->vehicle->type }}
                            </span>
                        @endif
                    </div>
                    @if($req)
                        <p class="flex gap-2 items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-success-600">من: {{ $req->start_address ?? 'نقطة الانطلاق' }}</span>
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
            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200 dispatch-drivers-panel dark:border-gray-800 dark:bg-gray-900">

                {{-- Search & Filters: Uses flex-col with gap-3 (TailAdmin compliant, replaces missing space-y-2.5) --}}
                <div class="flex flex-col gap-3 mb-3" style="flex-shrink: 0;">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="driverSearch" type="text"
                               placeholder="ابحث بالاسم أو الهاتف أو اللوحة..."
                               class="px-4 py-2.5 pr-10 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 hover:border-brand-500">
                        <div class="flex absolute inset-y-0 right-0 items-center pr-3 text-gray-400" style="pointer-events: none;">
                            <svg wire:loading.remove wire:target="driverSearch" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <div wire:loading wire:target="driverSearch" class="w-5 h-5 rounded-full border-2 border-solid animate-spin border-brand-500 border-t-transparent"></div>
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    <div class="flex gap-2 items-center">
                        <label class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white rounded-lg border border-gray-200 cursor-pointer select-none shadow-theme-xs hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="onlineDriversOnly"
                                   class="rounded border-gray-300 text-brand-500 focus:ring-1 focus:ring-brand-500" style="width: 16px; height: 16px;" />
                            <span class="w-2 h-2 rounded-full bg-success-500" style="flex-shrink: 0;"></span>
                            <span>المتصلون فقط</span>
                        </label>
                        <label class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white rounded-lg border border-gray-200 cursor-pointer select-none shadow-theme-xs hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="matchVehicleOnly"
                                   class="rounded border-gray-300 text-brand-500 focus:ring-1 focus:ring-brand-500" style="width: 16px; height: 16px;" />
                            <span>نفس المركبة</span>
                        </label>
                    </div>

                    {{-- Counter Bar --}}
                    <div class="flex justify-between items-center px-1 text-gray-500 text-theme-xs dark:text-gray-400">
                        <span>السائقون المتاحون ({{ count($this->drivers) }})</span>
                        <span class="text-xs">مرتبة حسب الأقرب</span>
                    </div>
                </div>

                {{-- Driver List --}}
                <div class="overflow-y-auto flex-1 px-1 py-1 space-y-1 custom-scrollbar" wire:loading.class="opacity-75" wire:target="driverSearch, onlineDriversOnly, matchVehicleOnly">
                    @forelse($this->drivers as $driver)
                        @php
                            $distKm = isset($driver->distance_km) ? round($driver->distance_km, 1) : null;
                            $driverImg = $driver->driver_image;
                            $driverImage = (!$driverImg || str_contains($driverImg, 'default_driver1.png'))
                                ? asset('tailadmin/build/src/images/user/SO.jpg')
                                : url($driverImg);
                            $vehicleTypeName = $driver->vehicle?->type ?? 'غير محدد';
                        @endphp
                        <div @click="flyToDriver({{ $driver->latitude ?? 'null' }}, {{ $driver->longitude ?? 'null' }}, {{ $driver->id }}, {
                                id: {{ $driver->id }},
                                name: '{{ addslashes($driver->name) }}',
                                phone: '{{ $driver->phone }}',
                                plate_number: '{{ $driver->plate_number }}',
                                vehicle_type: '{{ addslashes($vehicleTypeName) }}',
                                driver_image: '{{ $driverImage }}',
                                is_online: {{ $driver->is_online ? 'true' : 'false' }},
                                latitude: {{ $driver->latitude ?? 'null' }},
                                longitude: {{ $driver->longitude ?? 'null' }},
                                distance_km: {{ $distKm ?? 'null' }}
                             })"
                             :class="{
                                 'border-brand-500 bg-brand-50 dark:bg-brand-950 dark:border-brand-500': selectedDriverId === {{ $driver->id }},
                                 'border-gray-200 bg-white hover:border-brand-500 dark:border-gray-700 dark:bg-gray-800': selectedDriverId !== {{ $driver->id }}
                             }"
                             class="flex justify-between items-center p-3 rounded-xl border transition cursor-pointer driver-card-item shadow-theme-xs">

                            <div class="flex flex-1 gap-3 items-center" style="min-width: 0;">
                                <div class="relative" style="flex-shrink: 0;">
                                    <div class="h-11 w-11 overflow-hidden rounded-full border-2 {{ $driver->is_online ? 'border-success-500' : 'border-gray-200 dark:border-gray-700' }}">
                                        <img src="{{ $driverImage }}" alt="{{ $driver->name }}" class="w-full h-full" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}';">
                                    </div>
                                    @if($driver->is_online)
                                        <span class="absolute rounded-full border-2 border-white bg-success-500 dark:border-gray-900" style="width: 12px; height: 12px; bottom: -1px; right: -1px;" title="متصل"></span>
                                    @else
                                        <span class="absolute bg-gray-400 rounded-full border-2 border-white dark:border-gray-900" style="width: 12px; height: 12px; bottom: -1px; right: -1px;" title="غير متصل"></span>
                                    @endif
                                </div>
                                <div class="flex-1" style="min-width: 0;">
                                    <div class="flex gap-1.5 items-center">
                                        <p class="text-sm font-bold text-gray-800 dark:text-white/90" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $driver->name }}
                                        </p>
                                    </div>
                                    <div class="flex gap-1.5 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $driver->phone }}</span>
                                        @if($driver->vehicle)
                                            <span>•</span>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $driver->vehicle->type }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center mt-1">
                                        @if($distKm !== null)
                                            <span class="inline-flex gap-1 items-center px-2 py-0.5 text-xs font-bold rounded-md border bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400 border-warning-500">
                                                <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px; flex-shrink: 0;"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                                <span>يبعد {{ $distKm }} كم</span>
                                                <span class="text-gray-400">•</span>
                                                <span>≈ <span x-text="estimateArrival({{ $distKm }})"></span></span>
                                            </span>
                                        @else
                                            <span class="inline-flex gap-1 items-center px-2 py-0.5 text-xs text-gray-400 bg-gray-100 rounded-md dark:bg-gray-700">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>الموقع غير محدد</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button @click.stop="isLoading = true"
                                    wire:click="assignDriver({{ $driver->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 mr-2 text-xs font-medium text-white rounded-lg transition bg-brand-500 hover:bg-brand-600 shadow-theme-xs" style="flex-shrink: 0;">
                                <span>تعيين</span>
                            </button>
                        </div>
                    @empty
                        <div class="flex flex-col justify-center items-center py-8 text-center bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800" style="border-style: dashed;">
                            <div class="flex justify-center items-center mb-3 w-12 h-12 text-gray-400 bg-gray-100 rounded-full dark:bg-gray-700">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">لا يوجد سائقين مطابقين</p>
                            <p class="mt-1 text-xs text-gray-400">جرب تعديل خيارات البحث</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ──────── LEFT PANEL: Leaflet Map (Side-by-side) ──────── --}}
            <div class="bg-gray-100 rounded-2xl border border-gray-200 dispatch-map-panel dark:border-gray-800 dark:bg-gray-800">
                {{-- Map Container --}}
                <div wire:ignore id="dispatch-map" x-ref="dispatchMap"></div>

                {{-- Map Legend --}}
                <div class="flex absolute z-30 flex-col gap-1" style="top: 12px; right: 12px;">
                    <div class="flex gap-2 items-center px-3 py-1.5 text-xs bg-white rounded-xl border border-gray-200 shadow-theme-sm dark:border-gray-700 dark:bg-gray-900">
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
                <button @click="recenterOnPickup()"
                        title="الرجوع لموقع العميل"
                        class="flex absolute z-30 gap-1.5 items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white rounded-xl border border-gray-200 transition shadow-theme-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:hover:bg-gray-800"
                        style="top: 12px; left: 12px;">
                    <svg class="text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>موقع العميل</span>
                </button>

                {{-- Floating Driver Action Card --}}
                <div x-show="selectedDriver"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute z-40"
                     style="bottom: 12px; right: 12px; left: 12px; max-width: 380px;"
                     dir="rtl">
                    <div class="relative p-4 bg-white rounded-2xl border border-gray-200 shadow-theme-lg dark:border-gray-700 dark:bg-gray-900">
                        <button @click="clearSelection()"
                                class="flex absolute justify-center items-center w-7 h-7 text-gray-400 bg-gray-100 rounded-lg transition hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                                style="top: 10px; left: 10px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <div class="flex gap-3 items-center">
                            <div class="relative" style="flex-shrink: 0;">
                                <img :src="selectedDriver?.driver_image && !selectedDriver?.driver_image.includes('default_driver1.png') ? selectedDriver.driver_image : '{{ asset('tailadmin/build/src/images/user/SO.jpg') }}'" :alt="selectedDriver?.name"
                                     x-on:error="$event.target.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}'"
                                     class="w-12 h-12 rounded-xl border-2 border-brand-500 shadow-theme-xs" style="object-fit: cover;" />
                                <template x-if="selectedDriver?.is_online">
                                    <span class="absolute rounded-full border-2 border-white bg-success-500 dark:border-gray-900" style="width: 12px; height: 12px; bottom: -2px; right: -2px;"></span>
                                </template>
                            </div>
                            <div class="flex-1" style="min-width: 0;">
                                <h5 class="text-sm font-bold text-gray-800 dark:text-white/90" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="selectedDriver?.name"></h5>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="selectedDriver?.phone"></span> • <span x-text="selectedDriver?.vehicle_type"></span>
                                </p>
                                <template x-if="selectedDriver?.distance_km !== null">
                                    <span class="inline-block mt-1 text-xs font-bold text-warning-600 dark:text-warning-400" x-text="'يبعد ' + selectedDriver?.distance_km + ' كم (≈ ' + estimateArrival(selectedDriver?.distance_km) + ')'"></span>
                                </template>
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-t border-gray-100 dark:border-gray-800">
                            <button @click="isLoading = true"
                                    x-on:click="$wire.assignDriver(selectedDriverId)"
                                    wire:loading.attr="disabled"
                                    class="flex gap-2 justify-center items-center w-full h-10 text-xs font-medium text-white rounded-lg transition bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>تعيين هذا السائق للرحلة الآن</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scoped Styles for Dispatch Modal to ensure perfect display --}}
    <style>
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
        .dispatch-map-panel {
            flex: 1 1 0%;
            position: relative;
            min-height: 0;
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
    </style>
</div>
