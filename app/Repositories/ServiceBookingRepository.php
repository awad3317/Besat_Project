<?php
namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\ServiceBooking;

class ServiceBookingRepository implements RepositoriesInterface
{
    public function index()
    {
        //
    }
    public function getById($id): ?ServiceBooking
    {
        return ServiceBooking::find($id);
    }
    public function store(array $data): ServiceBooking
    {
        $booking = ServiceBooking::create($data);
        return $booking;
    }
    public function update(array $data, $id): ?ServiceBooking
    {
        $booking = $this->getById($id);
        if ($booking) {
            $booking->update($data);
            return $booking;
        }
        return null;
    }
    public function delete($id):bool
    {
        $booking = $this->getById($id);
        if ($booking) {
            $booking->delete();
            return true;
        }
        return false;
    }

    public function getByUserIdWithRelations(int $userId, array $relations = [], $perPage = 10)
    {
        $query = ServiceBooking::where('user_id', $userId)
            ->with($relations)
            ->orderBy('id', 'desc');

        if ($perPage === 'all' || empty($perPage) || $perPage <= 0) {
            return $query->get();
        }
        return $query->paginate((int) $perPage);
    }

    public function getByIdAndUserId(int $id, int $userId, array $relations = []): ?ServiceBooking
    {
        return ServiceBooking::where('id', $id)
            ->where('user_id', $userId)
            ->with($relations)
            ->first();
    }
}