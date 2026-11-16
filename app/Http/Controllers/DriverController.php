<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Rating;
use App\Models\Request as RequestModel;
use App\Repositories\DriverRepository;
use App\Services\RatingService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(private DriverRepository $driverRepository, private RatingService $ratingService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view('pages.drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function show($id)
    {
        $driver = Driver::with('vehicle:id,type')->findOrFail($id);

        $ratingStats = Rating::where('driver_id', $driver->id)
            ->selectRaw('COUNT(*) as total_count, COALESCE(AVG(rating_value), 0) as avg_rating')
            ->first();

        $ratingsCount = (int) ($ratingStats->total_count ?? 0);
        $averageRating = $ratingsCount > 0 ? (float) $ratingStats->avg_rating : 0;
        $fullStars = (int) floor($averageRating);
        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;

        $requests = RequestModel::select([
            'id',
            'user_id',
            'driver_id',
            'final_price',
            'status',
            'start_address',
            'end_address',
            'created_at',
        ])
            ->where('driver_id', $driver->id)
            ->with(['user' => fn ($query) => $query->withTrashed()->select('id', 'name', 'phone', 'deleted_at')])
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'requests_page')
            ->withQueryString();

        $ratings = Rating::select([
            'id',
            'request_id',
            'driver_id',
            'user_id',
            'rating_value',
            'comment',
            'created_at',
        ])
            ->where('driver_id', $driver->id)
            ->with(['user' => fn ($query) => $query->withTrashed()->select('id', 'name', 'phone', 'deleted_at')])
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'ratings_page')
            ->withQueryString();

        return view('pages.drivers.show', compact(
            'driver',
            'requests',
            'ratings',
            'averageRating',
            'ratingsCount',
            'fullStars',
            'hasHalfStar'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
        //
    }
}
