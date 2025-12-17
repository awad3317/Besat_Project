<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DriverRepository;
use App\Services\RatingService;

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
        $drivers=$this->driverRepository->index();
        $onlineDrivers=$this->driverRepository->getIsOnline()->count();
        $bannedDrivers=$this->driverRepository->getIsBanned()->count();
        return view('pages.drivers.index',compact('drivers','onlineDrivers','bannedDrivers'));
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
        $driver=$this->driverRepository->getById($id);
        $ratings = $driver->ratings;
        $ratingsCount = $ratings->count();
        if ($ratingsCount > 0) {
        $averageRating = $ratings->avg('rating_value');
        $fullStars = floor($averageRating);
        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
    } else {
        $averageRating = 0;
        $fullStars = 0;
        $hasHalfStar = false;
    }
        return view('pages.drivers.show',compact('driver','averageRating',
        'ratingsCount',
        'fullStars',
        'hasHalfStar'));
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
