<?php

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\SpecialOrder;

class SpecialOrderRepository implements RepositoriesInterface
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        return SpecialOrder::with(['driver', 'user'])->latest()->paginate(10);
    }

    public function getById($id): SpecialOrder
    {
        return SpecialOrder::with(['driver', 'user', 'creator', 'canceller'])->findOrFail($id);
    }

    public function store(array $data): SpecialOrder
    {
        return SpecialOrder::create($data);
    }

    public function update(array $data, $id): SpecialOrder
    {
        $specialOrder = SpecialOrder::findOrFail($id);
        $specialOrder->update($data);
        return $specialOrder;
    }

    public function delete($id): bool
    {
        return SpecialOrder::where('id', $id)->delete() > 0;
    }

    public function cancelledOrders()
    {
        return SpecialOrder::where('status', 'cancelled')->get();
    }

    public function completedOrders()
    {
        return SpecialOrder::where('status', 'completed')->get();
    }

    public function in_progressOrders()
    {
        return SpecialOrder::whereIn('status', ['in_progress', 'pending', 'searching_driver', 'paused'])->get();
    }
}
