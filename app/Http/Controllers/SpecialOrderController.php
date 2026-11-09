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
    public function __construct(
        private SpecialOrderRepository $specialOrderRepository,
        private DriverRepository $driverRepository,
        private VehicleRepository $vehicleRepository,
        private DiscountCodeService $discountCodeService,
        private PriceCalculationService $priceCalculationService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.specialOrder.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = $this->vehicleRepository->index();
        return view('pages.specialOrder.create', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'            => ['nullable', Rule::exists(User::class, 'id')],
            'customer_name'      => ['nullable', 'string', 'max:255'],
            'customer_phone'     => ['nullable', 'string', 'max:30'],
            'customer_whatsapp'  => ['nullable', 'string', 'max:30'],
            'driver_id'          => ['nullable', Rule::exists('drivers', 'id')],
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'start_address'      => ['required', 'string', 'max:255'],
            'end_address'        => ['required', 'string', 'max:255'],
            'price'              => ['nullable', 'numeric', 'min:0'],
            'start_latitude'     => ['nullable', 'numeric'],
            'start_longitude'    => ['nullable', 'numeric'],
            'end_latitude'       => ['nullable', 'numeric'],
            'end_longitude'      => ['nullable', 'numeric'],
            'vehicle_id'         => ['nullable', Rule::exists(Vehicle::class, 'id')],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            // حساب السعر التقديري تلقائياً إذا توفرت الإحداثيات ونوع المركبة ولم يُحدد السعر يدوياً
            if ((empty($data['price']) || $data['price'] <= 0) && !empty($data['vehicle_id']) && !empty($data['start_latitude']) && !empty($data['end_latitude'])) {
                $distanceInKm = $this->priceCalculationService->getdistanceInKm(
                    $data['start_latitude'],
                    $data['start_longitude'],
                    $data['end_latitude'],
                    $data['end_longitude']
                );
                $vehicle = $this->vehicleRepository->getById($data['vehicle_id']);
                $pricePerKm = $this->priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
                $data['price'] = $this->priceCalculationService->calculatePrice($distanceInKm, $pricePerKm, $vehicle->min_price);
            }

            $data['created_by'] = auth()->id();
            $data['status'] = !empty($data['driver_id']) ? 'in_progress' : 'pending';

            $order = $this->specialOrderRepository->store($data);

            ActivityLog::log('create', 'SpecialOrder', "تم إنشاء طلب خاص جديد #{$order->id} ({$order->title})");

            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة الطلب الخاص بنجاح', 'حسناً', 'specialOrder.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $order = $this->specialOrderRepository->getById($id);
            return view('pages.specialOrder.show', compact('order'));
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return redirect()->route('specialOrder.show', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title'              => ['required', 'string', 'max:255'],
            'customer_name'      => ['nullable', 'string', 'max:255'],
            'customer_phone'     => ['nullable', 'string', 'max:30'],
            'customer_whatsapp'  => ['nullable', 'string', 'max:30'],
            'driver_id'          => ['nullable', Rule::exists('drivers', 'id')],
            'start_address'      => ['required', 'string', 'max:255'],
            'end_address'        => ['required', 'string', 'max:255'],
            'price'              => ['nullable', 'numeric', 'min:0'],
            'description'        => ['nullable', 'string'],
            'status'             => ['required', 'in:pending,searching_driver,paused,in_progress,completed,cancelled'],
            'cancellation_reason'=> ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();
            if ($data['status'] === 'cancelled') {
                $data['cancelled_by'] = auth()->id();
            }

            $order = $this->specialOrderRepository->update($data, $id);

            ActivityLog::log('update', 'SpecialOrder', "تم تعديل بيانات الطلب الخاص #{$order->id}");

            return WebResponseClass::sendResponse('تم التحديث!', 'تم تحديث بيانات الطلب بنجاح', 'حسناً', 'specialOrder.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->specialOrderRepository->delete($id);

            ActivityLog::log('delete', 'SpecialOrder', "تم حذف الطلب الخاص #{$id}");

            return WebResponseClass::sendResponse('تم الحذف!', 'تم حذف الطلب بنجاح', 'حسناً', 'specialOrder.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Calculate price endpoint for maps and trips
     */
    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'start_latitude'  => ['required', 'numeric'],
            'start_longitude' => ['required', 'numeric'],
            'end_latitude'    => ['required', 'numeric'],
            'end_longitude'   => ['required', 'numeric'],
            'vehicle_id'      => ['required', 'integer'],
            'discount_code'   => ['nullable', 'string'],
            'user_id'         => ['required_with:discount_code', 'nullable', 'integer'],
            'stops'           => ['nullable', 'array'],
            'stops.*.latitude'  => ['required_with:stops', 'numeric'],
            'stops.*.longitude' => ['required_with:stops', 'numeric'],
        ]);

        $stops = $validated['stops'] ?? [];
        $distanceInKm = $this->priceCalculationService->getdistanceInKm(
            $validated['start_latitude'],
            $validated['start_longitude'],
            $validated['end_latitude'],
            $validated['end_longitude'],
            $stops
        );

        $vehicle = $this->vehicleRepository->getById($validated['vehicle_id']);
        $pricePerKm = $this->priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
        $priceOriginal = $this->priceCalculationService->calculatePrice($distanceInKm, $pricePerKm, $vehicle->min_price);

        $priceFinal = $priceOriginal;
        $vehicleName = $vehicle->type;
        $couponForResponse = null;
        $discountAmount = 0;

        if (!empty($validated['discount_code'])) {
            $couponObject = $this->discountCodeService->getDiscountCode($validated['discount_code']);
            if (!$couponObject) {
                return response()->json(['error' => 'كود الخصم الذي أدخلته غير صحيح.'], 422);
            }
            if (!$this->discountCodeService->checkIsActive($couponObject)) {
                return response()->json(['error' => 'كود الخصم غير متاح'], 422);
            }
            if (!$this->discountCodeService->checkGlobalUsage($couponObject)) {
                return response()->json(['error' => 'كود الخصم تجاوز الاستخدام المسموح به'], 422);
            }
            if (!empty($validated['user_id']) && !$this->discountCodeService->checkUserEligibility($couponObject, $validated['user_id'])) {
                return response()->json(['error' => 'كود الخصم تم استخدامه من قبل هذا المستخدم.'], 422);
            }

            $couponRate = $couponObject->discount_rate;
            $couponForResponse = number_format($couponRate * 100, 2);
            $discountAmount = $priceOriginal * $couponRate;
            $priceFinal = max(0, $priceOriginal - $discountAmount);
        }

        return response()->json([
            'distanceInKm'    => $distanceInKm,
            'price'           => $priceFinal,
            'vehicle'         => $vehicleName,
            'coupon'          => $couponForResponse,
            'discount_amount' => $discountAmount,
            'original_price'  => $priceOriginal,
        ]);
    }
}
