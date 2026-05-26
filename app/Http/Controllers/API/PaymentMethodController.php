<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Classes\ApiResponseClass; // لا تنسَ استدعاء الكلاس هنا
use Exception;

class PaymentMethodController extends Controller
{
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
}