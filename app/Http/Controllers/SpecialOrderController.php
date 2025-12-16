<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use Illuminate\Validation\Rule;
use App\Classes\WebResponseClass;
use App\Repositories\DriverRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SpecialOrderRepository;

class SpecialOrderController extends Controller
{
    public function __construct(private SpecialOrderRepository $specialOrderRepository, private DriverRepository $driverRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders=$this->specialOrderRepository->index();
        $cancelledOrders = $this->specialOrderRepository->cancelledOrders()->count();
        $completedOrders = $this->specialOrderRepository->cancelledOrders()->count();
        $in_progressOrders = $this->specialOrderRepository->cancelledOrders()->count();
        return view('pages.specialOrder.index', compact(
            'orders',
            'cancelledOrders','completedOrders','in_progressOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.specialOrder.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'=>['required', 'string','max:255'],
            'customer_phone'=>['required', 'string', 'max:20'],
            'customer_whatsapp' => ['nullable', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'start_address'=>['required'],
            'end_address' => ['required'],
            'driver_id' => ['required', Rule::exists('drivers', 'id')],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $driver=$this->driverRepository->getById($request->driver_id);
            if(!$driver->is_online || !$driver->is_active){
                return WebResponseClass::sendError('السائق غير متاح او غير متصل');
            }
            $validatData = $validator->validated();
            $validatData['created_by'] = auth()->user()->id;
            $validatData['status'] = 'paused';
            $this->specialOrderRepository->store($validatData);
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
