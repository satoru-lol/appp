<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class RobokassaPayment extends Component
{
    public string $paymentUrl;
    public string $qrCode;

    public function __construct(
        public int $invoiceId,
        public int|float $amount = 1,
        public string $description = 'Оплата подписки'
    ) {
        $merchantLogin = config('services.robokassa.login');
        $password_1 = config('services.robokassa.password_1');
        $isTest = app()->isLocal() ? 1 : 0;
        $shpUser = auth()->user()->id;

        // Генерация подписи по правилам Робокассы
        $signatureValue = md5("$merchantLogin:$this->amount:$this->invoiceId:$password_1:Shp_user=$shpUser");

        // Формирование URL
        $this->paymentUrl = "https://auth.robokassa.ru/Merchant/Index.aspx?" . http_build_query([
            'MerchantLogin' => $merchantLogin,
            'OutSum' => $this->amount,
            'InvoiceID' => $this->invoiceId,
            'SignatureValue' => $signatureValue,
            'Shp_user' => $shpUser,
            'IsTest' => $isTest,
            'Description' => $this->description,
        ]);
        
        // Генерация QR-кода
        $this->qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($this->paymentUrl);
    }

    public function render(): View
    {
        return view('components.robokassa-payment');
    }
} 