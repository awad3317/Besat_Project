<?php

namespace App\Services\Payment\Strategies;

use App\Interfaces\PaymentStrategy;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\Traits\DynamicPaymentValidation;

class QutaibiStrategy implements PaymentStrategy 
{
    use DynamicPaymentValidation;

    private string $appKey;
    private string $apiKey;
    private string $destination;
    private string $baseUrl;
    public function __construct()
    {
        $this->appKey  = (string) config('services.qutaibi.app_key');
        $this->apiKey = (string) config('services.qutaibi.api_key');
        $this->destination = (string) config('services.qutaibi.payment_destination');
        $this->baseUrl = rtrim(config('services.qutaibi.base_url', 'https://newdc.qtb-bank.com:5052/BillPayLive'), '/');
    }
    public function initiatePayment(array $data): array 
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'request_otp');
        $phone = $this->formatPhoneNumber($validatedData['customer_number']);
        $paymentCode = (string) ($validatedData['purchase_code'] ?? '');
        $amount = (float) ($data['amount'] ?? 0);
        $currencyId = (int) ($data['currency_id'] ?? 1);
        if ($amount <= 0) {
            throw new Exception('مبلغ العملية غير صالح للدفع.');
        }
        $payload = [
            'TargetMSISDN' => $phone,
            'PaymentCode' => $paymentCode,
            'Amount' => $amount,
            'CurrencyId'=> $currencyId,
            'PaymentDestination' => $this->destination,
        ];
        try {
            $response = Http::withHeaders([
                'AppKey' => $this->appKey,
                'ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/RequestPayment", $payload);
            $body = $response->json();
            Log::info('Qutaibi Bank [RequestPayment] Gateway Response:', [
                'status_code' => $response->status(),
                'payload' => $payload,
                'response'=> $body,
            ]);
            if (!$response->successful() || !isset($body['Success']) || $body['Success'] !== true) {
                $errorMsg = $body['Message'] ?? $body['Error'] ?? 'فشل طلب رمز التحقق من بنك القطيبي.';
                throw new Exception($errorMsg);
            }
            return [
                'status' => 'success',
                'next_action' => 'require_otp',
                'transaction_id' => $body['Data']['TransactionId'] ?? $body['TransactionId'] ?? null,
                'message' => $body['Message'] ?? 'تم إرسال رمز التحقق بنجاح إلى هاتفك.',
            ];
        } catch (Exception $e) {
            Log::error('Qutaibi Bank [RequestPayment] Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function confirmPayment(array $data): array
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'submit');
        $customerNo = !empty($data['customer_number']) 
            ? $data['customer_number'] 
            : ($data['user_phone'] ?? '');
        $phone = $this->formatPhoneNumber($customerNo);
        $paymentCode = (string) ($data['purchase_code'] ?? '');
        $amount = (float) ($data['amount'] ?? 0);
        $currencyId = (int) ($data['currency_id'] ?? 1);
        $otp = (string) $validatedData['otp'];
        $payload = [
            'TargetMSISDN'       => $phone,
            'PaymentCode'        => $paymentCode,
            'Amount'             => $amount,
            'CurrencyId'         => $currencyId,
            'PaymentDestination' => $this->destination,
            'OTP'                => $otp,
        ];
        try {
            $response = Http::withHeaders([
                'AppKey' => $this->appKey,
                'ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'  => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/ConfirmPayment", $payload);
            $body = $response->json();
            Log::info('Qutaibi Bank [ConfirmPayment] Gateway Response:', [
                'status_code' => $response->status(),
                'payload'     => $payload,
                'response'    => $body,
            ]);
            if (!$response->successful() || !isset($body['Success']) || $body['Success'] !== true) {
                $errorMsg = $body['Message'] ?? $body['Error'] ?? 'فشل تأكيد خصم المبلغ من بنك القطيبي.';
                throw new Exception($errorMsg);
            }
            $bankTransactionId = $body['Data']['TransactionId'] 
                ?? $body['TransactionId'] 
                ?? ('QTB-' . now()->timestamp . '-' . rand(100, 999));
            return [
                'status'         => 'paid',
                'next_action'    => 'none',
                'transaction_id' => (string) $bankTransactionId,
                'message'        => $body['Message'] ?? 'تم خصم المبلغ بنجاح عبر بنك القطيبي.',
            ];
        } catch (Exception $e) {
            Log::error('Qutaibi Bank [ConfirmPayment] Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    private function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }
}
