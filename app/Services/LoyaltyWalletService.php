<?php

namespace App\Services;

use App\Models\app_setting;
use App\Models\LoyaltyPointTransaction;
use App\Models\Request as TripRequest;
use App\Repositories\AppSettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\LoyaltyRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class LoyaltyWalletService
{
    protected $userRepo;
    protected $loyaltyRepo;
    protected $walletRepo;
    protected $appSettingRepo;

    public function __construct(
        UserRepository $userRepo,
        LoyaltyRepository $loyaltyRepo,
        WalletRepository $walletRepo,
        AppSettingRepository $appSettingRepo
    ) {
        $this->userRepo = $userRepo;
        $this->loyaltyRepo = $loyaltyRepo;
        $this->walletRepo = $walletRepo;
        $this->appSettingRepo = $appSettingRepo;
    }

    public function convertPointsToWallet(int $userId): array
    {
        $user = $this->userRepo->getById($userId);
        $minPoints = 100;

        if ($user->loyalty_points < $minPoints) {
            throw new Exception('عذراً، لا تمتلك الحد الأدنى من النقاط للتحويل.', 422);
        }

        // العمليات الحسابية
        $pointsToConvert = floor($user->loyalty_points / $minPoints) * $minPoints;
        $conversionRate = 0.01; 
        $moneyAmount = $pointsToConvert * $minPoints * $conversionRate;

        // تنفيذ الـ Transaction لضمان سلامة البيانات
        DB::transaction(function () use ($userId, $pointsToConvert, $moneyAmount) {
            // 1. تحديث أرصدة المستخدم عبر الـ UserRepository
            $this->userRepo->updateBalances($userId, -$pointsToConvert, $moneyAmount);

            // 2. تسجيل حركة النقاط عبر الـ LoyaltyRepository
            $this->loyaltyRepo->store([
                'user_id' => $userId,
                'points'  => -$pointsToConvert,
                'type'    => 'converted'
            ]);

            // 3. تسجيل حركة المحفظة عبر الـ WalletRepository
            $this->walletRepo->store([
                'user_id' => $userId,
                'amount'  => $moneyAmount,
                'type'    => 'deposit'
            ]);
        });

        // جلب البيانات المحدثة لإعادتها للـ Controller
        $updatedUser = $this->userRepo->getById($userId);

        return [
            'current_points' => $updatedUser->loyalty_points,
            'wallet_balance' => $updatedUser->wallet_balance
        ];
    }

    /**
     * منح نقاط الولاء للعميل تلقائياً عند اكتمال الرحلة
     *
     * @param TripRequest $trip
     * @return LoyaltyPointTransaction|null
     */
    public function awardPointsForTrip(TripRequest $trip): ?LoyaltyPointTransaction
    {
        if (!$trip->user_id) {
            return null;
        }

        $userSetting = $this->appSettingRepo->getSetting('user');
        $pointsPerKm = $userSetting ? (float) $userSetting->points_per_km : 0;
        $distance = (float) ($trip->distance_km ?? 0);

        // التأكد من أن points_per_km > 0 والمسافة المقطوعة الفعلية distance_km > 0
        if ($pointsPerKm <= 0 || $distance <= 0) {
            return null;
        }

        // التحقق من عدم منح نقاط للرحلة مسبقاً لتفادي التكرار (من خلال LoyaltyPointTransaction باستخدام trip_id)
        if ($this->loyaltyRepo->hasTripAwardedPoints($trip->id)) {
            return null;
        }

        // حساب النقاط: round(distance_km * points_per_km)
        $points = (int) round($distance * $pointsPerKm);
        if ($points <= 0) {
            return null;
        }

        // تسجيل عملية كسب النقاط في جدول loyalty_point_transactions (مع user_id, trip_id, points, type = 'earned')
        $transaction = $this->loyaltyRepo->store([
            'user_id' => $trip->user_id,
            'trip_id' => $trip->id,
            'points'  => $points,
            'type'    => 'earned',
        ]);

        // زيادة رصيد نقاط العميل loyalty_points في جدول users
        // $this->userRepo->incrementLoyaltyPoints($trip->user_id, $points);

        return $transaction;
    }
}