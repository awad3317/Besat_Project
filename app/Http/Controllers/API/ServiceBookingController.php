<?php
namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceBookingRequest;
use App\Services\ServiceBookingService;
use Illuminate\Support\Facades\Log;

class ServiceBookingController extends Controller
{
    public function __construct(
        private ServiceBookingService $bookingService
    ) {}

    /**
     * عرض قائمة حجوزات الخدمة للمستخدم الحالي
     */
    public function index(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $perPage = $request->query('per_page', 10);
            $bookings = $this->bookingService->getUserBookings($userId, $perPage);

            return ApiResponseClass::sendResponse($bookings, 'تم استرجاع الحجوزات بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching service bookings: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الحجوزات.', $e->getMessage(), 500);
        }
    }

    /**
     * إنشاء حجز خدمة جديد
     */
    public function store(StoreServiceBookingRequest $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $booking = $this->bookingService->createBooking($request->validated(), $userId);

            return ApiResponseClass::sendResponse($booking, 'تم إنشاء الحجز بنجاح.', 201);
        } catch (Exception $e) {
            Log::error('Error creating service booking: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء إنشاء الحجز.', $e->getMessage(), 500);
        }
    }

    /**
     * عرض تفاصيل حجز خدمة معين
     */
    public function show($id)
    {
        try {
            $userId = auth('sanctum')->id();
            $booking = $this->bookingService->getBookingForUser($id, $userId);

            if (!$booking) {
                return ApiResponseClass::sendError('الحجز غير موجود أو غير مصرح لك بالوصول إليه.', null, 404);
            }

            return ApiResponseClass::sendResponse($booking, 'تم استرجاع تفاصيل الحجز بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching service booking details: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب تفاصيل الحجز.', $e->getMessage(), 500);
        }
    }

    /**
     * تحديث حجز خدمة معين
     */
    public function update(StoreServiceBookingRequest $request, $id)
    {
        try {
            $userId = auth('sanctum')->id();
            $booking = $this->bookingService->updateBooking($id, $userId, $request->validated());

            if (!$booking) {
                return ApiResponseClass::sendError('الحجز غير موجود أو غير مصرح لك بتعديله.', null, 404);
            }

            return ApiResponseClass::sendResponse($booking, 'تم تحديث الحجز بنجاح.');
        } catch (Exception $e) {
            Log::error('Error updating service booking: ' . $e->getMessage());
            return ApiResponseClass::sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * إلغاء حجز خدمة معين
     */
    public function cancel($id)
    {
        try {
            $userId = auth('sanctum')->id();
            $booking = $this->bookingService->cancelBooking($id, $userId);

            if (!$booking) {
                return ApiResponseClass::sendError('الحجز غير موجود أو غير مصرح لك بإلغائه.', null, 404);
            }

            return ApiResponseClass::sendResponse($booking, 'تم إلغاء الحجز بنجاح.');
        } catch (Exception $e) {
            Log::error('Error cancelling service booking: ' . $e->getMessage());
            return ApiResponseClass::sendError($e->getMessage(), null, 422);
        }
    }
}