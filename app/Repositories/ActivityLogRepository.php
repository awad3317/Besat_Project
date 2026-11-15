<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\activity_log;
use Carbon\Carbon;

class ActivityLogRepository implements RepositoriesInterface
{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index(?string $filter = 'all')
    {
        return activity_log::query()
            ->when($filter === 'today', fn ($q) => $q->whereDate('created_at', Carbon::today()))
            ->when($filter === 'yesterday', fn ($q) => $q->whereDate('created_at', Carbon::yesterday()))
            ->when($filter === 'last_week', fn ($q) => $q->where('created_at', '>=', Carbon::now()->subDays(7)->toDateTimeString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();
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