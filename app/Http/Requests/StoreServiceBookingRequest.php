<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'service_type'      => ['required', 'string', 'max:255'],
            'vehicle_id'        => ['nullable', Rule::exists('vehicles', 'id')],
            'client_name'       => ['required', 'string', 'max:255'],
            'passenger_count'   => ['required', 'integer', 'min:1'],
            'notes'             => ['nullable', 'string', 'max:1000'],

            // نقطة البداية
            'start_address'     => ['required', 'string', 'max:255'],
            'start_latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'start_longitude'   => ['nullable', 'numeric', 'between:-180,180'],

            // نقطة النهاية
            'end_address'       => ['required', 'string', 'max:255'],
            'end_latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude'     => ['nullable', 'numeric', 'between:-180,180'],

            'vehicles_count'    => ['nullable', 'integer', 'min:1'],
            'wants_ac'          => ['nullable', 'boolean'],
            'trip_datetime'     => ['nullable', 'date_format:Y-m-d H:i:s'],
            'duration'          => ['nullable', 'string', 'max:255'],
            'service_details'   => ['nullable', 'array'],

            // نقاط التوقف
            'stops'               => ['nullable', 'array'],
            'stops.*.address_name' => ['nullable', 'string', 'max:255'],
            'stops.*.latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'stops.*.longitude'   => ['nullable', 'numeric', 'between:-180,180'],
        ];
        if (in_array($this->service_type, ['events', 'zafona', 'rehla', 'safety_airport'])) {
            $rules['bags_count'] = ['required', 'integer', 'min:0'];
        }
        if ($this->service_type === 'safety_airport') {
            $rules['airport_direction'] = ['required', Rule::in(['to_airport', 'from_airport'])];
        }
        if ($this->service_type === 'dawam') {
            $rules['work_or_school_name'] = ['required', 'string', 'max:255'];
            $rules['work_days']           = ['required', 'array', 'min:1'];
            $rules['work_days.*']         = ['string', Rule::in(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])];
            $rules['departure_time']      = ['required', 'string'];
            $rules['return_time']         = ['required', 'string'];
        }
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_type.required'        => 'نوع الخدمة مطلوب.',
            'client_name.required'         => 'اسم العميل مطلوب.',
            'passenger_count.required'     => 'عدد الركاب مطلوب.',
            'passenger_count.min'          => 'يجب أن يكون عدد الركاب 1 على الأقل.',
            'start_address.required'       => 'عنوان نقطة البداية مطلوب.',
            'end_address.required'         => 'عنوان نقطة النهاية مطلوب.',
            'bags_count.required'          => 'حقل عدد الحقائب مطلوب لهذه الخدمة.',
            'airport_direction.required'   => 'اتجاه رحلة المطار مطلوب (إلى/من المطار).',
            'work_or_school_name.required' => 'اسم المدرسة أو جهة العمل مطلوب.',
            'work_days.required'           => 'يرجى تحديد أيام الدوام.',
            'departure_time.required'      => 'وقت الانطلاق مطلوب.',
            'return_time.required'         => 'وقت العودة مطلوب.',
        ];
    }
}
