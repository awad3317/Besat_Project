<?php

namespace App\Services;

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

    public function __construct(
        UserRepository $userRepo,
        LoyaltyRepository $loyaltyRepo,
        WalletRepository $walletRepo
    ) {
        $this->userRepo = $userRepo;
        $this->loyaltyRepo = $loyaltyRepo;
        $this->walletRepo = $walletRepo;
    }

    public function convertPointsToWallet(int $userId): array
    {
        $user = $this->userRepo->getById($userId);
        $minPoints = 100;

        if ($user->loyalty_points < $minPoints) {
            throw new Exception('عذراً، لا تمتلك الحد الأدنى من النقاط للتحويل.');
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
}