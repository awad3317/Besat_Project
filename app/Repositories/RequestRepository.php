<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Request;

class RequestRepository implements RepositoriesInterface
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
        return Request::paginate(10);
    }

    public function getById($id): Request
    {
        return Request::findOrFail($id);
    }

    public function store(array $data): Request
    {
        return Request::create($data);
    }

    public function update(array $data, $id): Request
    {
        $Request = Request::findOrFail($id);
        $Request->update($data);
        return $Request;
    }
    public function delete($id): bool
    {
        return Request::where('id', $id)->delete() > 0;
    }

    public function getByUserIdWithRelations(int $userId, array $relations = [], $perPage = 10)
    {
        $query = Request::where('user_id', $userId)
            ->with($relations)
            ->orderBy('id', 'desc');
        if ($perPage === 'all' || empty($perPage) || $perPage <= 0) {
            return $query->get(); 
        }
        return $query->paginate((int) $perPage);
    }

}