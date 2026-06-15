<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Repositories\RatingRepository;
use App\Repositories\RequestRepository;
use App\Services\RatingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{

    public function __construct(private RatingRepository $ratingRepository,private RatingService $ratingService,private RequestRepository $requestRepository)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
    }

    public function upsertRating(Request $request)
    {
        $fields=$request->validate([
            'request_id' => ['required', Rule::exists('requests', 'id')],
            'rating' =>['required','integer','min:1','max:5'],
            'comment'    => ['nullable', 'string', 'max:500'], 
        ], [
            'request_id.required' => 'يجب تحديد الرحلة المراد تقييمها.',
            'request_id.exists'   => 'الرحلة المحددة غير موجودة.',
            'rating.required'     => 'يجب إدخال قيمة التقييم.',
            'rating.integer'      => 'يجب أن يكون التقييم رقمًا صحيحًا.',
            'rating.min'          => 'يجب أن يكون التقييم على الأقل 1.',
            'rating.max'          => 'يجب أن لا يتجاوز التقييم 5.',
            'comment.max'         => 'يجب أن لا يتجاوز التعليق 500 حرف.',
        ]);
        $userId = auth('sanctum')->id();
        $ride = $this->requestRepository->getById($fields['request_id']);
        if (!$ride) {
            return ApiResponseClass::sendError('الرحلة المحددة غير موجودة.', 404);
        }
        if ($ride->user_id !== $userId) {
            return ApiResponseClass::sendError('غير مصرح لك بتقييم هذه الرحلة.', 403);
        }
        if ($ride->status !== 'completed') {
            return ApiResponseClass::sendError('لا يمكن تقييم الرحلة إلا بعد اكتمالها بنجاح.', 400);
        }
        $ratingData = [
            'request_id'   => $ride->id,
            'driver_id'    => $ride->driver_id,
            'user_id'      => $userId,
            'rating_value' => $fields['rating'],
            'comment'      => $request->comment,
        ];
        $rating = $this->ratingRepository->getByRequestId($ride->id);
        if ($rating) {
            $updatedRating = $this->ratingRepository->update($ratingData, $rating->id);
            return ApiResponseClass::sendResponse($updatedRating, 'تم تحديث التقييم بنجاح.');
        }
        $newRating = $this->ratingRepository->store($ratingData);
        return ApiResponseClass::sendResponse($newRating, 'تم إنشاء التقييم بنجاح.');
    }

    public function getDriverRating(Request $request, $driverId)
    {
        try {
            $rating = $this->ratingService->getDriverAverageRating((int)$driverId);
            return ApiResponseClass::sendResponse(['rating'=>$rating, 'driver_id'=>$driverId], 'Driver rating fetched successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('An error occurred while fetching driver rating:'.$e->getMessage(), 500);
        }
    }   
}
