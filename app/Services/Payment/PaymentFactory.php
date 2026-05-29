<?php

namespace App\Services\Payment;

use App\Services\Payment\Strategies\QutaibiStrategy;
use App\Interfaces\PaymentStrategy;
use Exception;

class PaymentFactory
{
    public static function make(string $method_key): PaymentStrategy
    {
        return match ($method_key) {
            'qutaibi_pay' => new QutaibiStrategy(),
            default => throw new Exception("طريقة الدفع المحددة [{$method_key}] غير مدعومة حالياً.")
        };
    }
}