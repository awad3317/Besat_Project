<?php

namespace App\Repositories;

use App\Models\RequestStop;

class RequestStopRepository
{
    /**
     * تخزين نقاط التوقف المتعددة لطلب معين
     */
    public function store(int $requestId, array $stopsData)
    {
        $formattedStops = [];
        
        foreach ($stopsData as $index => $stop) {
            $formattedStops[] = [
                'request_id' => $requestId,
                'latitude'   => $stop['latitude'],
                'longitude'  => $stop['longitude'],
                'stop_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return RequestStop::insert($formattedStops);
    }
}