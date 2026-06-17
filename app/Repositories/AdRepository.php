<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\ads;
use Illuminate\Support\Facades\Cache;

class AdRepository implements RepositoriesInterface
{
    const CACHE_KEY = 'mobile_active_ads';

    public function index()
    {
        return ads::latest()->paginate(10);
    }

    public function getById($id): ads
    {
        return ads::findOrFail($id);
    }

    public function getActiveAdsForMobile()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return ads::where('is_active', true)
                ->select('id', 'image_path')
                ->latest()
                ->get();
        });
    }

    public function store(array $data): ads
    {
        $ad = ads::create($data);
        $this->clearAdsCache(); 
        return $ad;
    }

    public function update(array $data, $id): ads
    {
        $ad = ads::findOrFail($id);
        $ad->update($data);
        $this->clearAdsCache(); 
        return $ad;
    }

    public function delete($id): bool
    {
        $deleted = ads::where('id', $id)->delete() > 0;
        if ($deleted) {
            $this->clearAdsCache(); 
        }
        return $deleted;
    }

    public function clearAdsCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}