@extends('layouts.app')
@section('title', 'إنشاء رحلة جديدة')
@section('Breadcrumb', 'إنشاء رحلة جديدة')

@section('addButton')
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
  <div class="w-full rounded-3xl bg-white p-6 dark:bg-gray-900">
    <form method="POST" action="{{ route('specialOrder.store') }}" enctype="multipart/form-data" id="tripForm">
      @csrf

      <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
        إنشاء رحلة جديدة
      </h4>

      <!-- قسم الخريطة -->
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
        <!-- عنوان الرحلة -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            عنوان الرحلة <span class="text-red-500">*</span>
          </label>
          <input type="text" placeholder="مثال: رحلة خاصة" name="title" required
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
            value="{{ old('title') }}">
          @error('title')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- اسم العميل -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            اسم العميل <span class="text-red-500">*</span>
          </label>
          <input type="text" placeholder="مثال: أحمد شرجبي" name="customer_name" required
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
            value="{{ old('customer_name') }}">
          @error('customer_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- رقم جوال العميل -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            رقم جوال العميل <span class="text-red-500">*</span>
          </label>
          <input type="tel" placeholder="مثال: 967780236552" name="customer_phone" required
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
            value="{{ old('customer_phone') }}">
          @error('customer_phone')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- رقم الواتساب -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            رقم الواتساب
          </label>
          <input type="tel" placeholder="مثال: 967780236552" name="customer_whatsapp"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
            value="{{ old('customer_whatsapp') }}">
          @error('customer_whatsapp')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- نقطة البداية -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            نقطة البداية <span class="text-red-500">*</span>
          </label>
          <div class="flex flex-col space-y-2">
            <input type="text" name="start_address" placeholder="مثال: المنصورة - سوق القات" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
              value="{{ old('start_address') }}">
            <div class="grid grid-cols-2 gap-2">
              <input type="hidden" id="start_latitude" name="start_latitude" value="{{ old('start_latitude') }}">
              <input type="hidden" id="start_longitude" name="start_longitude" value="{{ old('start_longitude') }}">
              <div class="coordinates-input text-xs text-gray-500">
                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90" id="startLatDisplay">{{ old('start_latitude', '--') }}</span>
              </div>
              <div class="coordinates-input text-xs text-gray-500">
                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90" id="startLngDisplay">{{ old('start_longitude', '--') }}</span>
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
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            نقطة النهاية <span class="text-red-500">*</span>
          </label>
          <div class="flex flex-col space-y-2">
            <input type="text" id="end_address" name="end_address" placeholder="مثال: المعلا - اسكريم المعلا" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
              value="{{ old('end_address') }}">
            <div class="grid grid-cols-2 gap-2">
              <input type="hidden" id="end_latitude" name="end_latitude" value="{{ old('end_latitude') }}">
              <input type="hidden" id="end_longitude" name="end_longitude" value="{{ old('end_longitude') }}">
              <div class="coordinates-input text-xs text-gray-500">
                خط العرض: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90" id="endLatDisplay">{{ old('end_latitude', '--') }}</span>
              </div>
              <div class="coordinates-input text-xs text-gray-500">
                خط الطول: <span class="mt-1 text-xs text-warning-500 dark:text-warning/90" id="endLngDisplay">{{ old('end_longitude', '--') }}</span>
              </div>
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

        <!-- سعر الرحلة -->
        <div class="col-span-1">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            سعر الرحلة (ريال)
          </label>
          <input type="number" id="price" name="price" placeholder="سيتم حسابه تلقائياً" min="0" step="0.01"
            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
            value="{{ old('price') }}">
          @error('price')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <!-- اختيار السائق -->
        <div class="col-span-1">
          @livewire('online-drivers')
        </div>

        <!-- الوصف -->
        <div class="col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            الوصف
          </label>
          <textarea placeholder="أدخل وصفاً للرحلة" name="description"
            class="hover:border-brand-500 dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
          @error('description')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

      </div>

      <!-- أزرار التحكم -->
      <div class="flex items-center justify-end w-full gap-3 mt-6">
        <a href="{{ url()->previous() }}" type="button"
          class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
          إلغاء
        </a>
        <button type="submit"
          class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
          إنشاء رحلة
        </button>
      </div>
    </form>
  </div>
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