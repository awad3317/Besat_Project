<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Repositories\VehicleRepository;
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
        'type' => ['required', 'string', 'max:100'],
        'description' => ['nullable', 'string', 'max:1000'],
        'max_passengers' => ['required', 'integer', 'min:1'],
        'image' => ['nullable', 'image', 'max:2048']
    ]);

    if ($validator->fails()) {
        $firstError = $validator->errors()->first();
        return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', true)
                    ->with('error_title', 'حدث خطأ!')
                    ->with('error_message', $firstError)
                    ->with('error_buttonText', 'حسناً');
    }
    $image_path=$this->imageService->saveImage($request->file('image'),'vehicles');
    $validatData = $validator->validated();
    $validatData['image'] = $image_path;
    $vehicleData = $this->vehicleRepository->store($validatData);
    return redirect()->back()
           ->with('success', true)
           ->with('success_title', 'تم الإضافة!')
           ->with('success_message', 'تم إضافة المركبة بنجاح.')
           ->with('success_buttonText', 'حسناً');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('pages.Vehicles.show', compact('vehicle'));
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
