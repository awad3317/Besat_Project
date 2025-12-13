<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\VehiclePricingRepository;

class vehiclePricingController extends Controller
{
    public function __construct(private VehiclePricingRepository $VehiclePricingRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'vehicle_id'=>['required',Rule::exists('vehicles','id')],
            'base_price'=>['required'],
            'min_distance_km'=>['required','numeric','min:0'],
            'max_distance_km'=>['required','numeric','max:999','gt:min_distance_km']
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
        try {
            $existingPricings = $this->VehiclePricingRepository->getByVehicleId($request->vehicle_id);
            if ($existingPricings->isEmpty() && $request->min_distance_km != 0) {
                return redirect()->back()
                    ->with('error', true)
                    ->with('error_title', 'خطأ في القيمة!')
                    ->with('error_message', 'يجب أن تكون المسافة الأولى من 0 كم.')
                    ->with('error_buttonText', 'حسناً');
            }
            foreach ($existingPricings as $pricing) {
                if (
                    ($request->min_distance_km >= $pricing->min_distance_km && $request->min_distance_km < $pricing->max_distance_km) ||
                    ($request->max_distance_km > $pricing->min_distance_km && $request->max_distance_km <= $pricing->max_distance_km) ||
                    ($request->min_distance_km <= $pricing->min_distance_km && $request->max_distance_km >= $pricing->max_distance_km)
                ){
                    return redirect()->back()
                    ->with('error', true)
                    ->with('error_title', 'حدث تداخل!')
                    ->with('error_message', 'هذا النطاق يتداخل مع نطاق موجود بالفعل.')
                    ->with('error_buttonText', 'حسناً');
                }
            }
            if ($existingPricings->isNotEmpty())
            {
                $hasConnection = false;
                foreach ($existingPricings as $pricing) {
                    if ($request->min_distance_km == $pricing->max_distance_km || 
                        $request->max_distance_km == $pricing->min_distance_km) {
                        $hasConnection = true;
                        break;
                    }
                }
                if (!$hasConnection && $existingPricings->count() > 0){
                    return redirect()->back()
                        ->with('error', true)
                        ->with('error_title', 'فراغ في النطاق!')
                        ->with('error_message', 'يجب أن يتصل النطاق الجديد بالنطاقات الحالية دون فراغات.')
                        ->with('error_buttonText', 'حسناً');
                    }
            }

            $VehiclePricing = $this->VehiclePricingRepository->store($request->all());
            return redirect()->back()
            ->with('success', true)
            ->with('success_title', 'تم الإضافة!')
            ->with('success_message', 'تم إضافة السعر بنجاح.')
            ->with('success_buttonText', 'حسناً');
        
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
    public function edit($id)
    {
        try {
            $VehiclePricing=$this->VehiclePricingRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit',true)
                ->with('VehiclePricing', $VehiclePricing);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'حدث خطأ!')
                ->with('error_message', $e->getMessage())
                ->with('error_buttonText', 'حسناً')
                ->with('openModalEdit',false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'base_price'=>['required'],
            'max_distance_km'=>['required','numeric','max:999']
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
        try {
            $VehiclePricing=$this->VehiclePricingRepository->getById($id);
            if($VehiclePricing->min_distance_km > $request->max_distance_km){
                return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'حدث خطأ!')
                ->with('error_message', 'يجب أن يكون المسافه اكبر')
                ->with('error_buttonText', 'حسناً');
            }
            $data = [
                'base_price' => $request->base_price,
                'max_distance_km' => $request->max_distance_km,
            ];
            $this->VehiclePricingRepository->update($data,$id);
            return redirect()->back()
                ->with('success', true)
                ->with('success_title', 'تم التحديث!')
                ->with('success_message', 'تم تحديث السعر بنجاح')
                ->with('success_buttonText', 'حسناً');

        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $VehiclePricing=$this->VehiclePricingRepository->getById($id);
            if($this->VehiclePricingRepository->delete($id)){
                return redirect()->back()
                ->with('success', true)
                ->with('success_title', 'تم الحدف!')
                ->with('success_message', 'تم حدف السعر بنجاح')
                ->with('success_buttonText', 'حسناً');
            }
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'حدث خطأ!')
                ->with('error_message', 'فشل في حدف السعر ')
                ->with('error_buttonText', 'حسناً');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'حدث خطأ!')
                ->with('error_message', 'فشل في حدف السعر ')
                ->with('error_buttonText', 'حسناً');
        }
    }
}
