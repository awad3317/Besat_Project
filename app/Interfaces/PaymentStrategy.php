<?php

namespace App\Interfaces;

interface PaymentStrategy
{
    /**
     * دالة طلب رمز التحقق (OTP)
     */
    public function requestOtp(array $data): array;

    /**
     * دالة تأكيد الدفع والخصم النهائي
     */
    public function submitPayment(array $data): array;
}