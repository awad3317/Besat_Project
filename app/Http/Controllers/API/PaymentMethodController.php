<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass; // لا تنسَ استدعاء الكلاس هنا
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Services\TripDispatchService;
use App\Models\Request as TripRequest;
use App\Services\Payment\PaymentFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function __construct(private TripDispatchService $tripDispatchService)
    {
    }

    public function getDigitalPayments()
    {
        try {
            $banks = Bank::with([
                'currencies', 
                'steps' => function($query) {
                    $query->orderBy('sort_order');
                }, 
                'steps.fields'
            ])->where('is_active', true)->get();

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

            $resultData = [
                'digital_payment' => [
                    'method_key' => 'digital_payment',
                    'method_name' => 'الدفع الإلكتروني',
                    'banks' => $formattedBanks
                ]
            ];

            return ApiResponseClass::sendResponse(
                $resultData, 
                'تم جلب بوابات الدفع بنجاح', 
                200
            );

        } catch (Exception $e) {
            return ApiResponseClass::throw($e, 'حدث خطأ أثناء جلب بوابات الدفع');
        }
    }

    public function processPayment(Request $request, $action, $method_key)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
        ]);

        // 2. حماية قاعدة البيانات (Transaction)
        DB::beginTransaction();

        try {
            // جلب طلب الرحلة المطلوب دفعها
            $tripRequest = TripRequest::findOrFail($request->request_id);

            // استدعاء كلاس البنك المناسب ديناميكياً عبر الـ Factory
            $bankStrategy = PaymentFactory::make($method_key);

            // توجيه العملية بناءً على الـ action
            if ($action === 'request_otp') {
                
                $response = $bankStrategy->requestOtp($request->all());
                DB::commit(); 
                return ApiResponseClass::sendResponse($response, $response['message']);

            } elseif ($action === 'submit') {
                
                // تنفيذ عملية الخصم
                $response = $bankStrategy->submitPayment($request->all());

                if ($response['status'] === 'success') {
                    // التحديث المالي لجدول الطلبات
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

                throw new Exception("فشلت عملية الدفع من خلال البنك");
            }

            throw new Exception("الإجراء المطلوب غير مدعوم في النظام");

        } catch (Exception $e) {
            return ApiResponseClass::rollback($e, 'حدث خطأ أثناء معالجة العملية المالية: ' . $e->getMessage());
        }
    }
}