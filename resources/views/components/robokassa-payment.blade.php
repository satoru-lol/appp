<div class="custom-payment-form iframe-container">
    <div class="col-md-8">
        @php
            $merchant_login = "APPP";
            $password_1 = "LFuWhwWF2H63Uaf3Wwz1";
            $description = "Пополнение баланса личного кабинета, пользователь " . auth()->user()->id;
            $shpUser = auth()->user()->id;
            // Сигнатура для FormFLS.js не включает сумму, она передается отдельно.
            $signature_value = md5("$merchant_login::$invoiceId:$password_1:Shp_user=$shpUser");
            $isTest = 0;
        @endphp
        <script language="JavaScript"
                src="https://auth.robokassa.ru/Merchant/PaymentForm/FormFLS.js?MerchantLogin={{$merchant_login}}&InvoiceID={{$invoiceId}}&Description={{$description}}&SignatureValue={{$signature_value}}&IsTest={{$isTest}}&Shp_user={{$shpUser}}">
        </script>
    </div>
    <div class="col-md-4 ml-4 marginResize">
        <button type="button" style="padding: 15px;" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#qrModal-{{$invoiceId}}">
            Оплата через QR
        </button>
    </div>
</div>

<!-- Модальное окно для QR-кода -->
<div class="modal fade" id="qrModal-{{$invoiceId}}" tabindex="-1"
     aria-labelledby="qrModalLabel-{{$invoiceId}}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel-{{$invoiceId}}">QR-код для оплаты</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center">
                @php
                    $merchant_login_qr = "APPP";
                    $password_1_qr = "LFuWhwWF2H63Uaf3Wwz1";
                    $outSum_qr = 1; // или другая сумма по умолчанию для QR
                    $shpUser_qr = auth()->user()->id;
                    // Для Index.aspx сумма должна быть в подписи.
                    $signature_value_qr = md5("$merchant_login_qr:$outSum_qr:$invoiceId:$password_1_qr:Shp_user=$shpUser_qr");
                    $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$merchant_login_qr&OutSum=$outSum_qr&InvoiceID=$invoiceId&SignatureValue=$signature_value_qr&Shp_user=$shpUser_qr";
                    $qrCode = SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url);
                @endphp
                <a href="{{$url}}" target="_blank">{!! $qrCode !!}</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-payment-form iframe {
        min-height: 150px !important;
        width: 100%; /* Iframe будет занимать всю ширину контейнера */
    }
    .marginResize {
        margin-left: 0;
    }
    .iframe-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .iframe-container {
            flex-direction: row;
            align-items: flex-start;
        }
        .marginResize {
            margin-left: 20px;
        }
    }
</style> 