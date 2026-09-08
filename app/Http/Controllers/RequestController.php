<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
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
use App\Models\WalletTransaction;
use App\Repositories\RequestStopRepository;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function __construct(private RequestRepository $requestRepository,
    private VehicleRepository $vehicleRepository,
    private PriceCalculationService $priceCalculationService,
    private DiscountCodeService $discountCodeService,
    private RequestStopRepository $requestStopRepository
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
            'payment_method' => ['required',Rule::in(['cash', 'deposit', 'wallet'])],
            'bank_name' => ['required_if:payment_method,deposit'],
            'account_number' => ['required_if:payment_method,deposit'],
            'wants_ac' => ['nullable', 'boolean'],
            'trip_date' => ['nullable', 'date'],
            'trip_time' => ['nullable', 'date_format:H:i'],
            'final_price_client' => ['nullable', 'numeric'],
            'stops' => ['nullable', 'array'],
            'stops.*.latitude' => ['required_with:stops', 'numeric'],
            'stops.*.longitude' => ['required_with:stops', 'numeric'],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            

            $validatData = $validator->validated();

            // Check for existing active request
            $existingRequest = RequestModel::where('user_id', $validatData['user_id'])
                ->whereIn('status', ['searching_driver', 'in_progress', 'pending', 'paused', 'accepted', 'on_trip'])
                ->exists();

            if ($existingRequest) {
                return WebResponseClass::sendError('عذراً، لديك رحلة قيد التنفيذ أو جاري البحث عن سائق. يرجى إكمالها أولاً.');
            }

            // Combine Date and Time for Trip Datetime
            $tripDatetime = null;
            if (!empty($validatData['trip_date']) && !empty($validatData['trip_time'])) {
                $tripDatetime = \Carbon\Carbon::parse($validatData['trip_date'] . ' ' . $validatData['trip_time'])->format('Y-m-d H:i:s');
            } else {
                $tripDatetime = now()->format('Y-m-d H:i:s');
            }
            $validatData['trip_datetime'] = $tripDatetime;

            // Ensure wants_ac is boolean
            $validatData['wants_ac'] = filter_var($validatData['wants_ac'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $vehicle=$this->vehicleRepository->getById($validatData['vehicle_id']);
            
            // Calculate full price details passing the whole array so it reads stops correctly
            $priceDetails = $this->priceCalculationService->getFullPriceDetails($validatData, $vehicle);

            $distanceInKm = $priceDetails['distance_in_km'];
            $orginal_price = $priceDetails['original_price'];
            $totalSurcharge = $priceDetails['total_surcharges'];
            $final_price = $priceDetails['final_price'];
            

            $discount_code_id = null;
            $discount_amount = 0;

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
                
                // Recalculate with coupon applied
                $priceDetailsWithCoupon = $this->priceCalculationService->getFullPriceDetails($validatData, $vehicle, $coupon_object);
                $final_price = $priceDetailsWithCoupon['final_price'];
                $discount_amount = $priceDetailsWithCoupon['discount_amount'];
                $discount_code_id = $coupon_object->id;
                
                $this->discountCodeService->recordCouponUsage($coupon_object,$validatData['user_id']);
            }

            $validatData['app_commission_amount'] = $priceDetails['app_commission_amount'];
            $validatData['final_price'] = $final_price;
            $validatData['discount_code_id']= $discount_code_id;
            $validatData['discount_amount']= $discount_amount;
            $validatData['original_price'] = $orginal_price;
            $validatData['distance_km'] = $distanceInKm;
            $validatData['surcharge_amount'] = $totalSurcharge;
            $validatData['ac_cost'] = $priceDetails['ac_cost'] ?? 0;
            $validatData['created_by_user'] = auth()->user()->id;
            $validatData['created_by']='Web';
            $validatData['status'] = 'searching_driver';
            DB::beginTransaction();
            // Check wallet balance if payment is wallet
        
              if ($validatData['payment_method'] === 'wallet') {
                $user = User::lockForUpdate()->find($validatData['user_id']);
                if ($user->wallet_balance < $validatData['final_price']) {
                    DB::rollBack();
                    return WebResponseClass::sendError('رصيد المحفظة غير كافٍ لإتمام الرحلة.');
                }
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $validatData['final_price'],
                    'type' => 'payment',
                ]);
            }
            
            $createdRequest = $this->requestRepository->store($validatData);
            
            // Save Multiple Stops using the repository
            if (!empty($validatData['stops'])) {
                $this->requestStopRepository->store($createdRequest->id, $validatData['stops']);
            }
            
            // Attach surcharges to the created request
            $this->priceCalculationService->attachSurcharges($createdRequest, $priceDetails['surcharges_details'] ?? []);
            
            DB::commit();
            
            ActivityLog::log('create','Request','تم إنشاء رحلة جديده');
            
            return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة الرحلة بنجاح','حسناً','request.index');
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $request = $this->requestRepository->getById($id);
        $request->load(['user', 'driver', 'vehicle', 'surcharges', 'discountCode', 'stops']);
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
