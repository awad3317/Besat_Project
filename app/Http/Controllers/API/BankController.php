<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $banks = Cache::rememberForever('active_banks_list', function () {
                return Bank::where('is_active', true)
                    ->select('id', 'name', 'account_name', 'account_number', 'logo')
                    ->get();
            });
            return ApiResponseClass::sendResponse($banks, 'Banks retrieved successfully from cache.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Failed to retrieve banks. ' . $e->getMessage(), null, 500);
        }
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
