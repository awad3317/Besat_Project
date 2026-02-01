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
use App\Repositories\RequestRepository;
use App\Repositories\VehicleRepository;
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function __construct(private RequestRepository $requestRepository,
    private VehicleRepository $vehicleRepository,
    private PriceCalculationService $priceCalculationService,
    private DiscountCodeService $discountCodeService,
   )
    {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('pages.request.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.request.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>['required', Rule::exists(User::class, 'id')->where('type', 'user')],
            'vehicle_id'=>['required',Rule::exists(Vehicle::class, 'id')],
            'title' => ['nullable', 'string', 'max:255'],
            'discount_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'start_address'=>['required'],
            'start_latitude' => ['required','numeric'],
            'start_longitude' => ['required','numeric'],
            'end_latitude' => ['required','numeric'],
            'end_longitude' => ['required','numeric'],
            'end_address' => ['required'],
            'payment_method' => ['required',Rule::in(['cash', 'deposit'])],
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
            $orginal_price = $this->priceCalculationService->calculatePrice($distanceInKm,$price_per_km,$vehicle->min_price);
            
            // Calculate and add surcharges
            $applicableSurcharges = $this->priceCalculationService->getApplicableSurcharges();
            $totalSurcharge = $applicableSurcharges->sum('amount');

            $discount_amount = 0;
            $price_final = $orginal_price;
            $discount_code_id = null;
            if(isset($validatData['discount_code']) && !empty($validatData['discount_code'])){
                $coupon_object  = $this->discountCodeService->getDiscountCode($validatData['discount_code']);
                if (!$coupon_object) {
                    return WebResponseClass::sendError('كود الخصم الذي أدخلته غير صحيح.');
                }
                if(!$this->discountCodeService->checkIsActive($coupon_object)){
                    return WebResponseClass::sendError('كود الخصم غير متاح');
                }
                if(!$this->discountCodeService->checkGlobalUsage($coupon_object)){
                    return WebResponseClass::sendError( 'كود الخصم تجاوز الاستخدام المسموح به');
                }
                if(!$this->discountCodeService->checkUserEligibility($coupon_object,$validatData['user_id'])){
                    return WebResponseClass::sendError('كود الخصم تم استخدامه من قبل هذا المستخدم.');
                }
                $discount_code_id=$coupon_object->id;
                $discount_amount = $orginal_price * ($coupon_object->discount_rate);
                $price_final = $orginal_price - $discount_amount;
                $this->discountCodeService->recordCouponUsage($coupon_object,$validatData['user_id']);
            }
            $validatData['app_commission_amount'] = $this->priceCalculationService->calculateCommission($orginal_price);
            $validatData['final_price'] = $price_final + $validatData['app_commission_amount'] + $totalSurcharge;
            $validatData['discount_code_id']= $discount_code_id;
            $validatData['discount_amount']= $discount_amount;
            $validatData['original_price'] = $orginal_price;
            $validatData['distance_km'] = $distanceInKm;
            $validatData['surcharge_amount'] = $totalSurcharge;
            $validatData['created_by_user'] = auth()->user()->id;
            $validatData['created_by']='Web';
            $validatData['status'] = 'searching_driver';
            $createdRequest = $this->requestRepository->store($validatData);
            
            // Attach surcharges to the created request
            $this->priceCalculationService->attachSurcharges($createdRequest, $applicableSurcharges);
            
            ActivityLog::log('create','Request','تم إنشاء رحلة جديده');
            
            return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة الرحلة بنجاح','حسناً','request.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $request = $this->requestRepository->getById($id);
        $request->load(['user', 'driver', 'vehicle', 'surcharges', 'discountCode']);
        return view('pages.request.show', compact('request'));
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
}
