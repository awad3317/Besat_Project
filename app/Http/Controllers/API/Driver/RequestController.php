<?php

namespace App\Http\Controllers\API\Driver;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Request as TripRequest;
use App\Notifications\TripAcceptedNotification;
use App\Notifications\TripCancelledNotification;
use App\Repositories\ChatRepository;
use App\Repositories\DriverRepository;
use App\Repositories\RequestRepository;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    public function __construct(
        private DriverRepository $driverRepository,
        private RequestRepository $requestRepository,
        private FirebaseService $firebaseService,
        private ChatRepository $chatRepository) 
    {}
    public function updateTripStatus(Request $request)
    {
        $fields = $request->validate([
            'request_id' => ['required', 'integer', Rule::exists('requests', 'id')],
            'status' => ['required', 'string', Rule::in(['accepted', 'on_trip', 'completed', 'cancelled'])],
        ], [
            'request_id.required' => 'رقم الرحلة مطلوب.',
            'request_id.exists' => 'الرحلة المحددة غير موجودة.',
            'status.required' => 'الحالة الجديدة مطلوبة.',
            'status.in' => 'الحالة المحددة غير صالحة.',
        ]);
        $driver = auth('sanctum')->user();
        $targetStatus = $fields['status'];
        $requestId = $fields['request_id'];
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
                    $conversation = $this->chatRepository->getOrCreateOrderConversation(
                        $trip->id,
                        $trip->user_id,
                        $driver->id
                    );
                    if ($trip->user){
                        if ($trip->user->is_notifications_enabled) {
                            $trip->user->notify(new TripAcceptedNotification($trip, $conversation->id));
                        }
                    }
                    $deviceTokens = $trip->user->devices->pluck('device_token')->filter()->toArray();
                    foreach ($deviceTokens as $token){
                        try {
                            $this->firebaseService->sendNotification(
                                    $token,
                                    'تم قبول الرحلة!',
                                    'الكابتن ' . $driver->name . ' في طريقه إليك الآن.',
                                    [
                                        'request_id'=> (string) $trip->id,
                                        'conversation_id' => (string) $conversation->id,
                                        'status'=> 'accepted',
                                    ]
                                );
                        }catch(Exception $e){
                            Log::error("FCM Driver accept trip Error: " . $e->getMessage());
                        }
                    }
                    $responseData = $trip->fresh()->toArray();
                    $responseData['conversation_id'] = $conversation->id;
                    return ApiResponseClass::sendResponse($responseData, 'تم قبول الرحلة بنجاح.');
                }
                if ($trip->driver_id !== $driver->id) {
                    return ApiResponseClass::sendError('غير مصرح لك بتحديث حالة هذه الرحلة.', null, 403);
                }
                if ($targetStatus === 'cancelled') {
                    if ($trip->status === 'on_trip') {
                        return ApiResponseClass::sendError('لا يمكن إلغاء الرحلة أثناء سيرها (في الطريق).', null, 400);
                    }
                    $trip->update([
                        'status' => 'cancelled',
                        'cancelled_by' => null, 
                    ]);
                    
                    Conversation::where('request_id', $trip->id)
                        ->where('type', 'request')
                        ->update(['status' => 'closed']);

                    if ($trip->user){
                        if ($trip->user->is_notifications_enabled) {
                            $trip->user->notify(new TripCancelledNotification($trip));
                        }
                    }
                    $deviceTokens = $trip->user->devices->pluck('device_token')->filter()->toArray();
                    if (!empty($deviceTokens)){
                        foreach ($deviceTokens as $token){
                            try {
                                $this->firebaseService->sendNotification(
                                    $token,
                                    'تم إلغاء الرحلة',
                                    'قام الكابتن بإلغاء الرحلة الحالية.',
                                    [
                                        'request_id' => (string) $trip->id,
                                        'status'     => 'cancelled',
                                    ]
                                );
                            } catch (Exception $e) {
                                Log::error("FCM Cancel Error: " . $e->getMessage());
                            }
                            
                        }
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

                $updateData = ['status' => $targetStatus];
                
                if ($targetStatus === 'completed' && $trip->payment_method === 'cash') {
                    $updateData['payment_status'] = 'paid';
                }
                $trip->update($updateData);

                return ApiResponseClass::sendResponse($trip->fresh(), 'تم تحديث حالة الرحلة بنجاح.');
            });
        }catch(Exception $e){
            return ApiResponseClass::sendError('حدث خطأ أثناء تحديث حالة الرحلة.', $e->getMessage(), 500);
        }
    }
    
}