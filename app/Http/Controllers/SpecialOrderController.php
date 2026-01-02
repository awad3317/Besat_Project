<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Vehicle;
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
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>['required', Rule::exists(User::class, 'id')],
            'vehicle_id'=>['required',Rule::exists(Vehicle::class, 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_address'=>['required'],
            'start_latitude' => ['required','numeric'],
            'start_longitude' => ['required','numeric'],
            'end_latitude' => ['required','numeric'],
            'end_longitude' => ['required','numeric'],
            'end_address' => ['required'],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $validatData = $validator->validated();
            $distanceInKm = $this->priceCalculationService->getdistanceInKm(
                $validatData['start_latitude'],
                $validatData['start_longitude'],
                $validatData['end_latitude'],
                $validatData['end_longitude']
            );
            $vehicle=$this->vehicleRepository->getById($validatData['vehicle_id']);
            $price_per_km = $this->priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
            $price_orginal = $this->priceCalculationService->calculatePrice($distanceInKm,$price_per_km,$vehicle->min_price);
            $validatData['price'] = $price_orginal;
            $validatData['created_by'] = auth()->user()->id;
            $validatData['status'] = 'paused';
            dd($validatData);
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

    public function calculatePrice(Request $request)
    {

        $validated = $request->validate([
            'start_latitude' => ['required','numeric'],
            'start_longitude' => ['required','numeric'],
            'end_latitude' => ['required','numeric'],
            'end_longitude' => ['required','numeric'],
            'vehicle_id'=>['required','integer'],
            'discount_code'=> ['nullable','string'],
            'user_id'=>['required_with:discount_code','nullable','integer'],
        ]);
        $distanceInKm = $this->priceCalculationService->getdistanceInKm(
            $validated['start_latitude'],
            $validated['start_longitude'],
            $validated['end_latitude'],
            $validated['end_longitude']
        );
        $vehicle=$this->vehicleRepository->getById($validated['vehicle_id']);
        $price_per_km = $this->priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
        $price_orginal = $this->priceCalculationService->calculatePrice($distanceInKm,$price_per_km,$vehicle->min_price);

        $price_final = $price_orginal;
        $vehicle = $vehicle->type;
        $coupon_rate = 0;
        $coupon_for_response = null;
        $discount_amount = 0;
        if(isset($validated['discount_code']) && !empty($validated['discount_code'])){
            $coupon_object  = $this->discountCodeService->getDiscountCode($validated['discount_code']);
            if (!$coupon_object) {
                return response()->json(['error' => 'كود الخصم الذي أدخلته غير صحيح.'], 422); // 422 هو كود مناسب لأخطاء التحقق
            }
            if(!$this->discountCodeService->checkIsActive($coupon_object)){
                return response()->json(['error' => 'كود الخصم غير متاح']);
            }
            if(!$this->discountCodeService->checkGlobalUsage($coupon_object)){
                return response()->json(['error'=> 'كود الخصم تجاوز الاستخدام المسموح به']);
            }
            if(!$this->discountCodeService->checkUserEligibility($coupon_object,$validated['user_id'])){
                return response()->json(['error' => 'كود الخصم تم استخدامه من قبل هذا المستخدم.']);
            }
            $coupon_rate = $coupon_object->discount_rate;
            $coupon_for_response = number_format($coupon_rate * 100, 2);
            $discount_amount = $price_orginal * ($coupon_rate);
            $price_final = $price_orginal - $discount_amount;

        }

        return response()->json(['distanceInKm' => $distanceInKm, 'price' => $price_final, 'vehicle' => $vehicle, 'coupon' => $coupon_for_response , 'discount_amount'=>$discount_amount, 'original_price' => $price_orginal]);
    }



}
