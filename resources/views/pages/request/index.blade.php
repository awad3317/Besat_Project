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
            height: 86vh;
            max-height: 800px;
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
            width: 400px;
            min-width: 360px;
            max-width: 420px;
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
        function assignDriverDispatch() {
            return {
                show: false,
                isLoading: false,
                isSuccess: false,
                selectedDriverId: null,
                selectedDriver: null,
                map: null,
                markers: [],
                pickupMarker: null,
                dropoffMarker: null,
                stopMarkers: [],
                radarCircle: null,
                pickupLocation: null,

                initMap() {
                    if (typeof L === 'undefined') {
                        setTimeout(() => this.initMap(), 150);
                        return;
                    }

                    if (this.map) {
                        this.map.invalidateSize();
                        return;
                    }

                    const mapEl = this.$refs.dispatchMap;
                    if (!mapEl) return;

                    // Initialize Map with OpenStreetMap free high-resolution tiles
                    this.map = L.map(mapEl, {
                        center: [15.3694, 44.191],
                        zoom: 13,
                        zoomControl: false,
                        attributionControl: false,
                    });

                    // 100% Free OpenStreetMap tile server - No API key required
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);

                    L.control.zoom({ position: 'bottomleft' }).addTo(this.map);
                },

                syncMap(data) {
                    if (!data) return;
                    if (typeof L === 'undefined' || !this.map) {
                        setTimeout(() => this.syncMap(data), 200);
                        return;
                    }

                    // Clear existing layers
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];
                    if (this.pickupMarker) { this.map.removeLayer(this.pickupMarker); this.pickupMarker = null; }
                    if (this.dropoffMarker) { this.map.removeLayer(this.dropoffMarker); this.dropoffMarker = null; }
                    if (this.radarCircle) { this.map.removeLayer(this.radarCircle); this.radarCircle = null; }
                    this.stopMarkers.forEach(m => this.map.removeLayer(m));
                    this.stopMarkers = [];

                    const bounds = L.latLngBounds();
                    let hasValidPickup = false;

                    // ── Pickup Pin + Pulsing Radar ──
                    if (data.pickup && data.pickup.latitude && data.pickup.longitude) {
                        const pickupLatLng = [Number(data.pickup.latitude), Number(data.pickup.longitude)];
                        this.pickupLocation = pickupLatLng;
                        hasValidPickup = true;

                        this.pickupMarker = L.marker(pickupLatLng, {
                            icon: L.divIcon({
                                className: 'pickup-marker-icon',
                                html: '<div class="pickup-pulse-wrapper">' +
                                        '<div class="pickup-pulse-ring"></div>' +
                                        '<div class="flex items-center justify-center w-10 h-10 bg-emerald-500 rounded-full border-3 border-white shadow-xl text-white relative z-10">' +
                                            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>' +
                                        '</div>' +
                                      '</div>',
                                iconSize: [40, 40],
                                iconAnchor: [20, 20],
                            })
                        }).addTo(this.map);

                        if (data.pickup.address) {
                            this.pickupMarker.bindTooltip('موقع العميل: ' + data.pickup.address, { direction: 'top', offset: [0, -20], className: 'map-tooltip-rtl' });
                        }

                        this.radarCircle = L.circle(pickupLatLng, {
                            radius: 1200,
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.08,
                            weight: 2,
                            dashArray: '5, 5',
                        }).addTo(this.map);

                        bounds.extend(pickupLatLng);
                    }

                    // ── Drop-off Pin ──
                    if (data.dropoff && data.dropoff.latitude && data.dropoff.longitude) {
                        const dropoffLatLng = [Number(data.dropoff.latitude), Number(data.dropoff.longitude)];

                        this.dropoffMarker = L.marker(dropoffLatLng, {
                            icon: L.divIcon({
                                className: 'dropoff-marker-icon',
                                html: '<div class="flex items-center justify-center w-9 h-9 bg-red-500 rounded-full border-3 border-white shadow-xl text-white">' +
                                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>' +
                                      '</div>',
                                iconSize: [36, 36],
                                iconAnchor: [18, 18],
                            })
                        }).addTo(this.map);

                        if (data.dropoff.address) {
                            this.dropoffMarker.bindTooltip('الوجهة: ' + data.dropoff.address, { direction: 'top', offset: [0, -18], className: 'map-tooltip-rtl' });
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
                                    html: '<div class="flex items-center justify-center w-7 h-7 bg-amber-500 rounded-full border-2 border-white shadow-lg text-white text-xs font-bold">' + (i + 1) + '</div>',
                                    iconSize: [28, 28],
                                    iconAnchor: [14, 14],
                                })
                            }).addTo(this.map);
                            this.stopMarkers.push(stopMarker);
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
                                ? '<div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></div>'
                                : '<div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-gray-400 rounded-full border-2 border-white"></div>';

                            const avatarSrc = driver.driver_image || '/tailadmin/build/src/images/user/SO.jpg';

                            const marker = L.marker(driverLatLng, {
                                icon: L.divIcon({
                                    className: 'driver-map-marker',
                                    html: '<div class="flex flex-col items-center cursor-pointer">' +
                                            distBadge +
                                            '<div class="relative">' +
                                                '<img src="' + avatarSrc + '" class="w-10 h-10 rounded-full object-cover border-2 shadow-lg ' + onlineRing + '" onerror="this.src=\'/tailadmin/build/src/images/user/SO.jpg\'" />' +
                                                onlineDot +
                                            '</div>' +
                                          '</div>',
                                    iconSize: [44, 56],
                                    iconAnchor: [22, 50],
                                })
                            }).addTo(this.map);

                            marker.on('click', () => {
                                this.selectDriver(driver);
                            });

                            this.markers.push(marker);

                            if (driver.distance_km !== null && driver.distance_km <= 35) {
                                bounds.extend(driverLatLng);
                                nearbyDriversCount++;
                            }
                        });
                    }

                    // Center on pickup location if available
                    if (hasValidPickup) {
                        if (nearbyDriversCount > 0 && bounds.isValid()) {
                            this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                        } else {
                            this.map.setView(this.pickupLocation, 14);
                        }
                    } else if (bounds.isValid()) {
                        this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
                    }
                },

                selectDriver(driver) {
                    this.selectedDriverId = driver.id;
                    this.selectedDriver = driver;

                    if (driver.latitude && driver.longitude && this.map) {
                        this.map.flyTo([Number(driver.latitude), Number(driver.longitude)], 15, { duration: 0.8 });
                    }
                },

                recenterOnPickup() {
                    if (this.pickupLocation && this.map) {
                        this.map.flyTo(this.pickupLocation, 14, { duration: 0.7 });
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
                    if (!distanceKm || distanceKm <= 0) return '—';
                    const avgSpeedKmPerMin = 0.5;
                    const mins = Math.ceil(distanceKm / avgSpeedKmPerMin);
                    if (mins < 1) return 'أقل من دقيقة';
                    if (mins === 1) return 'دقيقة واحدة';
                    if (mins === 2) return 'دقيقتان';
                    if (mins <= 10) return mins + ' دقائق';
                    return mins + ' دقيقة';
                }
            };
        }
    </script>
@endsection
