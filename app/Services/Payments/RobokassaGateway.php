<?php

namespace App\Services\Payments;

use App\Services\PaymentService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RobokassaGateway implements PaymentGatewayInterface
{
    private string $merchantLogin;
    private string $password_1;
    private string $password_2;
    private string $testPassword_1;
    private string $testPassword_2;
    private bool $isTest;
    private string $paymentUrl;
    private string $culture;
    private string $hashAlgorithm;
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->merchantLogin = config('robokassa.login');
        $this->password_1 = config('robokassa.password_1');
        $this->password_2 = config('robokassa.password_2');
        $this->testPassword_1 = config('robokassa.test_password_1');
        $this->testPassword_2 = config('robokassa.test_password_2');
        $this->isTest = config('robokassa.test_mode', false);
        $this->paymentUrl = config('robokassa.payment_url');
        $this->culture = config('robokassa.culture', 'ru');
        $this->hashAlgorithm = strtolower(config('robokassa.hash_algorithm', 'md5'));
        $this->paymentService = $paymentService;
    }

    private function getCurrentPassword1(): string
    {
        return $this->isTest ? $this->testPassword_1 : $this->password_1;
    }

    private function getCurrentPassword2(): string
    {
        return $this->isTest ? $this->testPassword_2 : $this->password_2;
    }

    public function generatePaymentUrl(string $transactionId, float $amount, string $description, array $additionalParams = []): string
    {
        $outSumFormatted = number_format($amount, 2, '.', '');

        $shpParams = [];
        if (!empty($additionalParams)) {
            foreach ($additionalParams as $key => $value) {
                if (str_starts_with($key, 'Shp_')) {
                    $shpParams[$key] = $value;
                }
            }
        }
        
        $receipt = null;
        if (isset($additionalParams['receipt'])) {
            $receipt = $this->generateReceipt($additionalParams['receipt']);
        }
        
        $signature = $this->generateRequestSignature($outSumFormatted, $transactionId, $receipt, $shpParams);

        $urlParams = [
            'MerchantLogin' => $this->merchantLogin,
            'OutSum' => $outSumFormatted,
            'InvId' => $transactionId,
            'Description' => $description,
            'SignatureValue' => $signature,
            'Culture' => $this->culture,
        ];
        
        if ($receipt) {
            $urlParams['Receipt'] = $receipt;
        }

        // Добавляем Shp параметры
        if (!empty($shpParams)) {
             $urlParams = array_merge($urlParams, $shpParams);
        }

        if (isset($additionalParams['Email'])) {
            $urlParams['Email'] = $additionalParams['Email'];
        }
        
        if ($this->isTest) {
            $urlParams['IsTest'] = '1';
        }

        return $this->paymentUrl . '?' . http_build_query($urlParams);
    }

    private function generateReceipt(array $items): ?string
    {
        if (empty($items)) {
            return null;
        }

        $receipt = [
            'sno' => config('robokassa.sno'),
            'items' => array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'sum' => $item['sum'],
                    'payment_method' => $item['payment_method'] ?? 'full_prepayment',
                    'payment_object' => $item['payment_object'] ?? 'service',
                    'tax' => $item['tax'] ?? 'none',
                ];
            }, $items)
        ];

        return urlencode(json_encode($receipt));
    }

    private function generateSignatureString(string $outSum, string $invId, string $password, ?string $receipt = null, ?array $shpParams = null, bool $includeLogin = true): string
    {
        $parts = [];
        if ($includeLogin) {
            $parts[] = $this->merchantLogin;
        }

        array_push($parts, $outSum, $invId);
        
        if ($receipt) {
            array_push($parts, $receipt);
        }
        
        array_push($parts, $password);

        if (!empty($shpParams)) {
            ksort($shpParams);
            foreach ($shpParams as $key => $value) {
                if ($value !== null && $value !== '') {
                    $parts[] = "$key=$value";
                }
            }
        }

        return implode(':', $parts);
    }

    public function generateRequestSignature(string $outSum, string $invId, ?string $receipt = null, ?array $shpParams = null): string
    {
        $signatureString = $this->generateSignatureString(
            $outSum,
            $invId,
            $this->getCurrentPassword1(),
            $receipt,
            $shpParams,
            true // Включаем логин для запроса
        );

        Log::debug('Robokassa Signature Generation String: ' . $signatureString);
        return hash($this->hashAlgorithm, $signatureString);
    }
    
    public function validatePaymentNotification(array $requestData): bool
    {
        $outSum = $requestData['OutSum'] ?? null;
        $invId = $requestData['InvId'] ?? null;
        $signatureValue = $requestData['SignatureValue'] ?? null;

        if (!$outSum || !$invId || !$signatureValue) {
            Log::error('Robokassa validation error: missing required parameters.', $requestData);
            return false;
        }

        $shpParams = [];
        foreach ($requestData as $key => $value) {
            if (str_starts_with(strtolower($key), 'shp_')) {
                $shpParams[$key] = $value;
            }
        }

        $password = $this->getCurrentPassword2(); // Для ResultURL всегда используется пароль #2

        // При валидации ответа Receipt НЕ участвует в формировании подписи
        $signatureString = $this->generateSignatureString($outSum, $invId, $password, null, $shpParams, false);
        
        $calculatedSignature = strtoupper(hash($this->hashAlgorithm, $signatureString));
        $receivedSignature = strtoupper($signatureValue);

        Log::debug('Robokassa signature check:', [
            'password_used' => 'password2',
            'string_to_hash' => $signatureString,
            'calculated' => $calculatedSignature,
            'received' => $receivedSignature,
            'match' => $calculatedSignature === $receivedSignature
        ]);

        return $calculatedSignature === $receivedSignature;
    }

    public function processSuccessfulPayment(array $requestData): bool
    {
        $transactionId = (int)($requestData['InvId'] ?? 0);
        $shpUserId = (int)($requestData['Shp_user'] ?? 0);
        $shpTrId = (int)($requestData['Shp_tr_id'] ?? 0); // Наш внутренний ID транзакции

        if ($shpTrId > 0) {
            // Приоритет на наш ID транзакции, если он передан
             return $this->paymentService->processSuccessfulBalancePayment($shpTrId);
        } elseif ($transactionId > 0) {
            // Если нашего ID нет, ищем по ID счета робокассы
             return $this->paymentService->processSuccessfulBalancePayment($transactionId);
        }

        Log::error('Robokassa process error: missing transaction ID (InvId or Shp_tr_id)');
        return false;
    }

    public function getName(): string
    {
        return 'robokassa';
    }
} 