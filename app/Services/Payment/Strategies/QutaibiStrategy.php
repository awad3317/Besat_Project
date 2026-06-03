<?php

namespace App\Services\Payment\Strategies;

use App\Interfaces\PaymentStrategy;
use App\Services\Payment\Traits\DynamicPaymentValidation;

class QutaibiStrategy implements PaymentStrategy 
{
    use DynamicPaymentValidation;
    public function requestOtp(array $data): array 
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'request_otp');
        return [
            'status' => 'success',
            'message' => 'تم إرسال رمز التحقق إلى هاتفك بنجاح.'
        ];
    }

    public function submitPayment(array $data): array 
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'submit');
        $bankTransactionId = "TXN-QUT-" . rand(100000, 999999); 
        return [
            'status' => 'success',
            'transaction_id' => $bankTransactionId,
            'message' => 'تم خصم المبلغ بنجاح عبر بنك القطيبي.'
        ];
    }
}
