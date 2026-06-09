<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\WalletTransaction;

class WalletRepository implements RepositoriesInterface
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
        return WalletTransaction::paginate(10);
    }

    public function getById($id): WalletTransaction
    {
        return WalletTransaction::findOrFail($id);
    }

    public function store(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }

    public function update(array $data, $id): WalletTransaction
    {
        $WalletTransaction = WalletTransaction::findOrFail($id);
        $WalletTransaction->update($data);
        return $WalletTransaction;
    }
    public function delete($id): bool
    {
        return WalletTransaction::where('id', $id)->delete() > 0;
    }

}