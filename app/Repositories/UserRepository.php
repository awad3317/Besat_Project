<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\User;

class UserRepository implements RepositoriesInterface
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
        return User::paginate(10);
    }

    public function getById($id): User
    {
        return User::with(['requests'])->findOrFail($id);
    }

    public function store(array $data): User
    {
        return User::create($data);
    }

    public function update(array $data, $id): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }
    public function delete($id): bool
    {
        return User::where('id', $id)->delete() > 0;
    }

    public function findByPhone($phone)
    {
        return User::where('phone', $phone)->first();
    }

    public function getAdmins($request)
    {
        $query = User::where('type','admin');
        if ($request->has('is_banned') && $request->is_banned !== null) {
            $query->where('is_banned', 1);
        }
        return $query->paginate(10);
    }

     public function getUsers()
    {
        return User::where('type','user')->with(['requests'])->paginate(10);
    }

}