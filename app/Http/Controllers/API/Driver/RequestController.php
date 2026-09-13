<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Repositories\RequestRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Classes\ApiResponseClass;
use App\Models\Request as TripRequest;
use App\Notifications\TripCancelledNotification;
use App\Repositories\DriverRepository;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct(
        private DriverRepository $driverRepository,
        private RequestRepository $requestRepository) 
    {}
    public function updateTripStatus(Request $request)
    {
        $request->validate([
            'request_id' => ['required', 'integer', Rule::exists('requests', 'id')],
            'status'=> ['required', 'string', Rule::in(['accepted', 'on_trip', 'completed', 'cancelled'])],
        ]);
        $driver = auth('sanctum')->user();
        $targetStatus = $request->validated('status');
        $requestId = $request->validated('request_id');
        try {
             return DB::transaction(function () use ($driver, $requestId, $targetStatus) {
                $trip = TripRequest::where('id', $requestId)->lockForUpdate()->first();
                if (!$trip) {
                    return ApiResponseClass::sendError('الرحلة غير موجودة.', null, 404);
                }
                if (in_array($trip->status, ['completed', 'cancelled'])) {
                    return ApiResponseClass::sendError("لا يمكن تعديل حالة رحلة منتهية ({$trip->status}).", null, 400);
                }
                if ($targetStatus === 'accepted'){
                    if (!in_array($trip->status, ['searching_driver', 'pending'])) {
                        return ApiResponseClass::sendError('لا يمكن قبول هذه الرحلة لأنها ليست قيد البحث عن سائق.', null, 400);
                    }
                    if (!is_null($trip->driver_id) && $trip->driver_id !== $driver->id) {
                        return ApiResponseClass::sendError('تم قبول هذه الرحلة بالفعل من قِبل سائق آخر.', null, 400);
                    }
                    $trip->update([
                        'driver_id' => $driver->id,
                        'status' => 'accepted',
                    ]);
                    return ApiResponseClass::sendResponse($trip->fresh(), 'تم قبول الرحلة بنجاح.');
                }
                if ($trip->driver_id !== $driver->id) {
                    return ApiResponseClass::sendError('غير مصرح لك بتحديث حالة هذه الرحلة.', null, 403);
                }

                if ($targetStatus === 'cancelled') {
                    $trip->update([
                        'status' => 'cancelled',
                        'cancelled_by' => $driver->id,
                    ]);

                    if ($trip->user && $trip->user->is_notifications_enabled) {
                        $trip->user->notify(new TripCancelledNotification($trip));
                    }

                    return ApiResponseClass::sendResponse($trip->fresh(), 'تم إلغاء الرحلة بنجاح.');
                }
                $allowedNextStatus = [
                    'accepted' => 'on_trip',
                    'on_trip'  => 'completed',
                ];

                if (!isset($allowedNextStatus[$trip->status]) || $allowedNextStatus[$trip->status] !== $targetStatus) {
                    return ApiResponseClass::sendError(
                        "انتقال غير صالح: لا يمكن تغيير الحالة من [{$trip->status}] إلى [{$targetStatus}].",
                        null,
                        400
                    );
                }

                $trip->update([
                    'status' => $targetStatus,
                ]);

                return ApiResponseClass::sendResponse($trip->fresh(), 'تم تحديث حالة الرحلة بنجاح.');
            });
        }catch(Exception $e){
            return ApiResponseClass::sendError('حدث خطأ أثناء تحديث حالة الرحلة.', $e->getMessage(), 500);
        }
    }
    
}