<?php

namespace App\Http\Controllers;

use App\Models\activity_log;
use Illuminate\Http\Request;
use App\Repositories\ActivityLogRepository;

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
        $logs=$this->activityLogRepository->index();
        $stats = [
            'total' => activity_log::count(),
            'today' => activity_log::whereDate('created_at', \Carbon\Carbon::today())->count(),
            'yesterday' => activity_log::whereDate('created_at', \Carbon\Carbon::yesterday())->count(),
            'last_week' => activity_log::where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))->count(),
        ];
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
