<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use Illuminate\Validation\Rule;
use App\Classes\WebResponseClass;
use App\Services\DiscountCodeService;
use App\Repositories\DriverRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SpecialOrderRepository;

class SpecialOrderController extends Controller
{
    public function __construct(private SpecialOrderRepository $specialOrderRepository, 
    private DriverRepository $driverRepository, 
    private VehicleRepository $vehicleRepository, 
    private DiscountCodeService $discountCodeService )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders=$this->specialOrderRepository->index();
        $cancelledOrders = $this->specialOrderRepository->cancelledOrders()->count();
        $completedOrders = $this->specialOrderRepository->cancelledOrders()->count();
        $in_progressOrders = $this->specialOrderRepository->cancelledOrders()->count();
        return view('pages.specialOrder.index', compact(
            'orders',
            'cancelledOrders','completedOrders','in_progressOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.specialOrder.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'=>['required', 'string','max:255'],
            'customer_phone'=>['required', 'string', 'max:20'],
            'customer_whatsapp' => ['nullable', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_address'=>['required'],
            'end_address' => ['required'],
            'driver_id' => ['required', Rule::exists('drivers', 'id')],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $driver=$this->driverRepository->getById($request->driver_id);
            if(!$driver->is_online || !$driver->is_active){
                return WebResponseClass::sendError('السائق غير متاح او غير متصل');
            }
            $validatData = $validator->validated();
            $validatData['created_by'] = auth()->user()->id;
            $validatData['status'] = 'paused';
            $this->specialOrderRepository->store($validatData);
            ActivityLog::log('create','SpecialOrder','تم إنشاء رحلة جديده');
            // send notification to driver

            //
            return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة الرحلة بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

     /**
     * حساب سعر الرحلة بناءً على الإحداثيات.
     */
    public function calculatePrice(Request $request)
    {

        $validated = $request->validate([
            'start_latitude' => ['required','numeric'],
            'start_longitude' => ['required','numeric'],
            'end_latitude' => ['required','numeric'],
            'end_longitude' => ['required','numeric'],
            'vehicle_id'=>['required','integer'],
            'coupon_code'=>['nullable','string']
        ]);
         
        $distanceInKm = $this->calculateDistance(
            $validated['start_latitude'],
            $validated['start_longitude'],
            $validated['end_latitude'],
            $validated['end_longitude']
        );
        $vehicle=$this->vehicleRepository->getById($validated['vehicle_id'])->type;
        if(isset($validated['coupon_code'])){
            $coupon =number_format($this->discountCodeService->getDiscountCode($validated['coupon_code'])->discount_rate * 100, 2) ;
        }
        else{
            $coupon = null;
        }


        // =======================================================
        // ==> هنا تضع المنطق الخاص بك لحساب السعر
        // يمكنك استخدام الإحداثيات لحساب المسافة عبر Google Maps API
        // أو استخدام معادلة تسعير خاصة بك.
        // =======================================================

        $price = rand(1500, 5000); 

        return response()->json(['distanceInKm' => $distanceInKm, 'price' => $price, 'vehicle' => $vehicle, 'coupon' => $coupon ]);
    }
    /**
     * دالة مساعدة لحساب المسافة بين نقطتين بالكيلومتر
     * باستخدام معادلة هافيرسين (Haversine).
     *
     * @param float $lat1 خط عرض نقطة البداية
     * @param float $lon1 خط طول نقطة البداية
     * @param float $lat2 خط عرض نقطة النهاية
     * @param float $lon2 خط طول نقطة النهاية
     * @return float المسافة بالكيلومتر
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // نصف قطر الأرض بالكيلومتر
        $earthRadius = 6371;

        // تحويل الفروقات من درجات إلى راديان
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        // تطبيق معادلة هافيرسين
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // المسافة النهائية
        $distance = $earthRadius * $c;

        return $distance;
    }
}
