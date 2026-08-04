<?php

namespace App\Services;

use App\Models\ServiceBooking;
use App\Models\ServiceBookingStop;
use App\Repositories\ServiceBookingRepository;
use Illuminate\Support\Facades\DB;

class ServiceBookingService
{
    public function __construct(
        private ServiceBookingRepository $bookingRepository
    ) {}

    /**
     * إنشاء حجز خدمة جديد مع نقاط التوقف
     */
    public function createBooking(array $data, int $userId): ServiceBooking
    {
        $stopsData = $data['stops'] ?? [];
        unset($data['stops']);
        $data['service_details'] = $this->extractServiceDetails($data);

        $data['user_id'] = $userId;
        $data['status'] = 'pending';
        $data['payment_status'] = 'unpaid';

        DB::beginTransaction();

        $booking = $this->bookingRepository->store($data);

        if (!empty($stopsData)) {
            $this->storeStops($booking->id, $stopsData);
        }

        DB::commit();

        return $booking->load('stops', 'vehicle');
    }

    /**
     * عرض حجز خدمة معين للمستخدم
     */
    public function getBookingForUser(int $bookingId, int $userId): ?ServiceBooking
    {
        return ServiceBooking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->with(['stops', 'vehicle:id,type', 'user:id,name,phone'])
            ->first();
    }

    /**
     * عرض قائمة حجوزات المستخدم
     */
    public function getUserBookings(int $userId, int $perPage = 10)
    {
        return ServiceBooking::where('user_id', $userId)
            ->with(['stops', 'vehicle:id,type'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * تحديث حجز خدمة (فقط إذا كان بحالة pending)
     */
    public function updateBooking(int $bookingId, int $userId, array $data): ?ServiceBooking
    {
        $booking = ServiceBooking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return null;
        }

        if ($booking->status !== 'pending') {
            throw new \Exception('لا يمكن تعديل الحجز إلا إذا كان بحالة انتظار.');
        }

        $stopsData = $data['stops'] ?? null;
        unset($data['stops']);
        $data['service_details'] = $this->extractServiceDetails($data);

        DB::beginTransaction();

        $booking->update($data);

        // تحديث نقاط التوقف إذا تم إرسالها
        if ($stopsData !== null) {
            $booking->stops()->delete();
            if (!empty($stopsData)) {
                $this->storeStops($booking->id, $stopsData);
            }
        }

        DB::commit();

        return $booking->load('stops', 'vehicle');
    }

    /**
     * إلغاء حجز خدمة
     */
    public function cancelBooking(int $bookingId, int $userId): ?ServiceBooking
    {
        $booking = ServiceBooking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return null;
        }

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            throw new \Exception('لا يمكن إلغاء هذا الحجز لأنه مكتمل أو ملغي بالفعل.');
        }

        $booking->update(['status' => 'cancelled']);

        return $booking;
    }

    /**
     * تخزين نقاط التوقف المتعددة لحجز خدمة
     */
    private function storeStops(int $bookingId, array $stopsData): void
    {
        $formattedStops = [];

        foreach ($stopsData as $index => $stop) {
            $formattedStops[] = [
                'service_booking_id' => $bookingId,
                'address_name'       => $stop['address_name'] ?? null,
                'latitude'           => $stop['latitude'] ?? null,
                'longitude'          => $stop['longitude'] ?? null,
                'stop_order'         => $index + 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        ServiceBookingStop::insert($formattedStops);
    }
    private function extractServiceDetails(array $data): ?array
    {
        $details = [];
        $serviceType = $data['service_type'] ?? '';

        if (in_array($serviceType, ['events', 'zafona', 'rehla', 'safety_airport']) && isset($data['bags_count'])) {
            $details['bags_count'] = (int) $data['bags_count'];
        }

        if ($serviceType === 'safety_airport' && isset($data['airport_direction'])) {
            $details['airport_direction'] = $data['airport_direction'];
        }

        if ($serviceType === 'dawam') {
            if (isset($data['work_or_school_name'])) $details['work_or_school_name'] = $data['work_or_school_name'];
            if (isset($data['work_days']))           $details['work_days']           = $data['work_days'];
            if (isset($data['departure_time']))      $details['departure_time']      = $data['departure_time'];
            if (isset($data['return_time']))         $details['return_time']         = $data['return_time'];
        }

        return count($details) > 0 ? $details : null;
    }
}
