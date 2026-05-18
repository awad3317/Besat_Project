<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\DiscountCodeRepository;
use App\Repositories\RequestRepository;
use App\Repositories\RequestStopRepository;
use App\Repositories\VehicleRepository;
use App\Services\CommissionCalculationService;
use App\Services\DiscountCodeService;
use App\Services\DriverLocationService;
use App\Services\FirebaseService;
use App\Services\PriceCalculationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function __construct(
        private RequestRepository $requestRepository, 
        private PriceCalculationService $priceCalculationService,
        private DiscountCodeRepository $discountCodeRepository,
        private DriverLocationService $driverLocationService,
        private AppSettingRepository $appSettingRepository,
        private FirebaseService $firebaseService,
        private RequestStopRepository $requestStopRepository,
        private DiscountCodeService $discountCodeService,
        private VehicleRepository $vehicleRepository
)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')],
            'discount_code' => ['nullable', Rule::exists('discount_codes', 'code')],
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'start_address' => 'required|string|max:255',
            'end_latitude' => 'required|numeric',
            'end_longitude' => 'required|numeric',
            'end_address' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'payment_method'=>['required',Rule::in(['cash','deposit'])],
            'notes' => 'nullable|string|max:500',
            'stops'  => ['nullable', 'array'],
            'stops.*.latitude'  => ['required_with:stops', 'numeric', 'between:-90,90'],
            'stops.*.longitude' => ['required_with:stops', 'numeric', 'between:-180,180'],
            'stops.*.address'   => ['required_with:stops', 'string', 'max:255'],
        ]);

        try {
            $stopsData = $validated['stops'] ?? [];
            unset($validated['stops']);
            // إضافة معرف المستخدم المصادق عليه
            $validated['user_id'] = auth('sanctum')->id();
            $validated['created_by'] = 'APP';
            // حساب السعر الأصلي والنهائي مع تطبيق الخصم إذا وجد
            if(isset($validated['discount_code'])){
                if($coupon=$this->discountCodeRepository->getDiscountCodeByCode($validated['discount_code'])){
                    if(!$this->discountCodeRepository->isCouponActive($coupon)){
                        return ApiResponseClass::sendError('كود الخصم غير نشط.');
                    }
                    if(!$this->discountCodeRepository->hasUsageLimitAvailable($coupon)){
                        return ApiResponseClass::sendError('تم الوصول إلى الحد الأقصى لاستخدام كود الخصم.');
                    }
                    $result=$this->priceCalculationService->calculatePriceWithDiscount($validated['service_id'], $validated['distance_km'],$coupon);
                }
                else{
                    return ApiResponseClass::sendError('كود الخصم غير صالح.');
                }
            }
            else{
                $result=$this->priceCalculationService->calculateBasePrice($validated['service_id'], $validated['distance_km']);
            }
            $validated['original_price']=$result['original_price'];
            $validated['final_price']=$result['final_price'];
            if($result['discount_applied'] && isset($validated['discount_code'])){
                
                $coupon = $this->discountCodeRepository->getDiscountCodeByCode($validated['discount_code']);
                $validated['discount_code_id'] = $coupon->id;
                $validated['discount_amount'] = $result['discount_amount'];
                // زيادة عدد الاستخدامات لكود الخصم
                $this->discountCodeRepository->incrementUsage($coupon);
            }
            else{
                $validated['discount_amount']=0;
            }
            unset($validated['discount_code']);
            // حساب عمولة التطبيق
            $appCommissionAmount = $this->priceCalculationService->calculateCommission($validated['final_price']);
            $validated['app_commission_amount'] = $appCommissionAmount;
            DB::beginTransaction();
            // تخزين الطلب
            $requestModel = $this->requestRepository->store($validated);
            if (!empty($stopsData)) {
                $this->requestStopRepository->store($requestModel->id, $stopsData);
            }
            DB::commit();
            
            $appSettings = Cache::rememberForever('app_settings', function () {
                return $this->appSettingRepository->getSetting();
            });
            if ($appSettings && $appSettings->auto_assign_to_drivers) {
                // جلب أقرب السائقين الاقرب للزبون            
                $nearestDrivers =$this->driverLocationService->getNearestDrivers($validated['start_latitude'], $validated['start_longitude'], 8,20);
                if ($nearestDrivers === null) {
                    // لا يوجد سائقين متاحين في المنطقة
                }
                $title = 'طلب جديد';
                $body = 'يوجد طلب جديد في منطقتك، اضغط لقبول الطلب';
                $data = [
                    'request_id' => $requestModel->id,
                    'start_latitude' => $validated['start_latitude'],
                    'start_longitude' => $validated['start_longitude'],
                    'start_address' => $validated['start_address'],
                ];
                foreach ($nearestDrivers as $driver) {
                    if (isset($driver['device_token']) && !empty($driver['device_token'])) {
                        $this->firebaseService->sendNotification(
                            $driver['device_token'],
                            $title,
                            $body,
                            $data
                        );
                    }
                }
            }else {
                // النظام اليدوي - إرسال الطلب للداشبورد
            }
            return ApiResponseClass::sendResponse($requestModel, 'Request created successfully.');
        } catch (Exception $e) {
            return apiResponseClass::sendError('Failed to create request.'.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
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
        ]);
        try {
            $validated['user_id'] = auth('sanctum')->id();
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
        $vehicle_type = $vehicle->type;
        $coupon_rate = 0;
        $coupon_for_response = null;
        $discount_amount = 0;
        if(isset($validated['discount_code']) && !empty($validated['discount_code'])){
            $coupon_object  = $this->discountCodeService->getDiscountCode($validated['discount_code']);
            if (!$coupon_object) {
               return ApiResponseClass::sendError('كود الخصم الذي أدخلته غير صحيح.', null, 422);
            }
            if(!$this->discountCodeService->checkIsActive($coupon_object)){
                return ApiResponseClass::sendError('كود الخصم غير متاح', null, 400);
            }
            if(!$this->discountCodeService->checkGlobalUsage($coupon_object)){
                return ApiResponseClass::sendError('كود الخصم تجاوز الاستخدام المسموح به', null, 400);
            }
            if(!$this->discountCodeService->checkUserEligibility($coupon_object,$validated['user_id'])){
                return ApiResponseClass::sendError('كود الخصم تم استخدامه من قبل هذا المستخدم مسبقاً.', null, 400);
            }
            $coupon_rate = $coupon_object->discount_rate;
            $coupon_for_response = number_format($coupon_rate * 100, 2);
            $discount_amount = $price_orginal * ($coupon_rate);
            $price_final = $price_orginal - $discount_amount;

        }
        $responseData = [
                'distanceInKm' => $distanceInKm, 
                'price' => $price_final, 
                'vehicle' => $vehicle_type, 
                'coupon' => $coupon_for_response, 
                'discount_amount' => $discount_amount, 
                'original_price' => $price_orginal
            ];

        return ApiResponseClass::sendResponse($responseData, 'تم حساب السعر بنجاح.');
        }catch (Exception $e) {
            // 5. التقاط الأخطاء وتسجيلها
            Log::error('Error calculating price: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء حساب السعر.', $e->getMessage(), 500);
        }
        
    }
}
