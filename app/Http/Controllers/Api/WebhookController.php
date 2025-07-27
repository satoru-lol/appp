<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\RobokassaGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $robokassaGateway;

    public function __construct(RobokassaGateway $robokassaGateway)
    {
        $this->robokassaGateway = $robokassaGateway;
    }

    /**
     * Обрабатывает уведомления (Result URL) от Robokassa.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleRobokassa(Request $request)
    {
        $data = $request->all();
        $invoiceId = $data['InvId'] ?? 'N/A';
        Log::info('Robokassa webhook received.', $data);

        // 1. Валидация подписи
        if (!$this->robokassaGateway->validatePaymentNotification($data)) {
            Log::error('Robokassa webhook: Invalid signature.', [
                'invoice_id' => $invoiceId
            ]);
            // Робокасса не требует определенного ответа при неверной подписи,
            // но мы вернем ошибку для нашей внутренней диагностики.
            return response('Invalid signature.', 400);
        }
        
        // 2. Обработка платежа
        $isProcessed = $this->robokassaGateway->processSuccessfulPayment($data);

        if (!$isProcessed) {
            Log::error('Robokassa webhook: Payment processing failed.', [
                'invoice_id' => $invoiceId
            ]);
            // Если что-то пошло не так при обработке, логируем, но Робокассе все равно нужно отдать "OK"
            // чтобы она не пыталась отправить уведомление повторно.
        } else {
             Log::info('Robokassa webhook: Payment processed successfully.', ['invoice_id' => $invoiceId]);
        }

        // 3. Отправка успешного ответа Robokassa
        // Робокасса требует ответ "OK" + номер счета.
        return response('OK' . $invoiceId, 200);
    }
} 