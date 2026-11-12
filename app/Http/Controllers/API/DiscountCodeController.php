<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Services\DiscountCodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DiscountCodeController extends Controller
{
    public function __construct(private DiscountCodeService $discountCodeService)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscountCode $discountCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountCode $discountCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountCode $discountCode)
    {
        //
    }

    public function checkCoupon(Request $request)
    {
        $validated = $request->validate([
            'discount_code'  => ['required', 'string', 'max:50'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $userId = auth('sanctum')->id();
            $originalPrice = isset($validated['original_price']) ? (float) $validated['original_price'] : null;
            $couponCheck = $this->discountCodeService->validateCoupon($validated['discount_code'],$userId,$originalPrice);

            if (!$couponCheck['is_valid']) {
                return ApiResponseClass::sendError($couponCheck['message'], null, $couponCheck['status_code']);
            }
            $coupon = $couponCheck['coupon'];
            $responseData = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_rate' => (float) ($coupon->discount_rate * 100),
                'max_discount_amount' => $coupon->max_discount_amount ? (float) $coupon->max_discount_amount : null,
                'min_trip_fare' => $coupon->min_trip_fare ? (float) $coupon->min_trip_fare : null,
                'message' => 'كود الخصم فعال وصالح للاستخدام.'
            ];

            return ApiResponseClass::sendResponse($responseData, 'تم التحقق من كود الخصم بنجاح.');
        } catch (Exception $e) {
            Log::error('Error checking coupon: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء التحقق من كود الخصم.', $e->getMessage(), 500);
        }
    }
}
