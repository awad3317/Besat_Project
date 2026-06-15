<?php

namespace App\Services;

use App\Repositories\RatingRepository;

class RatingService
{

    public function __construct(private RatingRepository $ratingRepository)
    {
        //
    }
    /**
     * حساب بيانات التقييم باستخدام معرف السائق
     * * @param int $driverId
     * @return array
     */
    public function calculateForDriver(int $driverId): array
    {
        $stats = $this->ratingRepository->getDriverStats($driverId);
        $ratingsCount = (int) $stats->count;

        if ($ratingsCount === 0) {
            return $this->emptyRatingData();
        }

        $average = (float) $stats->average;

        return [
            'average'     => $average,
            'count'       => $ratingsCount,
            'fullStars'   => (int) floor($average),
            'hasHalfStar' => (($average - floor($average)) >= 0.5),
            'display'     => number_format($average, 1),
            'percentage'  => ($average / 5) * 100,
            'stars'       => $this->getStarTypes($average)
        ];
    }
    
    /**
     * مصفوفة توضح حالة كل نجمة من النجوم الخمسة
     */
    public function getStarTypes($average): array
    {
        $fullStars = floor($average);
        $hasHalfStar = (($average - $fullStars) >= 0.5);
        
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $stars[$i] = 'full';
            } elseif ($hasHalfStar && $i == $fullStars + 1) {
                $stars[$i] = 'half';
            } else {
                $stars[$i] = 'empty';
            }
        }
        
        return $stars;
    }
    
    private function emptyRatingData(): array
    {
        return [
            'average'     => 0.0,
            'count'       => 0,
            'fullStars'   => 0,
            'hasHalfStar' => false,
            'display'     => '0.0',
            'percentage'  => 0,
            'stars'       => [1 => 'empty', 2 => 'empty', 3 => 'empty', 4 => 'empty', 5 => 'empty']
        ];
    }

    /**
    * جلب المتوسط العشري لتقييم السائق فقط
    *
    * @param int $driverId
    * @return float
    */
    public function getDriverAverageRating(int $driverId): float
    {
        $stats = $this->ratingRepository->getDriverStats($driverId);
        if (!$stats || (int) $stats->count === 0) {
            return 0.0;
        }
        return (float) round($stats->average, 1);
    }
}