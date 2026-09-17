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

    private string $baseUrl;
    private string $apiKey;
    private string $appKey;
    private int $destination;
    private string $encryptedCustomerNo;
    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.qutaibi.base_url', 'https://newdc.qtb-bank.com:5052/BillPayLive'), '/');
        $this->apiKey = (string) config('services.qutaibi.api_key');
        $this->appKey = (string) config('services.qutaibi.app_key');
        $this->destination = (int) config('services.qutaibi.destination', 11250875);
        $this->encryptedCustomerNo = (string) config('services.qutaibi.encrypted_customer_no', 'Batwy3YPl8D2ZVoucGQmkw==');
    }
    private function getHeaders(): array
    {
        return [
            'X-API-KEY'    => $this->apiKey,
            'X-APP-KEY'    => $this->appKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }
    public function initiatePayment(array $data): array 
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'request_otp');

        $phone = $this->formatPhoneNumber($validatedData['customer_number'] ?? $data['customer_number']);
        $paymentCode = (int) ($validatedData['purchase_code'] ?? $data['purchase_code']);
        $amount = (int) ($data['amount'] ?? 0);
        $currencyId = (int) ($data['currency_id'] ?? 1);

        if ($amount <= 0) {
            throw new Exception('مبلغ العملية غير صالح للدفع.');
        }

        $payload = [
            'customer_no'        => $this->encryptedCustomerNo,
            'payment_DestNation' => $this->destination,
            'payment_Code'       => $paymentCode,
            'payment_CustomerNo' => $phone,
            'payment_Amount'     => $amount,
            'payment_Curr'       => $currencyId,
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post("{$this->baseUrl}/E_Payment/RequestPayment", $payload);

            $body = $response->json();

            Log::info('Qutaibi Bank [RequestPayment] Response:', [
                'status_code' => $response->status(),
                'payload'     => $payload,
                'response'    => $body,
            ]);

            if (!$response->successful() || !isset($body['status']) || $body['status'] !== true) {
                $errorMsg = $body['description'] ?? 'فشل طلب رمز التحقق من بنك القطيبي.';
                throw new Exception($errorMsg);
            }
            $bankTransactionId = $body['transactionID'] ?? $body['refNo'] ?? ('QTB-' . now()->timestamp);
            return [
                'status'         => 'success',
                'next_action'    => 'require_otp',
                'transaction_id' => (string) $bankTransactionId,
                'message'        => $body['description'] ?? 'تم إرسال رمز التحقق بنجاح إلى هاتفك.',
            ];
        } catch (Exception $e) {
            Log::error('Qutaibi Bank [RequestPayment] Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function confirmPayment(array $data): array
    {
        $validatedData = $this->validateStepDynamic($data, 'qutaibi_pay', 'submit');

        $rawCustomerNo = !empty($validatedData['customer_number']) 
            ? $validatedData['customer_number'] 
            : ($data['customer_number'] ?? $data['user_phone'] ?? '');

        $phone = $this->formatPhoneNumber($rawCustomerNo);
        $paymentCode = (int) ($data['purchase_code'] ?? $validatedData['purchase_code']);
        $amount = (int) ($data['amount'] ?? 0);
        $currencyId = (int) ($data['currency_id'] ?? 1);
        $otp = (int) $validatedData['otp'];

        $payload = [
            'customer_no'        => $this->encryptedCustomerNo,
            'payment_DestNation' => $this->destination,
            'payment_CustomerNo' => $phone,
            'payment_Code'       => $paymentCode,
            'payment_Amount'     => $amount,
            'payment_Curr'       => $currencyId,
            'Payment_OTP'        => $otp,
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post("{$this->baseUrl}/E_Payment/ConfirmPayment", $payload);

            $body = $response->json();

            Log::info('Qutaibi Bank [ConfirmPayment] Response:', [
                'status_code' => $response->status(),
                'payload'     => $payload,
                'response'    => $body,
            ]);

            if (!$response->successful() || !isset($body['status']) || $body['status'] !== true) {
                $errorMsg = $body['description'] ?? 'فشل تأكيد خصم المبلغ من بنك القطيبي.';
                throw new Exception($errorMsg);
            }
            $bankTransactionId = $body['transactionID'] ?? $body['refNo'] ?? ('QTB-' . now()->timestamp);
            return [
                'status'         => 'paid',
                'next_action'    => 'none',
                'transaction_id' => (string) $bankTransactionId,
                'message'        => $body['description'] ?? 'تم خصم المبلغ بنجاح عبر بنك القطيبي.',
            ];
        } catch (Exception $e) {
            Log::error('Qutaibi Bank [ConfirmPayment] Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function resendOtp(array $data): array
    {
        $phone = $this->formatPhoneNumber($data['customer_number'] ?? '');
        $paymentCode = (int) ($data['purchase_code'] ?? 0);
        $amount = (int) ($data['amount'] ?? 0);
        $currencyId = (int) ($data['currency_id'] ?? 1);
        $expiredOtp = (int) ($data['otp'] ?? 0);

        $payload = [
            'customer_no'        => $this->encryptedCustomerNo,
            'payment_DestNation' => $this->destination,
            'payment_CustomerNo' => $phone,
            'payment_Code'       => $paymentCode,
            'payment_Amount'     => $amount,
            'payment_Curr'       => $currencyId,
            'Payment_OTP'        => $expiredOtp,
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->timeout(30)
            ->post("{$this->baseUrl}/E_Payment/ResendOTP", $payload);

        $body = $response->json();

        if (!$response->successful() || !isset($body['status']) || $body['status'] !== true) {
            throw new Exception($body['description'] ?? 'فشل إعادة إرسال رمز التحقق.');
        }

        return [
            'status'  => 'success',
            'message' => $body['description'] ?? 'تمت إعادة إرسال الرمز بنجاح.',
        ];
    }
    private function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }
}
