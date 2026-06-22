<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\FavoritePlaceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoritePlaceController extends Controller
{
    public function __construct(private FavoritePlaceRepository $favoritePlaceRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = auth('sanctum')->id();
            $places = $this->favoritePlaceRepository->getByUserId($userId);
            return ApiResponseClass::sendResponse($places, 'تم استرجاع الأماكن المفضلة بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching favorite places: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الأماكن المفضلة.', $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $fields=$request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'latitude'    => ['required','numeric'], 
            'longitude'    => ['required','numeric'], 
        ], [
            'latitude.required'     => 'يجب إدخال خط العرض.',
            'longitude.required'     => 'يجب إدخال خط الطول.',
        ]);
        try {
            $userId = auth('sanctum')->id();
            $fields['user_id'] = $userId;
            $place = $this->favoritePlaceRepository->store($fields);
            return ApiResponseClass::sendResponse($place, 'تم إنشاء مكان مفضل بنجاح.');
        } catch (Exception $e) {
            Log::error('Error storing favorite place: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء إنشاء مكان مفضل.', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $place = $this->favoritePlaceRepository->delete($id);
            return ApiResponseClass::sendResponse($place, 'تم حذف المكان المفضل بنجاح.');
        } catch (Exception $e) {
            Log::error('Error deleting favorite place: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء حذف المكان المفضل.', $e->getMessage(), 500);
        }
    }
}
