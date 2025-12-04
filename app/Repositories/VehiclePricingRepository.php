<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\vehicle_pricing;

class VehiclePricingRepository implements RepositoriesInterface
{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        return vehicle_pricing::all();
    }

    public function getById($id): vehicle_pricing
    {
        return vehicle_pricing::findOrFail($id);
    }

    public function store(array $data): vehicle_pricing
    {
        return vehicle_pricing::create($data);
    }

    public function update(array $data, $id): vehicle_pricing
    {
        $vehicle_pricing = vehicle_pricing::findOrFail($id);
        $vehicle_pricing->update($data);
        return $vehicle_pricing;
    }
    public function delete($id): bool
    {
        return vehicle_pricing::where('id', $id)->delete() > 0;
    }

    public function getByVehicleId($vehicleId)
    {
        return vehicle_pricing::where('vehicle_id', $vehicleId)->orderBy('min_distance_km', 'asc')->get();
    }

}