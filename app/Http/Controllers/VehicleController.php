<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Vehicle;
use Mockery\Expectation;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use GPBMetadata\Google\Api\Auth;
use App\Classes\WebResponseClass;
use App\Repositories\VehicleRepository;
use App\Services\Notifications\FireBase;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function __construct(private VehicleRepository $vehicleRepository,private ImageService $imageService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = $this->vehicleRepository->index();
        return view('pages.Vehicles.index', compact('vehicles'));
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
            'type' => ['required', 'string', 'max:100',Rule::unique('vehicles','type')],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_passengers' => ['required', 'integer', 'min:1'],
            'min_price'=>['required','min:0'],
            'image' => ['nullable', 'image', 'max:2048']
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
    try {
        $validatData = $validator->validated();
        if($request->hasFile('image')){
            $image_path=$this->imageService->saveImage($request->file('image'),'vehicles');
            $validatData['image'] = $image_path;
        }        
        $vehicleData = $this->vehicleRepository->store($validatData);
        ActivityLog::log('create','Vehicle','تمت إضافة مركبه جديده');
        return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة المركبة بنجاح');
        }
    catch (Exception $e) {
        return WebResponseClass::sendExceptionError($e);
    }
}
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vehicle = $this->vehicleRepository->getById($id);
        return view('pages.Vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $vehicle=$this->vehicleRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit',true)
                ->with('vehicle', $vehicle);
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string', 'max:100',Rule::unique('vehicles','type')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_passengers' => ['required', 'integer', 'min:1'],
            'min_price'=>['required','min:0'],
            'image' => ['nullable', 'image', 'max:2048']
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $vehicle = $this->vehicleRepository->getById($id);
            $data = [
                'type' => $request->type,
                'description' => $request->description,
                'max_passengers' => $request->max_passengers,
                'min_price'=>$request->min_price,
            ];
            if ($request->hasFile('image')) {
                if ($vehicle->image) {
                    $this->imageService->deleteImage($vehicle->image);
                }
                $image_path=$this->imageService->saveImage($request->file('image'),'vehicles');
                $data['image'] = $image_path;
            }
            $this->vehicleRepository->update($data,$id);
            ActivityLog::log('update','Vehicle','تم تحديث مركبة ');
            return WebResponseClass::sendResponse( 'تم التحديث!', 'تم تحديث بيانات المركبة بنجاح');

        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
