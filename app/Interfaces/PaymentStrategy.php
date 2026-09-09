<?php

namespace App\Interfaces;

interface PaymentStrategy
{
    public function initiatePayment(array $data): array;
    public function confirmPayment(array $data): array;
}