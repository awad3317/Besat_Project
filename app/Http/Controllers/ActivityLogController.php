<?php

namespace App\Http\Controllers;

use App\Models\activity_log;
use App\Repositories\ActivityLogRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogRepository $activityLogRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $logs = $this->activityLogRepository->index();

    $today = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();
    $lastWeek = Carbon::now()->subDays(7)->toDateTimeString();
    $stats = activity_log::selectRaw("
        COUNT(*) as total,
        COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as today,
        COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as yesterday,
        COUNT(CASE WHEN created_at >= ? THEN 1 END) as last_week
    ", [$today, $yesterday, $lastWeek])->first()->toArray();

    return view('pages.ActivityLog.index', compact('logs', 'stats'));
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
    public function show(activity_log $activity_log)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(activity_log $activity_log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, activity_log $activity_log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(activity_log $activity_log)
    {
        //
    }
}
