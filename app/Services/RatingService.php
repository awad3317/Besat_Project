<?php

namespace App\Services;

class RatingService
{
    public function calculateForDriver($driver)
    {
        $ratings = $driver->ratings;
        $ratingsCount = $ratings->count();
        
        if ($ratingsCount === 0) {
            return $this->emptyRatingData();
        }
        
        $average = $ratings->avg('rating_value');
        
        return [
            'average' => $average,
            'count' => $ratingsCount,
            'fullStars' => floor($average),
            'hasHalfStar' => (($average - floor($average)) >= 0.5),
            'display' => number_format($average, 1),
            'percentage' => ($average / 5) * 100
        ];
    }
    
    public function getStarTypes($average)
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
    
    private function emptyRatingData()
    {
        return [
            'average' => 0,
            'count' => 0,
            'fullStars' => 0,
            'hasHalfStar' => false,
            'display' => '0.0',
            'percentage' => 0
        ];
    }
}