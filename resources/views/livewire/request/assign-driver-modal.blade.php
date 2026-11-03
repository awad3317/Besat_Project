<!-- resources/views/livewire/request/assign-driver-modal.blade.php -->
@php
    $req = $this->selectedRequest;
@endphp

<div x-data="assignDriverDispatch()"
     x-on:open-modal.window="if ($event.detail == 'assign-driver-modal') {
        show = true; isLoading = false; isSuccess = false; clearSelection();
        $nextTick(() => { initMap(); setTimeout(() => { if (map) map.invalidateSize(); }, 300); });
     }"
     x-on:close-modal.window="if ($event.detail == 'assign-driver-modal') { show = false; clearSelection(); }"
     x-on:driver-assigned.window="isLoading = false; isSuccess = true; setTimeout(() => { show = false; isSuccess = false; $wire.clearSelectedRequest(); }, 2000)"
     x-on:sync-dispatch-map.window="syncMap($event.detail[0] || $event.detail)"
     x-on:close.stop="if (!isLoading && !isSuccess) { show = false; }"
     x-on:keydown.escape.window="if (!isLoading && !isSuccess) { show = false; }"
     x-show="show"
     class="fixed inset-0 flex items-center justify-center p-3 sm:p-5 overflow-y-auto modal z-99999"
     style="display: none;">

    {{-- Backdrop (TailAdmin signature blur) --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
         @click="!isLoading && !isSuccess ? show = false : null"></div>

    {{-- Modal Box (Standard TailAdmin rounded-3xl card with split view) --}}
    <div @click.outside="!isLoading && !isSuccess ? show = false : null"
         class="relative w-full rounded-3xl bg-white p-5 lg:p-6 dark:bg-gray-900 z-10 dispatch-modal-box shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden"
         dir="rtl">

        {{-- ════════════ Loading Overlay ════════════ --}}
        <div x-show="isLoading"
             class="absolute inset-0 bg-white/90 dark:bg-gray-900/90 flex flex-col items-center justify-center z-50 rounded-3xl">
            <div class="h-14 w-14 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent mb-4"></div>
            <p class="text-gray-800 dark:text-white font-bold text-base">جاري تعيين السائق للطلب...</p>
            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">يتم الآن تحديث الحالة وإرسال الإشعارات</p>
        </div>

        {{-- ════════════ Success Overlay ════════════ --}}
        <div x-show="isSuccess"
             class="absolute inset-0 bg-white dark:bg-gray-900 flex flex-col items-center justify-center z-50 rounded-3xl">
            <div class="h-16 w-16 bg-success-50 dark:bg-success-500/10 text-success-500 rounded-full flex items-center justify-center mb-4 transition-transform duration-500">
                <svg class="w-10 h-10 animate-bounce" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-2">تم تعيين السائق بنجاح!</h4>
            <p class="text-gray-500 dark:text-gray-400 text-sm">تم إسناد الطلب وإشعار السائق والعميل بنجاح...</p>
        </div>

        {{-- ════════════ Header ════════════ --}}
        <div class="mb-4 pb-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            تعيين سائق للطلب #{{ $selectedRequestId }}
                        </h4>
                        @if($req?->vehicle)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15">
                                {{ $req->vehicle->type }}
                            </span>
                        @endif
                    </div>
                    @if($req)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1.5 truncate">
                            <span class="text-emerald-600 font-semibold">من: {{ $req->start_address ?? 'نقطة الانطلاق' }}</span>
                            <span>←</span>
                            <span class="text-red-500 font-semibold">إلى: {{ $req->end_address ?? 'الوجهة' }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <button x-on:click="!isLoading && !isSuccess ? show = false : null"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ════════════ Split-View Content (Side-by-side in RTL) ════════════ --}}
        <div class="dispatch-split-container">

            {{-- ──────── RIGHT PANEL: Driver List & Filters ──────── --}}
            <div class="dispatch-drivers-panel border border-gray-100 dark:border-gray-800 rounded-2xl p-3 bg-gray-50/60 dark:bg-gray-800/30">

                {{-- Search & Filters --}}
                <div class="mb-3 space-y-2.5 shrink-0">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="driverSearch" type="text"
                               placeholder="ابحث بالاسم أو رقم الهاتف..."
                               class="hover:border-brand-500 dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-white px-4 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white text-right">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg wire:loading.remove wire:target="driverSearch" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <div wire:loading wire:target="driverSearch" class="w-4 h-4 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer select-none text-xs font-medium text-gray-700 dark:text-gray-300 hover:border-brand-500 transition">
                            <input type="checkbox" wire:model.live="onlineDriversOnly"
                                   class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800" />
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            <span>المتصلون فقط</span>
                        </label>
                        <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer select-none text-xs font-medium text-gray-700 dark:text-gray-300 hover:border-brand-500 transition">
                            <input type="checkbox" wire:model.live="matchVehicleOnly"
                                   class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800" />
                            <span>نفس المركبة</span>
                        </label>
                    </div>
                </div>

                {{-- Driver List --}}
                <div class="flex-1 overflow-y-auto pr-1 space-y-2 custom-scrollbar" wire:loading.class="opacity-50" wire:target="driverSearch, onlineDriversOnly, matchVehicleOnly">
                    @forelse($this->drivers as $driver)
                        @php
                            $distKm = isset($driver->distance_km) ? round($driver->distance_km, 1) : null;
                            $driverImage = $driver->driver_image ? url($driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg');
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
                                 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/40 dark:border-brand-500 dark:bg-brand-950/20': selectedDriverId === {{ $driver->id }},
                                 'border-gray-100 dark:border-gray-700 hover:border-brand-200 dark:hover:border-brand-500/30 bg-white dark:bg-gray-800': selectedDriverId !== {{ $driver->id }}
                             }"
                             class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all group shadow-xs">

                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="relative shrink-0">
                                    <div class="h-11 w-11 overflow-hidden rounded-full border {{ $driver->is_online ? 'border-emerald-400 ring-2 ring-emerald-400/20' : 'border-gray-200 dark:border-gray-700' }}">
                                        <img src="{{ $driverImage }}" alt="{{ $driver->name }}" class="h-full w-full object-cover">
                                    </div>
                                    @if($driver->is_online)
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-900"></span>
                                    @else
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-gray-400 rounded-full border-2 border-white dark:border-gray-900"></span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-bold text-sm text-gray-800 dark:text-white group-hover:text-brand-500 transition truncate">
                                            {{ $driver->name }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                        <span>{{ $driver->phone }}</span>
                                        @if($driver->vehicle)
                                            <span>•</span>
                                            <span class="text-gray-600 dark:text-gray-300">{{ $driver->vehicle->type }}</span>
                                        @endif
                                    </div>
                                    @if($distKm !== null)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-500 border border-warning-500/20">
                                                يبعد {{ $distKm }} كم
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                ≈ <span x-text="estimateArrival({{ $distKm }})"></span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button @click.stop="isLoading = true"
                                    wire:click="assignDriver({{ $driver->id }})"
                                    wire:loading.attr="disabled"
                                    class="shrink-0 px-3.5 py-1.5 text-xs font-semibold text-white bg-brand-500 rounded-lg hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 disabled:opacity-50 transition shadow-xs">
                                <span>تعيين</span>
                            </button>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center rounded-xl border border-dashed border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50">
                            <div class="h-12 w-12 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-2.5 text-gray-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 font-medium text-sm">لا يوجد سائقين مطابقين</p>
                            <p class="text-xs text-gray-400 mt-0.5">جرب تعديل خيارات البحث</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ──────── LEFT PANEL: Leaflet Map (Side-by-side) ──────── --}}
            <div class="dispatch-map-panel border border-gray-100 dark:border-gray-800 bg-gray-100 dark:bg-gray-800">
                {{-- Map Container --}}
                <div wire:ignore id="dispatch-map" x-ref="dispatchMap"></div>

                {{-- Map Legend & Recenter Controls --}}
                <div class="absolute top-3 right-3 z-[30] flex flex-col gap-1.5">
                    <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-xl px-3 py-1.5 shadow-md border border-gray-200/80 dark:border-gray-700 flex items-center gap-2.5 text-[11px]">
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">العميل</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">الوجهة</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">السائقون</span>
                        </div>
                    </div>
                </div>

                {{-- Recenter Button on Pickup --}}
                <button @click="recenterOnPickup()"
                        title="الرجوع لموقع العميل"
                        class="absolute top-3 left-3 z-[30] bg-white/95 dark:bg-gray-900/95 backdrop-blur-md text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded-xl shadow-md border border-gray-200/80 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition flex items-center gap-1.5 text-xs font-semibold">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>موقع العميل</span>
                </button>

                {{-- ════════════ Floating Driver Action Card ════════════ --}}
                <div x-show="selectedDriver"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="absolute bottom-3 right-3 left-3 sm:right-auto sm:left-3 sm:w-[380px] z-[40]"
                     dir="rtl">
                    <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-3.5 relative">
                        <button @click="clearSelection()"
                                class="absolute top-2.5 left-2.5 w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                <img :src="selectedDriver?.driver_image" :alt="selectedDriver?.name"
                                     class="w-12 h-12 rounded-xl object-cover border-2 border-brand-500 shadow-sm" />
                                <template x-if="selectedDriver?.is_online">
                                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-900"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-gray-800 dark:text-white text-sm truncate" x-text="selectedDriver?.name"></h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    <span x-text="selectedDriver?.phone"></span> • <span x-text="selectedDriver?.vehicle_type"></span>
                                </p>
                                <template x-if="selectedDriver?.distance_km !== null">
                                    <span class="inline-block mt-1 text-[11px] font-bold text-warning-600" x-text="'يبعد ' + selectedDriver?.distance_km + ' كم (≈ ' + estimateArrival(selectedDriver?.distance_km) + ')'"></span>
                                </template>
                            </div>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-gray-100 dark:border-gray-800">
                            <button @click="isLoading = true"
                                    x-on:click="$wire.assignDriver(selectedDriverId)"
                                    wire:loading.attr="disabled"
                                    class="w-full flex items-center justify-center gap-1.5 h-9 rounded-xl text-xs font-bold text-white bg-brand-500 hover:bg-brand-600 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>تعيين هذا السائق للرحلة الآن</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════ Footer ════════════ --}}
        <div class="mt-3.5 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-end shrink-0">
            <button x-on:click="!isLoading && !isSuccess ? show = false : null" type="button"
                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                إلغاء
            </button>
        </div>
    </div>
</div>
