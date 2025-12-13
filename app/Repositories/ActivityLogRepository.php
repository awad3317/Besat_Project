<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\activity_log;

class ActivityLogRepository implements RepositoriesInterface
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
        return activity_log::latest()->paginate(10);
    }

    public function getById($id): activity_log
    {
        return activity_log::findOrFail($id);
    }

    public function store(array $data): activity_log
    {
        return activity_log::create($data);
    }

    public function update(array $data, $id): activity_log
    {
        $activity_log = activity_log::findOrFail($id);
        $activity_log->update($data);
        return $activity_log;
    }
    public function delete($id): bool
    {
        return activity_log::where('id', $id)->delete() > 0;
    }

}