<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\LoyaltyPointTransaction;

class LoyaltyRepository implements RepositoriesInterface
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
        return LoyaltyPointTransaction::paginate(10);
    }

    public function getById($id): LoyaltyPointTransaction
    {
        return LoyaltyPointTransaction::findOrFail($id);
    }

    public function store(array $data): LoyaltyPointTransaction
    {
        return LoyaltyPointTransaction::create($data);
    }

    public function update(array $data, $id): LoyaltyPointTransaction
    {
        $LoyaltyPointTransaction = LoyaltyPointTransaction::findOrFail($id);
        $LoyaltyPointTransaction->update($data);
        return $LoyaltyPointTransaction;
    }
    public function delete($id): bool
    {
        return LoyaltyPointTransaction::where('id', $id)->delete() > 0;
    }

}