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
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SpecialOrderRepository;

class SpecialOrderController extends Controller
{
    public function __construct(private SpecialOrderRepository $specialOrderRepository, 
    private DriverRepository $driverRepository, 
    private VehicleRepository $vehicleRepository, 
    private DiscountCodeService $discountCodeService,
    private PriceCalculationService $priceCalculationService
   )
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
        ]);
         
        $distanceInKm = $this->priceCalculationService->calculateDistance(
            $validated['start_latitude'],
            $validated['start_longitude'],
            $validated['end_latitude'],
            $validated['end_longitude']
        );
        
        $price_orginal = rand(1500, 5000); 
        $price_final = $price_orginal;
        $vehicle=$this->vehicleRepository->getById($validated['vehicle_id'])->type;
        $coupon_rate = 0;
        $coupon_for_response = null; 
        $discount_amount = 0;
        if(isset($request->coupon_code) && !empty($request->coupon_code)){
            $coupon_object  = $this->discountCodeService->getDiscountCode($request->coupon_code);
            if (!$coupon_object) {
                return response()->json(['error' => 'كود الخصم الذي أدخلته غير صحيح.'], 422); // 422 هو كود مناسب لأخطاء التحقق
            }
            if(!$this->discountCodeService->checkIsActive($coupon_object)){
                return response()->json(['error' => 'كود الخصم غير متاح']);
            }
            // if($this->discountCodeService->checkGlobalUsage($coupon)){
            //     return response()->json(['error'=> 'كود الخصم غير متاح']);
            // }
            // عندما يتم ارسال المستخدم فعل هدا الكود
            // if($this->discountCodeService->checkUserEligibility($coupon)){
            //     return response()->json(['error' => 'كود الخصم غير متاح']);
            // }
            $coupon_rate = $coupon_object->discount_rate;
            $coupon_for_response = number_format($coupon_rate * 100, 2);
            $discount_amount = $price_orginal * ($coupon_rate);
            $price_final = $price_orginal - $discount_amount;
            
        }
        return response()->json(['distanceInKm' => $distanceInKm, 'price' => $price_final, 'vehicle' => $vehicle, 'coupon' => $coupon_for_response , 'discount_amount'=>$discount_amount, 'original_price' => $price_orginal]);
    }
   


}
