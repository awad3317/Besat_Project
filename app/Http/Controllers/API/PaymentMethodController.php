<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass; // لا تنسَ استدعاء الكلاس هنا
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Request as TripRequest;
use App\Services\Payment\PaymentFactory;
use App\Services\TripDispatchService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function __construct(private TripDispatchService $tripDispatchService)
    {
    }

    public function getDigitalPayments()
{
    try {
        
        $resultData = Cache::remember('digital_payments_active', 60 * 60 * 24, function () {
            $banks = Bank::select('id', 'name', 'logo', 'color')
                ->with([
                    'currencies:id,name,bank_id', 
                    'steps' => function($query) {
                        $query->select('id', 'bank_id', 'step_key', 'action_text', 'action_url', 'sort_order')
                              ->orderBy('sort_order');
                    },
                    
                    'steps.fields:id,step_id,field_key,default_value,label,hint,description,type,is_hidden,is_required,min_length,max_length'
                ])
                ->where('is_active', true)
                ->get();

            $formattedBanks = $banks->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'image' => $bank->logo,
                    'color' => $bank->color,
                    'is_selected' => false,
                    'currencies' => $bank->currencies->map(function ($currency) {
                        return [
                            'id' => $currency->id,
                            'name' => $currency->name
                        ];
                    }),
                    'steps' => $bank->steps->map(function ($step) {
                        return [
                            'key' => $step->step_key,
                            'action' => [
                                'text' => $step->action_text,
                                'url' => $step->action_url ?? ""
                            ],
                            'fields' => $step->fields->map(function ($field) {
                                return array_filter([
                                    'key' => $field->field_key,
                                    'default_value' => $field->default_value,
                                    'label' => $field->label,
                                    'hint' => $field->hint ?? "",
                                    'description' => $field->description ?? "",
                                    'type' => $field->type,
                                    'is_hidden' => $field->is_hidden,
                                    'is_required' => $field->is_required,
                                    'min' => $field->min_length,
                                    'max' => $field->max_length,
                                ], function($val) { 
                                    return $val !== null; 
                                });
                            })->values()
                        ];
                    })
                ];
            });

            return [
                'digital_payment' => [
                    'method_key' => 'digital_payment',
                    'method_name' => 'الدفع الإلكتروني',
                    'banks' => $formattedBanks
                ]
            ];
        });

        return ApiResponseClass::sendResponse(
            $resultData, 
            'تم جلب بوابات الدفع بنجاح', 
            200
        );

    } catch (\Exception $e) {
        return ApiResponseClass::throw($e, 'Error: ' . $e->getMessage() . ' in line: ' . $e->getLine());
    }
}

    public function processPayment(Request $request, $action, $method_key)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
        ]);
        DB::beginTransaction();

        try {
            $tripRequest = TripRequest::findOrFail($request->request_id);
            $bankStrategy = PaymentFactory::make($method_key);
            if ($action === 'request_otp') {
                
                $response = $bankStrategy->requestOtp($request->all());
                DB::commit(); 
                return ApiResponseClass::sendResponse($response, $response['message']);

            } elseif ($action === 'submit') {
                
                $response = $bankStrategy->submitPayment($request->all());

                if ($response['status'] === 'success') {
                    $tripRequest->update([
                        'payment_method' => 'digital_payment',
                        'payment_status' => 'paid',
                        'transaction_id' => $response['transaction_id'], 
                        'bank_id'        => Bank::where('method_key', $method_key)->first()?->id 
                    ]);

                    DB::commit();
                    $this->tripDispatchService->dispatchToDrivers($tripRequest);

                    return ApiResponseClass::sendResponse([
                        'trip_request_id' => $tripRequest->id,
                        'transaction_id'  => $tripRequest->transaction_id,
                        'payment_status'  => 'paid'
                    ], $response['message']);
                }

                throw new \Exception("فشلت عملية الدفع من خلال البنك");
            }

            throw new \Exception("الإجراء المطلوب غير مدعوم في النظام");

        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponseClass::sendValidationError('بيانات الإدخال غير صالحة', $e->errors());

        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'حدث خطأ أثناء معالجة العملية المالية: ' . $e->getMessage());
        }
    }
}