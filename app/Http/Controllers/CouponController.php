<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\Validator;
use App\Repositories\DiscountCodeRepository;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{

    public function __construct(private DiscountCodeRepository $DiscountCodeRepository)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.coupons.index');
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
            'code' => ['required', 'string', 'max:50', Rule::unique(DiscountCode::class, 'code')],
            'discount_rate' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_uses' => ['required', 'integer', 'min:1'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $validatedData = $validator->validated();
            $validatedData['discount_rate'] = $validatedData['discount_rate'] / 100;
            DiscountCode::create($validatedData);
            ActivityLog::log('create', 'DiscountCode', 'تم إضافة كوبون خصم جديد');
            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة الكوبون بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $coupon = DiscountCode::findOrFail($id);
        $users = $coupon->users()->paginate(10);
        return view('pages.coupons.show', compact('coupon','users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $coupon=$this->DiscountCodeRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit',true)
                ->with('Coupon', $coupon);
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
            'code' => ['required', 'string', 'max:50', 'unique:discount_codes,code,'.$id],
            'discount_rate' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_uses' => ['required', 'integer', 'min:1'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $validatedData = $validator->validated();
            $coupon = $this->DiscountCodeRepository->getById($id);
            if($validatedData['max_uses'] < $coupon->current_uses){
                return WebResponseClass::sendError('يجب أن يكون الحد الأقصى للاستخدام أكبر من أو يساوي عدد الاستخدامات الحالية للكوبون.', 'خطأ في الحد الأقصى للاستخدام');
            }
            if($validatedData['usage_limit_per_user'] < $coupon->usage_limit_per_user){
                return WebResponseClass::sendError('يجب أن يكون حد الاستخدام لكل مستخدم أكبر من أو يساوي الحد السابق.', 'خطأ في حد الاستخدام لكل مستخدم');  
            }
            $validatedData['discount_rate'] = $validatedData['discount_rate'] / 100;

            $this->DiscountCodeRepository->update($validatedData, $id);
            ActivityLog::log('update', 'DiscountCode', 'تم تعديل كوبون خصم');

            return WebResponseClass::sendResponse('تم التعديل!', 'تم تعديل الكوبون بنجاح');
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
