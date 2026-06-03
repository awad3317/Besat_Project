<?php

namespace App\Services\Payment\Traits;

use App\Models\Bank;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

trait DynamicPaymentValidation
{
    /**
     * دالة تبني قواعد التحقق وتنفذها ديناميكياً من قاعدة البيانات 
     *
     * @param array $data البيانات القادمة من الموبايل
     * @param string $method_key مفتاح البنك
     * @param string $step_key مفتاح الخطوة
     * @return array
     * @throws Exception|ValidationException
     */
    public function validateStepDynamic(array $data, string $method_key, string $step_key): array
    {
        $bank = Bank::select('id', 'method_key')
            ->where('method_key', $method_key)
            ->with([
                'steps' => fn($query) => $query->select('id', 'bank_id', 'step_key')->where('step_key', $step_key),
                'steps.fields' => fn($query) => $query->select('id', 'payment_step_id', 'field_key', 'type', 'is_required', 'min_length', 'max_length')
            ])->first();

        if (!$bank || $bank->steps->isEmpty()) {
            throw new Exception("إعدادات البنك أو الخطوة [{$step_key}] غير مدعومة أو غير موجودة في قاعدة البيانات.");
        }

        $fields = $bank->steps->first()->fields;
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];
            $fieldRules[] = $field->is_required ? 'required' : 'nullable';

            if ($field->type === 'numeric') {
                $fieldRules[] = 'numeric';
                
                if ($field->min_length && $field->max_length) {
                    $fieldRules[] = "digits_between:{$field->min_length},{$field->max_length}";
                } elseif ($field->min_length) {
                    $fieldRules[] = "min_digits:{$field->min_length}";
                } elseif ($field->max_length) {
                    $fieldRules[] = "max_digits:{$field->max_length}";
                }
            } else {
                $fieldRules[] = 'string';
                
                if ($field->min_length) {
                    $fieldRules[] = "min:{$field->min_length}";
                }
                if ($field->max_length) {
                    $fieldRules[] = "max:{$field->max_length}";
                }
            }

            $rules[$field->field_key] = implode('|', $fieldRules);
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }
}