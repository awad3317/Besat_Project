@extends('layouts.app')
@section('title', 'إدارة الطلبات العامة')
@section('Breadcrumb', 'إدارة الطلبات العامة')
@section('addButton')
<a href="{{ route('request.create') }}"
    class="bg-brand-500 hover:bg-warning-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px] inline-flex items-center justify-center transition shadow-xs">
    إنشاء رحلة جديدة
</a>
<x-modals.success-modal />
<x-modals.error-modal />
@endsection

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Force modal container above everything */
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

        /* Split View: Side-by-side layout (RTL: Drivers on Right, Map on Left) */
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

        /* Pulse Radar Animation */
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

        /* Marker Styles */
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

        /* RTL Leaflet Tooltip */
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

        /* Custom Scrollbar */
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
@endsection

@section('content')
    <livewire:request.index />
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ── 1. Defensive Tooltip Patch (Protects against Leaflet internal latLngToLayerPoint null crash) ──
        if (typeof window !== 'undefined') {
            const patchLeafletTooltip = () => {
                if (typeof L !== 'undefined' && L.Tooltip && !L.Tooltip.prototype._patchedSafeUpdate) {
                    const originalUpdatePosition = L.Tooltip.prototype._updatePosition;
                    L.Tooltip.prototype._updatePosition = function () {
                        if (!this._map) return;
                        try {
                            originalUpdatePosition.apply(this, arguments);
                        } catch (e) {
                            // Suppress latLngToLayerPoint crash if layer is detached mid-animation
                        }
                    };
                    L.Tooltip.prototype._patchedSafeUpdate = true;
                }
            };
            patchLeafletTooltip();
            window.addEventListener('load', patchLeafletTooltip);
        }

        // ── 2. Decoupled Leaflet Objects (Stored OUTSIDE Alpine x-data reactive Proxy) ──
        let dispatchMap = null;
        let dispatchMarkers = [];
        let dispatchPickupMarker = null;
        let dispatchDropoffMarker = null;
        let dispatchStopMarkers = [];
        let dispatchRadarCircle = null;
        let dispatchPickupLocation = null;

        const DEFAULT_DRIVER_AVATAR = '{{ asset("tailadmin/build/src/images/user/SO.jpg") }}';

        // Helper to safely detach layers, popups, and tooltips before removing from map
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

        function assignDriverDispatch() {
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

                    // Google Maps Roadmap Tiles: Arabic labels, Yemen region, fast, 100% UNBLOCKED
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

                    // Stop any ongoing animations to prevent frame conflicts
                    try {
                        dispatchMap.stop();
                    } catch (e) {}

                    // Clear existing layers safely without leaving orphaned tooltips
                    clearAllMapLayers();

                    const bounds = L.latLngBounds();
                    let hasValidPickup = false;

                    // ── Pickup Pin + Pulsing Radar ──
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

                    // ── Drop-off Pin ──
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

                    // ── Intermediate Stops ──
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

                    // ── Driver Markers ──
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

                            // Clean avatar path fallback (prevents 404 for default_driver1.png)
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

                    // Center on pickup location or bounds safely without animation race conditions
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
        }
    </script>
@endsection
