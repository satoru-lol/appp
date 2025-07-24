<?php

namespace App\Services\Payments;

interface PaymentGatewayInterface
{
    /**
     * Генерирует URL для перенаправления пользователя на страницу оплаты.
     *
     * @param string $transactionId Уникальный ID транзакции в нашей системе.
     * @param float $amount Сумма платежа.
     * @param string $description Описание платежа.
     * @param array $additionalParams Дополнительные параметры (например, email клиента, Shp-параметры).
     * @return string URL для оплаты.
     */
    public function generatePaymentUrl(string $transactionId, float $amount, string $description, array $additionalParams = []): string;

    /**
     * Проверяет подлинность уведомления (callback) от платежной системы.
     *
     * @param array $requestData Данные, полученные от платежной системы.
     * @return bool True, если уведомление подлинное, иначе false.
     */
    public function validatePaymentNotification(array $requestData): bool;

    /**
     * Обрабатывает успешное уведомление о платеже.
     *
     * @param array $requestData Данные, полученные от платежной системы.
     * @return bool True, если платеж успешно обработан, иначе false.
     */
    public function processSuccessfulPayment(array $requestData): bool;

    /**
     * Возвращает имя платежного шлюза.
     *
     * @return string
     */
    public function getName(): string;
} 