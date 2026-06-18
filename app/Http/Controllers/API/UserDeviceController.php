<?php

namespace App\Http\Controllers\API;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;

class UserDeviceController extends Controller
{
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
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type'  => 'nullable|string',
        ]);
        $user = auth('sanctum')->user();
        $currentSessionTokenId = $user->currentAccessToken()->id;
        $device = $user->devices()->updateOrCreate(
            [
                'device_token' => $request->device_token
            ], 
            [
                'device_type' => $request->device_type,
                'token_id'    => $currentSessionTokenId
            ]
        );
        return ApiResponseClass::sendResponse($device, 'Device stored successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDevice $userDevice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserDevice $userDevice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDevice $userDevice)
    {
        //
    }
}
