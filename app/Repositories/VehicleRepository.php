<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\vehicle;

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
        return vehicle::paginate(10);
    }

    public function getById($id): vehicle
    {
        return vehicle::findOrFail($id);
    }

    public function store(array $data): vehicle
    {
        return vehicle::create($data);
    }

    public function update(array $data, $id): vehicle
    {
        $vehicle = vehicle::findOrFail($id);
        $vehicle->update($data);
        return $vehicle;
    }
    public function delete($id): bool
    {
        return vehicle::where('id', $id)->delete() > 0;
    }

    public function findByPhone($phone)
    {
        return vehicle::where('phone', $phone)->first();
    }

}