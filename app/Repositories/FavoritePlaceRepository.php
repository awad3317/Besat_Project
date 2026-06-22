<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\FavoritePlace;

class FavoritePlaceRepository implements RepositoriesInterface
{

    public function index()
    {
        return FavoritePlace::latest()->paginate(10);
    }

    public function getById($userId): FavoritePlace
    {
        return FavoritePlace::where('user_id', $userId)->get();
    }

    public function store(array $data): FavoritePlace
    {
        $ad = FavoritePlace::create($data);
        return $ad;
    }

    public function update(array $data, $id): FavoritePlace
    {
        $ad = FavoritePlace::findOrFail($id);
        $ad->update($data);
        return $ad;
    }

    public function delete($id): bool
    {
        $deleted = FavoritePlace::where('id', $id)->delete() > 0;
        return $deleted;
    }
}