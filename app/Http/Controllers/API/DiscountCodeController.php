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
            'discount_code' => ['required', 'string', 'max:50'],
        ]);
        try {
            $userId = auth('sanctum')->id();
            $couponCheck = $this->discountCodeService->validateCoupon($validated['discount_code'], $userId);
            if (!$couponCheck['is_valid']) {
                return ApiResponseClass::sendError($couponCheck['message'], null, $couponCheck['status_code']);
            }
            $coupon = $couponCheck['coupon'];
            $responseData = [
                'id'            => $coupon->id,
                'code'          => $coupon->code,
                'discount_rate' => (float) ($coupon->discount_rate * 100),
                'message'       => 'كود الخصم فعال وصالح للاستخدام.'
            ];
            return ApiResponseClass::sendResponse($responseData, 'تم التحقق من كود الخصم بنجاح.');
        } catch (Exception $e) {
            Log::error('Error checking coupon: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء التحقق من كود الخصم.', $e->getMessage(), 500);
        }
    }
}
