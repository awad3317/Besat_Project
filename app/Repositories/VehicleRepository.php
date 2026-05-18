<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;

class VehicleRepository implements RepositoriesInterface
{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $vehicle->restore();
        return $vehicle;
    }
    public function indexWithTrashed()
    {
        return Vehicle::withTrashed()->paginate(10);
    }
    public function getAllVehiclesCached()
    {
        
        return Cache::rememberForever('vehicles_list', function () {
            return Vehicle::select(['id', 'type', 'image', 'max_passengers'])->get();
        });
    }
    public function index()
    {
        return Vehicle::select(['id', 'type', 'image', 'max_passengers'])->get();
    }

    public function getById($id): Vehicle
    {
        return Vehicle::with(['drivers','pricing'])->findOrFail($id);
    }

    public function store(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(array $data, $id): Vehicle
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($data);
        return $vehicle;
    }
    public function delete($id): bool
    {
        return Vehicle::where('id', $id)->delete() > 0;
    }

}