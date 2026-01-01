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
  <div x-data="{ loadingPrice: false,
                showErrorModal: false,
                errorMessage: null,
                showPriceModal: false,
                calculatedPrice: null,
                distanceInKm: null,
                vehicle: null,
                coupon: null,
                discount_amount: 0,
                original_price: null,
                }" class="w-full rounded-3xl bg-white p-6 dark:bg-gray-900">
    <form method="POST" id="tripForm" action="{{ route('specialOrder.store') }}" enctype="multipart/form-data" id="tripForm">
      @csrf
      <div class="col-span-2 mb-6">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
          تحديد المسار على الخريطة
        </label>

        <div class="map-container mb-4">
          <div id="map"></div>
          <div class="map-controls mb-6">
            <button type="button" id="clearRouteBtn" class="map-btn">
              <i class="fas fa-trash"></i>
              مسح المسار
            </button>
          </div>
        </div>
        <!-- معلومات المسار -->
        <div id="routeInfo" class="route-info hidden">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
      <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
        <!-- نقطة البداية -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            نقطة البداية <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
          </label>
          <div class="flex flex-col space-y-2">
            <input type="text" name="start_address" placeholder="مثال: المنصورة - سوق القات"
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
              value="{{ old('start_address') }}">
            <div class="grid grid-cols-2 gap-2">
              <input type="hidden" id="start_latitude" name="start_latitude" value="{{ old('start_latitude') }}">
              <input type="hidden" id="start_longitude" name="start_longitude" value="{{ old('start_longitude') }}">
              <div class="coordinates-input text-xs text-gray-500">
                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                  id="startLatDisplay">{{ old('start_latitude', '--') }}</span>
              </div>
              <div class="coordinates-input text-xs text-gray-500">
                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                  id="startLngDisplay">{{ old('start_longitude', '--') }}</span>
              </div>
            </div>
          </div>
          @error('start_address')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
          @error('start_latitude')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
          @error('start_longitude')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror

        </div>

        <!-- نقطة النهاية -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            نقطة النهاية <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
          </label>
          <div class="flex flex-col space-y-2">
            <input type="text" id="end_address" name="end_address" placeholder="مثال: المعلا - اسكريم المعلا"
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
              value="{{ old('end_address') }}">
            <div class="grid grid-cols-2 gap-2">
              <input type="hidden" id="end_latitude" name="end_latitude" value="{{ old('end_latitude') }}">
              <input type="hidden" id="end_longitude" name="end_longitude" value="{{ old('end_longitude') }}">
              <div class="coordinates-input text-xs text-gray-500">
                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                  id="endLatDisplay">{{ old('end_latitude', '--') }}</span>
              </div>
              <div class="coordinates-input text-xs text-gray-500">
                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90"
                  id="endLngDisplay">{{ old('end_longitude', '--') }}</span>
              </div>
            </div>
            @error('end_address')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
            @error('end_latitude')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
            @error('end_longitude')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>
        </div>

      </div>

      <livewire:special-orders.create />

      <!-- أزرار التحكم -->

      <!-- أزرار التحكم -->
      <div class="flex items-center justify-end w-full gap-3 mt-6">

        {{-- 2. تعديل زر "السعر" --}}
        <button type="button" @click="
                            loadingPrice = true;
                            showPriceModal = false;
                            // جلب الإحداثيات
                            const startLat = document.getElementById('start_latitude').value;
                            const startLng = document.getElementById('start_longitude').value;
                            const endLat = document.getElementById('end_latitude').value;
                            const endLng = document.getElementById('end_longitude').value;
                            const vehicle_id = document.getElementById('vehicle_id').value;
                            const coupon_code = document.getElementById('coupon_code').value;

                            // تحقق من وجود الإحداثيات
                            if (!startLat || !endLat) {
                                showErrorModal= true;
                                errorMessage='يجب عليك تحديد نقطة النهاية والبدايه';
                                loadingPrice = false;
                                return;
                            }
                            if(!vehicle_id){
                                showErrorModal= true;
                                errorMessage = 'يجب اختيار المركبه اولا';
                                loadingPrice = false;
                                return;
                            }

                            // إرسال الطلب
                            fetch('{{ route('trip.calculatePrice') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    start_latitude: startLat,
                                    start_longitude: startLng,
                                    end_latitude: endLat,
                                    end_longitude: endLng,
                                    vehicle_id: vehicle_id,
                                    coupon_code: coupon_code,

                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(data.price !== undefined) {
                                    original_price = data.original_price;
                                    calculatedPrice = data.price;
                                    distanceInKm = data.distanceInKm;
                                    vehicle = data.vehicle;
                                    coupon = data.coupon;
                                    discount_amount = data.discount_amount;
                                    showPriceModal = true;
                                }if(data.error){
                                    showErrorModal = true;
                                    errorMessage = data.error;
                                    loadingPrice = false;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('فشل الاتصال بالخادم.');
                            })
                            .finally(() => {
                                loadingPrice = false;
                            });
                        " :disabled="loadingPrice"
          class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto disabled:opacity-50">

          <span x-show="!loadingPrice">السعر</span>
          <span x-show="loadingPrice">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
          </span>
        </button>

        {{-- زر إنشاء الرحلة --}}
        <button type="submit"
          class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
          إنشاء رحلة
        </button>
      </div>
    </form>
    @include('pages.specialOrder.show-price-modal')
  </div>
  {{-- 3. إضافة المودال الخاص بعرض السعر --}}

@endsection

@section('script')
  <script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&language=ar&region=YE"
    async defer></script>
  <script>
    let map;
    let startMarker = null;
    let endMarker = null;
    let geocoder;


    function initializeMap() {
      if (!document.getElementById('map')) return;

      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 12.7773, lng: 45.0336 }, // عدن - كريتر
        zoom: 13
      });

      geocoder = new google.maps.Geocoder();
      map.addListener('click', function (event) {
        const latLng = event.latLng;

        if (confirm('حدد نوع النقطة:\n\حسنا = نقطة بداية\nإلغاء = نقطة نهاية')) {
          setStartPoint(latLng);
        } else {
          setEndPoint(latLng);
        }
      });

      document.getElementById('clearRouteBtn')?.addEventListener('click', clearMarkers);
    }


    function setStartPoint(latLng) {
      document.getElementById('start_latitude').value = latLng.lat();
      document.getElementById('start_longitude').value = latLng.lng();
      document.getElementById('startLatDisplay').textContent = latLng.lat().toFixed(6);
      document.getElementById('startLngDisplay').textContent = latLng.lng().toFixed(6);

      geocoder.geocode({ location: latLng }, (results) => {
      });

      if (startMarker) startMarker.setMap(null);

      startMarker = new google.maps.Marker({
        position: latLng,
        map: map,
        title: 'نقطة البداية',
        icon: {
          url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
          scaledSize: new google.maps.Size(40, 40)
        }
      });
    }

    function setEndPoint(latLng) {
      document.getElementById('end_latitude').value = latLng.lat();
      document.getElementById('end_longitude').value = latLng.lng();
      document.getElementById('endLatDisplay').textContent = latLng.lat().toFixed(6);
      document.getElementById('endLngDisplay').textContent = latLng.lng().toFixed(6);

      geocoder.geocode({ location: latLng }, (results) => {
      });

      if (endMarker) endMarker.setMap(null);

      endMarker = new google.maps.Marker({
        position: latLng,
        map: map,
        title: 'نقطة النهاية',
        icon: {
          url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
          scaledSize: new google.maps.Size(40, 40)
        }
      });
    }

    function clearMarkers() {
      if (startMarker) {
        startMarker.setMap(null);
        startMarker = null;
      }
      if (endMarker) {
        endMarker.setMap(null);
        endMarker = null;
      }


      ['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'start_address', 'end_address'].forEach(id => {
        document.getElementById(id).value = '';
      });

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
