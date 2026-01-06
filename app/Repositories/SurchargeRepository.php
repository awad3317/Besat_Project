<?php

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Surcharge;

class SurchargeRepository implements RepositoriesInterface
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
        return Surcharge::latest()->paginate(10);
    }

    public function getById($id): Surcharge
    {
        return Surcharge::findOrFail($id);
    }

    public function store(array $data): Surcharge
    {
        return Surcharge::create($data);
    }

    public function update(array $data, $id): Surcharge
    {
        $Surcharge = Surcharge::findOrFail($id);
        $Surcharge->update($data);
        return $Surcharge;
    }
    public function delete($id): bool
    {
        return Surcharge::where('id', $id)->delete() > 0;
    }

}
