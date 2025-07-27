<div class="payment-section my-4">
    <h4 class="mb-3">Продление подписки</h4>

    @php
        // Получаем данные из конфига
        $merchantLogin = config('services.robokassa.login');
        $password_1 = config('services.robokassa.password_1');

        // Параметры платежа
        $invoiceId = $invID ?? 0; // Номер заказа (должен быть уникальным)
        $amount = "1.00"; // Сумма заказа
        $description = "Оплата подписки для пользователя " . (auth()->user()->email ?? auth()->id());
        $userId = auth()->id();

        // Формируем подпись
        // Формат: [Login]:[OutSum]:[InvId]:[Password#1]:shp_user=[Shp_user]
        $signatureValue = md5("{$merchantLogin}:{$amount}:{$invoiceId}:{$password_1}:shp_user={$userId}");
        
        // URL для оплаты
        $paymentUrl = "https://auth.robokassa.ru/Merchant/Index.aspx?" . http_build_query([
            'MerchantLogin' => $merchantLogin,
            'OutSum' => $amount,
            'InvoiceID' => $invoiceId,
            'Description' => $description,
            'SignatureValue' => $signatureValue,
            'shp_user' => $userId,
            'Culture' => 'ru',
            'isTest' => 0,
        ]);

        $qrCode = SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($paymentUrl);
    @endphp

    <div class="d-flex align-items-center gap-3">
        <!-- Кнопка оплаты -->
        <a href="{{ $paymentUrl }}" target="_blank" class="btn btn-lg btn-primary">
            <i class="fas fa-credit-card me-2"></i>
            Оплатить картой
        </a>

        <!-- Кнопка для QR-кода -->
        <button type="button" class="btn btn-lg btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
            <i class="fas fa-qrcode me-2"></i>
            Показать QR-код
        </button>
    </div>
    <p class="text-muted mt-2">
        Нажимая на кнопку, вы будете перенаправлены на защищенную страницу оплаты Robokassa.
    </p>
</div>

<!-- Модальное окно для QR-кода -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Оплата по QR-коду</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Отсканируйте QR-код вашим банковским приложением для быстрой оплаты.</p>
                <div class="my-3">
                    {!! $qrCode !!}
                </div>
                <a href="{{ $paymentUrl }}" target="_blank">Или перейдите по ссылке</a>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-section {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e3e3e3;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
        font-weight: 500;
    }
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }
</style> 