<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Services\LoyaltyWalletService;
use Exception;
use Illuminate\Http\Request;

class LoyaltyWalletController extends Controller
{
    public function __construct(private LoyaltyWalletService $loyaltyWalletService)
    {
    }
    public function getBalance()
    {
        try {
            $user = auth('sanctum')->user();
            $data = [
                'loyalty_points' => $user->loyalty_points,
                'wallet_balance' => $user->wallet_balance,
            ];
            return ApiResponseClass::sendResponse($data, 'تم استرجاع بيانات المحفظة والنقاط بنجاح.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب البيانات.', $e->getMessage(), 500);
        }
    }
    public function convertPoints()
    {
        try {
            $userId = auth('sanctum')->id();
            $result = $this->loyaltyWalletService->convertPointsToWallet($userId);
            return ApiResponseClass::sendResponse($result, 'تم تحويل النقاط إلى رصيد في المحفظة بنجاح.');
        } catch (Exception $e) {
            if ($e->getCode() === 422) {
                return ApiResponseClass::sendResponse(null, $e->getMessage(), 422);
            }
            $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            return ApiResponseClass::sendError('حدث خطأ أثناء تحويل النقاط.', $e->getMessage(), $statusCode);
        }
    }
}
