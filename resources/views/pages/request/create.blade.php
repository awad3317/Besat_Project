@extends('layouts.app')
@section('title', 'إنشاء رحلة جديدة')
@section('Breadcrumb', 'إنشاء رحلة جديدة')

@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('style')
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .dark #map {
            border-color: #4b5563;
        }

        .map-container {
            position: relative;
        }

        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .map-btn {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            margin-top: 70%;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .map-btn:hover {
            background: #f3f4f6;
        }

        .dark .map-btn {
            background: #374151;
            border-color: #4b5563;
            color: white;
        }

        .dark .map-btn:hover {
            background: #4b5563;
        }

        .location-marker {
            position: absolute;
            transform: translate(-50%, -100%);
            color: #ef4444;
            font-size: 24px;
        }

        .start-marker {
            color: #10b981;
        }

        .end-marker {
            color: #ef4444;
        }

        .coordinates-input {
            font-family: monospace;
            font-size: 12px;
        }

        .route-info {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .dark .route-info {
            background: #1f2937;
        }
    </style>
@endsection

@section('content')
    <div x-data="{
        loadingPrice: false,
        loadingSubmit: false,
        showErrorModal: false,
        errorMessage: null,
        showPriceModal: false,
        calculatedPrice: null,
        distanceInKm: null,
        vehicle: null,
        coupon: null,
        discount_amount: 0,
        original_price: null,
    }" class="p-6 w-full bg-white rounded-3xl dark:bg-gray-900">
        <form method="POST" id="tripForm" action="{{ route('request.store') }}" enctype="multipart/form-data"
            @submit="loadingSubmit = true">
            @csrf
    
            <div class="col-span-2 mb-6">
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                    تحديد المسار على الخريطة
                </label>
                <input type="hidden" id="distance_km" name="distance_km" value="{{ old('distance_km') }}">
                <div class="mb-4 map-container" wire:ignore>
                    <div id="map"></div>
                    <div class="mb-6 map-controls">
                        <button type="button" id="clearRouteBtn" class="map-btn">
                            <i class="fas fa-trash"></i>
                            مسح المسار
                        </button>
                    </div>
                </div>
                <!-- معلومات المسار -->
                <div id="routeInfo" class="hidden route-info">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">المسافة:</span>
                            <span id="distance" class="font-medium">--</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">الوقت المتوقع:</span>
                            <span id="duration" class="font-medium">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-2">
                <!-- نقطة البداية -->
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        نقطة البداية <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <div class="flex flex-col space-y-2">
                        <input type="text" name="start_address" placeholder="مثال: المنصورة - سوق القات"
                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            value="{{ old('start_address') }}">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="hidden" id="start_latitude" name="start_latitude"
                                value="{{ old('start_latitude') }}">
                            <input type="hidden" id="start_longitude" name="start_longitude"
                                value="{{ old('start_longitude') }}">
                            <div class="text-xs text-gray-500 coordinates-input">
                                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                                    id="startLatDisplay">{{ old('start_latitude', '--') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 coordinates-input">
                                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                                    id="startLngDisplay">{{ old('start_longitude', '--') }}</span>
                            </div>
                        </div>
                    </div>
                    @error('start_address')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- نقطة النهاية -->
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        نقطة النهاية <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <div class="flex flex-col space-y-2">
                        <input type="text" id="end_address" name="end_address" placeholder="مثال: المعلا - اسكريم المعلا"
                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            value="{{ old('end_address') }}">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="hidden" id="end_latitude" name="end_latitude" value="{{ old('end_latitude') }}">
                            <input type="hidden" id="end_longitude" name="end_longitude"
                                value="{{ old('end_longitude') }}">
                            <div class="text-xs text-gray-500 coordinates-input">
                                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                                    id="endLatDisplay">{{ old('end_latitude', '--') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 coordinates-input">
                                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                                    id="endLngDisplay">{{ old('end_longitude', '--') }}</span>
                            </div>
                        </div>
                    </div>
                    @error('end_address')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="stopsInputs"></div>
            <!-- حاوية نقاط التوقف -->
            <div id="waypointsContainer" class="col-span-2 my-4 space-y-3 sm:col-span-2"></div>

            <livewire:request.create />


            <!-- أزرار التحكم -->

            <!-- أزرار التحكم -->
            <div class="flex gap-3 justify-end items-center mt-6 w-full">

                {{-- زر السعر تم حذفه لأنه يتم حسابه تلقائياً عبر Livewire --}}

                {{-- زر إنشاء الرحلة --}}
                <button type="submit" :disabled="loadingSubmit"
                    class="flex justify-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg hover:bg-brand-600 bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loadingSubmit">إنشاء رحلة</span>
                    <span x-show="loadingSubmit" class="flex gap-2 items-center">
                        <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        جاري الإنشاء...
                    </span>
                </button>
            </div>
        </form>
        @include('pages.specialOrder.show-price-modal')
    </div>
    {{-- 3. إضافة المودال الخاص بعرض السعر --}}

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&language=ar&region=YE"
        async defer></script>
    <script>
        let map;
        let startMarker = null;
        let endMarker = null;
        let geocoder;
        let directionsService, directionsRenderer;
        let waypoints = [];
        let customMarkers = [];


        function initializeMap() {
            if (!document.getElementById('map')) return;

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: '#F58A07',
                    strokeWeight: 5
                } // Orange thick line like app
            });

            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 12.7773,
                    lng: 45.0336
                }, // عدن - كريتر
                zoom: 13
            });

            directionsRenderer.setMap(map);
            geocoder = new google.maps.Geocoder();

            map.addListener('click', function(event) {
                let latLng = event.latLng;

                Swal.fire({
                    title: 'تحديد نقطة المسار',
                    text: 'ماذا تريد أن تعين هذه النقطة؟',
                    icon: 'question',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'نقطة انطلاق',
                    denyButtonText: 'نقطة توقف',
                    cancelButtonText: 'نقطة وصول',
                    confirmButtonColor: '#28a745',
                    denyButtonColor: '#F58A07',
                    cancelButtonColor: '#dc3545',
                }).then((result) => {
                    if (result.isConfirmed) {
                        setStartPoint(latLng);
                    } else if (result.isDenied) {
                        addWaypoint(latLng);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        setEndPoint(latLng);
                    }
                });
            });

            document.getElementById('clearRouteBtn')?.addEventListener('click', clearMarkers);
        }

        function setStartPoint(latLng) {
            document.getElementById('start_latitude').value = latLng.lat();
            document.getElementById('start_longitude').value = latLng.lng();
            document.getElementById('startLatDisplay').textContent = latLng.lat().toFixed(6);
            document.getElementById('startLngDisplay').textContent = latLng.lng().toFixed(6);

            geocoder.geocode({
                location: latLng
            }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    document.querySelector('input[name="start_address"]').value = results[0].formatted_address;
                }
            });

            startMarker = {
                lat: latLng.lat(),
                lng: latLng.lng()
            };
            calculateAndDisplayRoute();
        }

        function setEndPoint(latLng) {
            document.getElementById('end_latitude').value = latLng.lat();
            document.getElementById('end_longitude').value = latLng.lng();
            document.getElementById('endLatDisplay').textContent = latLng.lat().toFixed(6);
            document.getElementById('endLngDisplay').textContent = latLng.lng().toFixed(6);

            geocoder.geocode({
                location: latLng
            }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    document.getElementById('end_address').value = results[0].formatted_address;
                }
            });

            endMarker = {
                lat: latLng.lat(),
                lng: latLng.lng()
            };
            calculateAndDisplayRoute();
        }

        function addWaypoint(latLng) {
            geocoder.geocode({
                location: latLng
            }, (results, status) => {
                let address = 'نقطة توقف ' + (waypoints.length + 1);
                if (status === 'OK' && results[0]) {
                    address = results[0].formatted_address;
                }
                waypoints.push({
                    location: latLng,
                    stopover: true,
                    address: address
                });
                renderWaypointsUI();
                updateHiddenStopsInputs();
                calculateAndDisplayRoute();
            });
        }

        function removeWaypoint(index) {
            waypoints.splice(index, 1);
            renderWaypointsUI();
            updateHiddenStopsInputs();
            calculateAndDisplayRoute();
        }

        function moveWaypointUp(index) {
            if (index > 0) {
                const temp = waypoints[index];
                waypoints[index] = waypoints[index - 1];
                waypoints[index - 1] = temp;
                renderWaypointsUI();
                updateHiddenStopsInputs();
                calculateAndDisplayRoute();
            }
        }

        function moveWaypointDown(index) {
            if (index < waypoints.length - 1) {
                const temp = waypoints[index];
                waypoints[index] = waypoints[index + 1];
                waypoints[index + 1] = temp;
                renderWaypointsUI();
                updateHiddenStopsInputs();
                calculateAndDisplayRoute();
            }
        }

        let draggedWaypointIndex = null;

        function dragStart(event, index) {
            draggedWaypointIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            setTimeout(() => {
                event.target.classList.add('opacity-50');
            }, 0);
        }

        function dragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
        }

        function drop(event, dropIndex) {
            event.preventDefault();
            if (draggedWaypointIndex !== null && draggedWaypointIndex !== dropIndex) {
                const item = waypoints.splice(draggedWaypointIndex, 1)[0];
                waypoints.splice(dropIndex, 0, item);
                renderWaypointsUI();
                updateHiddenStopsInputs();
                calculateAndDisplayRoute();
            }
        }

        function dragEnd(event) {
            event.target.classList.remove('opacity-50');
            draggedWaypointIndex = null;
        }

        function renderWaypointsUI() {
            const container = document.getElementById('waypointsContainer');
            container.innerHTML = '';

            if (waypoints.length > 0) {
                let html =
                    '<label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">نقاط التوقف</label>';
                html += '<div class="space-y-2">';
                waypoints.forEach((wp, index) => {
                    html += `
                    <div draggable="true" ondragstart="dragStart(event, ${index})" ondragover="dragOver(event)" ondrop="drop(event, ${index})" ondragend="dragEnd(event)" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200 transition-colors dark:bg-gray-800 dark:border-gray-700 cursor-grab active:cursor-grabbing hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex gap-3 items-center pointer-events-none">
                            <div class="flex justify-center items-center w-8 h-8 text-sm font-bold text-blue-600 bg-blue-100 rounded-full">
                                ${index + 1}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-800 dark:text-gray-200 truncate max-w-[200px] sm:max-w-md" title="${wp.address}">${wp.address}</span>
                                <span class="text-xs text-gray-500">
                                    خط العرض: <span class="text-warning-500">${wp.location.lat().toFixed(6)}</span> | 
                                    خط الطول: <span class="text-warning-500">${wp.location.lng().toFixed(6)}</span>
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 items-center">
                            ${index > 0 ? `<button type="button" onclick="moveWaypointUp(${index})" class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300" title="تحريك النقطة لأعلى">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        </button>` : `<div class="w-6 h-6"></div>`}
                            ${index < waypoints.length - 1 ? `<button type="button" onclick="moveWaypointDown(${index})" class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300" title="تحريك النقطة لأسفل">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>` : `<div class="w-6 h-6"></div>`}
                            <button type="button" onclick="removeWaypoint(${index})" class="text-red-500 transition-colors hover:text-red-700" title="حذف النقطة">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        }

        function updateHiddenStopsInputs() {
            const container = document.getElementById('stopsInputs');
            container.innerHTML = '';
            waypoints.forEach((wp, index) => {
                container.innerHTML +=
                    `<input type="hidden" name="stops[${index}][latitude]" value="${wp.location.lat()}">`;
                container.innerHTML +=
                    `<input type="hidden" name="stops[${index}][longitude]" value="${wp.location.lng()}">`;
            });
        }

        function calculateAndDisplayRoute() {
            // Clear current map markers
            customMarkers.forEach(marker => marker.setMap(null));
            customMarkers = [];

            // Draw Custom Markers (using google maps default colored pins to simulate app icons)
            if (startMarker) drawCustomMarker(new google.maps.LatLng(startMarker.lat, startMarker.lng), 'start');
            if (endMarker) drawCustomMarker(new google.maps.LatLng(endMarker.lat, endMarker.lng), 'end');
            waypoints.forEach((wp, index) => {
                drawCustomMarker(wp.location, 'stop', index + 1);
            });

            if (startMarker && endMarker) {
                // تحويل مصفوفة التوقفات إلى waypoints المطلوبة في API جوجل
                const formattedWaypoints = waypoints.map(wp => ({
                    location: wp.location,
                    stopover: true // إجبار المرور عبر النقاط
                }));

                directionsService.route({
                    origin: new google.maps.LatLng(startMarker.lat, startMarker.lng),
                    destination: new google.maps.LatLng(endMarker.lat, endMarker.lng),
                    waypoints: formattedWaypoints, // تضمين نقاط التوقف المنسقة
                    optimizeWaypoints: false, // الحفاظ على الترتيب الأصلي
                    travelMode: 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(response);
                        const route = response.routes[0];
                        let totalDistance = 0;
                        let totalDuration = 0;

                        // جمع المسافات والوقت لجميع أجزاء الرحلة (من البداية مروراً بالتوقفات حتى النهاية)
                        for (let i = 0; i < route.legs.length; i++) {
                            totalDistance += route.legs[i].distance.value;
                            totalDuration += route.legs[i].duration.value;
                        }
                        window.totalRouteDistanceInMeters =
                            totalDistance; // تخزين المسافة الإجمالية لتمريرها للباك إند

                        document.getElementById('routeInfo').classList.remove('hidden');
                        document.getElementById('distance').textContent = (totalDistance / 1000).toFixed(2) + ' كم';
                        document.getElementById('duration').textContent = Math.round(totalDuration / 60) + ' دقيقة';
                        document.getElementById('distance_km').value =
                            (totalDistance / 1000).toFixed(6);
                        // إرسال المسافة المحسوبة إلى مكون Livewire لتحديث بطاقة السعر تلقائياً
                        if (window.Livewire) {
                            window.Livewire.dispatch('updateDistance', {
                                distanceInMeters: totalDistance
                            });
                        }
                    } else {
                        window.alert('تعذر حساب المسار: ' + status);
                    }
                });
            } else {
                document.getElementById('routeInfo').classList.add('hidden');
                directionsRenderer.setDirections({
                    routes: []
                }); // Clear route line
            }
        }

        function drawCustomMarker(position, type, index = 1) {
            let iconUrl = '';
            let labelText = '';

            if (type === 'start') {
                iconUrl = 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png'; // Matches App orange start pin
            } else if (type === 'end') {
                iconUrl = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'; // Matches App red end pin
            } else {
                iconUrl = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'; // Matches App blue stop pin
                labelText = index.toString();
            }

            let marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: iconUrl,
                label: labelText ? {
                    text: labelText,
                    color: 'white',
                    fontWeight: 'bold'
                } : null
            });
            customMarkers.push(marker);
        }

        function clearMarkers() {
            startMarker = null;
            endMarker = null;
            waypoints = [];
            renderWaypointsUI();
            updateHiddenStopsInputs();
            calculateAndDisplayRoute();

            ['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.querySelector('input[name="start_address"]').value = '';
            document.getElementById('end_address').value = '';

            ['startLatDisplay', 'startLngDisplay', 'endLatDisplay', 'endLngDisplay'].forEach(id => {
                document.getElementById(id).textContent = '--';
            });
        }

        if (window.google && window.google.maps) {
            initializeMap();
        } else {
            window.addEventListener('load', initializeMap);
        }
    </script>
@endsection
