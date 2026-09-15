<?php

namespace App\Http\Controllers\API\Auth\Driver;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\DriverRepository;
use Exception;
use App\Models\Request as TripRequest;
use Illuminate\Support\Facades\Log;
use App\Services\DriverSettlementService;

class DriverController extends Controller
{
    public function __construct(private DriverRepository $driverRepository,private DriverSettlementService $settlementService)
    {
        //
    }
    public function updateDeviceToken(Request $request)
    {
        $fields=$request->validate([
            'device_token' => 'required',
        ]);
        try {
            $driver_id=auth('sanctum')->id();
            $driver=$this->driverRepository->update($fields,$driver_id);
            return ApiResponseClass::sendResponse($driver,'Device token updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated token.'.$e->getMessage());
        }
        
    }
    public function index()
    {
        try {
            $driver = auth('sanctum')->user();
            $driverProfile = $this->driverRepository->getById($driver->id);

            if (!$driverProfile) {
                return ApiResponseClass::sendError('السائق غير موجود', [], 404);
            }

            return ApiResponseClass::sendResponse($driverProfile, 'تم جلب بيانات الملف الشخصي بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching driver profile: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب بيانات الملف الشخصي.', $e->getMessage(), 500);
        }
    }
    public function updateProfile(Request $request)
    {
        $fields = $request->validate([
            'name'            => ['sometimes', 'required', 'string', 'max:255'],
            'whatsapp_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'city'            => ['sometimes', 'nullable', 'string', 'max:255'],
            'district'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'identity_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'plate_number'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'driver_image'    => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'vehicle_image'   => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'identity_image'  => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'latitude'        => ['sometimes', 'nullable', 'numeric'],
            'longitude'       => ['sometimes', 'nullable', 'numeric'],
            'is_online'       => ['sometimes', 'nullable', 'boolean'],
        ]);

        try {
            $driver = auth('sanctum')->user();
            $imageFields = ['driver_image', 'vehicle_image', 'identity_image'];
            foreach ($imageFields as $imageField) {
                if ($request->hasFile($imageField)) {
                    $fields[$imageField] = $request->file($imageField)->store('drivers/' . $imageField, 'public');
                }
            }
            $updatedDriver = $this->driverRepository->update($fields, $driver->id);
            return ApiResponseClass::sendResponse($updatedDriver, 'تم تحديث بيانات الملف الشخصي بنجاح.');
        } catch (Exception $e) {
            Log::error('Error updating driver profile: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء تحديث البيانات.', $e->getMessage(), 500);
        }
    }
    public function updateLocation(Request $request)
    {
        $fields = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $driver = auth('sanctum')->user();
            $driver = $this->driverRepository->update($fields, $driver->id);
            return ApiResponseClass::sendResponse($driver, 'Location updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated location.' . $e->getMessage());
        }
    }
    public function updateOnlineStatus(Request $request)
    {
        try {
            $fields = $request->validate([
                'is_online' => 'required|boolean',
            ]);
            $driver = auth('sanctum')->user();
            $driverId = $driver->id;
            if (! $fields['is_online']) {
                $hasActiveTrip = TripRequest::where('driver_id', $driverId)
                    ->whereIn('status', ['accepted', 'on_trip'])
                    ->exists();

                if ($hasActiveTrip) {
                    return ApiResponseClass::sendError('لا يمكنك تغيير حالتك إلى غير متصل أثناء وجود رحلة جارية.', [], 422);
                }
            }
            $this->driverRepository->update($fields, $driver->id);
            $status = $fields['is_online'] ? 'متصل' : 'غير متصل';
            return ApiResponseClass::sendResponse([], "تم التحديث إلى: {$status}");
        } catch (Exception $e) {
            return ApiResponseClass::sendError('فشل في تحديث الحالة: ' . $e->getMessage());
        }
    }
    public function allRequest()
    {
        try {
            $driver = auth('sanctum')->user();
            $orders = $driver->requests()->get(); 
            return ApiResponseClass::sendResponse($orders, 'تم جلب طلبات السائق بنجاح.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الطلبات.', $e->getMessage(), 500);
        }
    }
    public function getFinancialSummary(){
        try {
            $driver = auth('sanctum')->user();
            $summary = $this->settlementService->calculateDriverBalance($driver->id);
            return ApiResponseClass::sendResponse($summary, 'تم جلب الحسابات المالية بنجاح.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب البيانات المالية: ' . $e->getMessage(), [], 500);
        }
    }
    public function getDriverStats(){
        try {
            $driver = auth('sanctum')->user();
            if (!$driver) {
                return ApiResponseClass::sendError('المستخدم غير مصرح له أو الجلسة منتهية.', null, 401);
            }
            $stats = $this->settlementService->getDriverStats($driver->id);
            return ApiResponseClass::sendResponse($stats, 'تم جلب إحصائيات السائق بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching driver statistics: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الإحصائيات: ' . $e->getMessage(), [], 500);
        }
    }
    
}
