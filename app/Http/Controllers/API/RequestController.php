<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\DiscountCodeRepository;
use App\Repositories\RequestRepository;
use App\Repositories\RequestStopRepository;
use App\Repositories\VehicleRepository;
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
        private VehicleRepository $vehicleRepository,
)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $perPage = $request->query('per_page', 10);
            $trips = $this->requestRepository->getByUserIdWithRelations($userId, [],$perPage);
            return ApiResponseClass::sendResponse($trips, 'تم استرجاع سجل الرحلات بنجاح.');

        } catch (Exception $e) {
            Log::error('Error fetching trip history: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب سجل الرحلات.', $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')],
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
            'wants_ac'        => ['nullable', 'boolean'],
            'trip_datetime'   => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {
            $stopsData = $validated['stops'] ?? [];
            unset($validated['stops']);
            $validated['user_id'] = auth('sanctum')->id();
            $validated['created_by'] = 'APP';
            $vehicle = $this->vehicleRepository->getById($validated['vehicle_id']);
            $coupon_object = null;
            if(isset($validated['discount_code']) && !empty($validated['discount_code'])){
                $couponCheck = $this->discountCodeService->validateCoupon($validated['discount_code'], $validated['user_id']);
                if (!$couponCheck['is_valid']) {
                    return ApiResponseClass::sendError($couponCheck['message'], null, $couponCheck['status_code']);
                }
                $coupon_object = $couponCheck['coupon'];
                $validated['discount_code_id'] = $coupon_object->id;
                unset($validated['discount_code']);
            }
            $priceDetails = $this->priceCalculationService->getFullPriceDetails($validated, $vehicle, $coupon_object);
            $validated['original_price']        = $priceDetails['original_price'];
            $validated['final_price']           = $priceDetails['final_price'];
            $validated['discount_amount']       = $priceDetails['discount_amount'];
            $validated['surcharge_amount']      = $priceDetails['total_surcharges']; 
            $validated['ac_cost']               = $priceDetails['ac_cost']; 
            $validated['app_commission_amount'] = $priceDetails['app_commission_amount'];
            $validated['status'] = 'pending';
            DB::beginTransaction();
            $requestModel = $this->requestRepository->store($validated);
            if (!empty($stopsData)) {
                $this->requestStopRepository->store($requestModel->id, $stopsData);
            }
            if (!empty($priceDetails['surcharges_details'])) {
                $surchargesToAttach = [];
                foreach ($priceDetails['surcharges_details'] as $surcharge) {
                    $surchargesToAttach[$surcharge['id']] = ['amount' => $surcharge['amount']];
                }
                $requestModel->surcharges()->attach($surchargesToAttach);
            }
            if ($coupon_object && $priceDetails['discount_amount'] > 0) {
                $this->discountCodeService->recordCouponUsage($coupon_object, $validated['user_id']);
            }
            DB::commit();
            $appSettings = Cache::rememberForever('app_settings', function () {
                return $this->appSettingRepository->getSetting();
            });
            if ($appSettings && $appSettings->auto_assign_to_drivers) {
                $nearestDrivers = $this->driverLocationService->getNearestDrivers($validated['start_latitude'], $validated['start_longitude'], 8, 20);
                
                if ($nearestDrivers) {
                    $title = 'طلب جديد';
                    $body = 'يوجد طلب جديد في منطقتك، اضغط لقبول الطلب';
                    $data = [
                        'request_id'      => $requestModel->id,
                        'start_latitude'  => $validated['start_latitude'],
                        'start_longitude' => $validated['start_longitude'],
                        'start_address'   => $validated['start_address'],
                    ];

                    foreach ($nearestDrivers as $driver) {
                        if (!empty($driver['device_token'])) {
                            $this->firebaseService->sendNotification($driver['device_token'], $title, $body, $data);
                        }
                    }
                }
            } else {
                // النظام اليدوي - إرسال الطلب للداشبورد
            }
            $requestModel->refresh();
            return ApiResponseClass::sendResponse($requestModel, 'Request created successfully.');
        }catch (Exception $e) {
            DB::rollBack();
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
            'start_latitude'  => ['required', 'numeric'],
            'start_longitude' => ['required', 'numeric'],
            'end_latitude'    => ['required', 'numeric'],
            'end_longitude'   => ['required', 'numeric'],
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')],
            'discount_code'   => ['nullable', 'string'],
            'wants_ac'        => ['nullable', 'boolean'],
            'trip_datetime'   => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        try {
            $validated['user_id'] = auth('sanctum')->id();
            $vehicle = $this->vehicleRepository->getById($validated['vehicle_id']);
            $coupon_object = null;
            if (!empty($validated['discount_code'])) {
                $couponCheck = $this->discountCodeService->validateCoupon($validated['discount_code'], $validated['user_id']);
                if (!$couponCheck['is_valid']) {
                    return ApiResponseClass::sendError($couponCheck['message'], null, $couponCheck['status_code']);
                }
                $coupon_object = $couponCheck['coupon'];
            }
            $responseData = $this->priceCalculationService->getFullPriceDetails($validated, $vehicle, $coupon_object);
            unset(
                $responseData['ac_cost'],
                $responseData['discount_amount'],
                $responseData['total_surcharges']
            );
            return ApiResponseClass::sendResponse($responseData, 'تم حساب السعر بنجاح.');
        } catch (Exception $e) {
            Log::error('Error calculating price: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء حساب السعر.', $e->getMessage(), 500);
        }
    }
}
