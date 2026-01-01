<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use Illuminate\Validation\Rule;
use App\Classes\WebResponseClass;
use App\Repositories\RequestRepository;
use App\Repositories\VehicleRepository;
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function __construct(private RequestRepository $requestRepository,
    private VehicleRepository $vehicleRepository,
    private PriceCalculationService $priceCalculationService
   )
    {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('pages.request.index');
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
        $validator = Validator::make($request->all(), [
            'user_id'=>['required', Rule::exists(User::class, 'id')],
            'vehicle_id'=>['required',Rule::exists(Vehicle::class, 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_address'=>['required'],
            'start_latitude' => ['required','numeric'],
            'start_longitude' => ['required','numeric'],
            'end_latitude' => ['required','numeric'],
            'end_longitude' => ['required','numeric'],
            'end_address' => ['required'],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $validatData = $validator->validated();
            $distanceInKm = $this->priceCalculationService->getdistanceInKm(
                $validatData['start_latitude'],
                $validatData['start_longitude'],
                $validatData['end_latitude'],
                $validatData['end_longitude']
            );
            $vehicle=$this->vehicleRepository->getById($validatData['vehicle_id']);
            $price_per_km = $this->priceCalculationService->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
            $price_orginal = $this->priceCalculationService->calculatePrice($distanceInKm,$price_per_km,$vehicle->min_price);
            $validatData['price'] = $price_orginal;
            $validatData['created_by'] = auth()->user()->id;
            $validatData['status'] = 'paused';
            dd($validatData);
            $this->requestRepository->store($validatData);
            ActivityLog::log('create','SpecialOrder','تم إنشاء رحلة جديده');
            // send notification to driver

            //
            return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة الرحلة بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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
