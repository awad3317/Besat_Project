<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\app_setting;
use App\Models\LoyaltyPointTransaction;
use App\Models\Request as TripRequest;
use App\Repositories\AppSettingRepository;
use App\Repositories\LoyaltyRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\LoyaltyWalletService;

class LoyaltyPointsTripTest extends TestCase
{
    private $userRepo;
    private $loyaltyRepo;
    private $walletRepo;
    private $appSettingRepo;
    private LoyaltyWalletService $loyaltyWalletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = $this->createMock(UserRepository::class);
        $this->loyaltyRepo = $this->createMock(LoyaltyRepository::class);
        $this->walletRepo = $this->createMock(WalletRepository::class);
        $this->appSettingRepo = $this->createMock(AppSettingRepository::class);

        $this->loyaltyWalletService = new LoyaltyWalletService(
            $this->userRepo,
            $this->loyaltyRepo,
            $this->walletRepo,
            $this->appSettingRepo
        );
    }

    public function test_points_are_awarded_correctly_on_trip_completion()
    {
        // إعداد points_per_km = 2.0 لتطبيق المستخدم
        $userSetting = new app_setting(['points_per_km' => 2.0]);
        $this->appSettingRepo->expects($this->once())
            ->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        // لم يتم منح نقاط لهذه الرحلة مسبقاً
        $this->loyaltyRepo->expects($this->once())
            ->method('hasTripAwardedPoints')
            ->with(10)
            ->willReturn(false);

        // إنشاء كائن معاملة النقاط المتوقع
        $expectedTransaction = new LoyaltyPointTransaction([
            'id'      => 1,
            'user_id' => 5,
            'trip_id' => 10,
            'points'  => 11,
            'type'    => 'earned',
        ]);

        // يجب استدعاء store مع البيانات الصحيحة (5.4 كم * 2.0 = 10.8 -> round = 11 نقطة)
        $this->loyaltyRepo->expects($this->once())
            ->method('store')
            ->with([
                'user_id' => 5,
                'trip_id' => 10,
                'points'  => 11,
                'type'    => 'earned',
            ])
            ->willReturn($expectedTransaction);

        // يجب زيادة رصيد نقاط المستخدم بـ 11 نقطة
        $this->userRepo->expects($this->once())
            ->method('incrementLoyaltyPoints')
            ->with(5, 11);

        $trip = new TripRequest([
            'user_id'     => 5,
            'distance_km' => 5.4,
        ]);
        $trip->id = 10;

        $result = $this->loyaltyWalletService->awardPointsForTrip($trip);

        $this->assertNotNull($result);
        $this->assertEquals(11, $result->points);
        $this->assertEquals('earned', $result->type);
    }

    public function test_points_are_not_awarded_twice_for_the_same_trip()
    {
        $userSetting = new app_setting(['points_per_km' => 2.0]);
        $this->appSettingRepo->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        // الرحلة مُنحت نقاطاً مسبقاً
        $this->loyaltyRepo->expects($this->once())
            ->method('hasTripAwardedPoints')
            ->with(10)
            ->willReturn(true);

        // يجب ألا يتم استدعاء store أو incrementLoyaltyPoints نهائياً
        $this->loyaltyRepo->expects($this->never())->method('store');
        $this->userRepo->expects($this->never())->method('incrementLoyaltyPoints');

        $trip = new TripRequest([
            'user_id'     => 5,
            'distance_km' => 10.0,
        ]);
        $trip->id = 10;

        $result = $this->loyaltyWalletService->awardPointsForTrip($trip);

        $this->assertNull($result);
    }

    public function test_points_are_not_awarded_when_points_per_km_is_zero_or_null()
    {
        // حالة points_per_km = 0
        $userSetting = new app_setting(['points_per_km' => 0]);
        $this->appSettingRepo->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        $this->loyaltyRepo->expects($this->never())->method('store');
        $this->userRepo->expects($this->never())->method('incrementLoyaltyPoints');

        $trip = new TripRequest([
            'user_id'     => 5,
            'distance_km' => 10.0,
        ]);
        $trip->id = 10;

        $result = $this->loyaltyWalletService->awardPointsForTrip($trip);
        $this->assertNull($result);
    }

    public function test_points_are_not_awarded_when_distance_is_zero()
    {
        $userSetting = new app_setting(['points_per_km' => 2.0]);
        $this->appSettingRepo->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        $this->loyaltyRepo->expects($this->never())->method('store');
        $this->userRepo->expects($this->never())->method('incrementLoyaltyPoints');

        $trip = new TripRequest([
            'user_id'     => 5,
            'distance_km' => 0,
        ]);
        $trip->id = 10;

        $result = $this->loyaltyWalletService->awardPointsForTrip($trip);
        $this->assertNull($result);
    }

    public function test_points_are_not_awarded_when_trip_has_no_user()
    {
        $trip = new TripRequest([
            'user_id'     => null,
            'distance_km' => 15.0,
        ]);
        $trip->id = 10;

        $this->appSettingRepo->expects($this->never())->method('getSetting');
        $this->loyaltyRepo->expects($this->never())->method('store');
        $this->userRepo->expects($this->never())->method('incrementLoyaltyPoints');

        $result = $this->loyaltyWalletService->awardPointsForTrip($trip);
        $this->assertNull($result);
    }

    public function test_points_rounding_precision()
    {
        $userSetting = new app_setting(['points_per_km' => 1.5]);
        $this->appSettingRepo->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        $this->loyaltyRepo->method('hasTripAwardedPoints')->willReturn(false);

        // الحالة الأولى: 3.4 * 1.5 = 5.1 -> تقريب إلى 5
        $this->loyaltyRepo->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($data) {
                return $data['points'] === 5;
            }))
            ->willReturn(new LoyaltyPointTransaction(['points' => 5]));

        $trip1 = new TripRequest(['user_id' => 5, 'distance_km' => 3.4]);
        $trip1->id = 101;

        $result1 = $this->loyaltyWalletService->awardPointsForTrip($trip1);
        $this->assertEquals(5, $result1->points);
    }

    public function test_points_rounding_up_precision()
    {
        $userSetting = new app_setting(['points_per_km' => 1.5]);
        $this->appSettingRepo->method('getSetting')
            ->with('user')
            ->willReturn($userSetting);

        $this->loyaltyRepo->method('hasTripAwardedPoints')->willReturn(false);

        // الحالة الثانية: 3.7 * 1.5 = 5.55 -> تقريب إلى 6
        $this->loyaltyRepo->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($data) {
                return $data['points'] === 6;
            }))
            ->willReturn(new LoyaltyPointTransaction(['points' => 6]));

        $trip2 = new TripRequest(['user_id' => 5, 'distance_km' => 3.7]);
        $trip2->id = 102;

        $result2 = $this->loyaltyWalletService->awardPointsForTrip($trip2);
        $this->assertEquals(6, $result2->points);
    }
}
