<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Vehicle;
use Twilio\Rest\Conversations\V1;

class VehicleRepository implements RepositoriesInterface
{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Vehicle::paginate(10);
    }

    public function getById($id): Vehicle
    {
        return Vehicle::findOrFail($id);
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

    public function findByPhone($phone)
    {
        return Vehicle::where('phone', $phone)->first();
    }

}