<?php

namespace App\Services\Payment\Strategies;

use App\Interfaces\PaymentStrategy;

class QutaibiStrategy implements PaymentStrategy 
{
    public function requestOtp(array $data): array 
    {
        return [
            'status' => 'success',
            'message' => 'تم إرسال رمز التحقق إلى هاتفك بنجاح.'
        ];
    }

    public function submitPayment(array $data): array 
    {
        $bankTransactionId = "TXN-QUT-" . rand(100000, 999999); 
        return [
            'status' => 'success',
            'transaction_id' => $bankTransactionId,
            'message' => 'تم خصم المبلغ بنجاح عبر بنك القطيبي.'
        ];
    }
}
