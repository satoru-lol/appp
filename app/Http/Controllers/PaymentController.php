<?php

/**
 * @deprecated Этот контроллер устарел. Функционал оплаты интегрирован в рефакторенную архитектуру
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Функционал оплаты теперь находится в:
 * - app/Services/V2/AuthService.php (для авторизованных платежей)
 * - app/Services/PaymentService.php (основной сервис оплаты)
 * - app/Services/Payments/RobokassaGateway.php (шлюз оплаты)
 * 
 * Миграция:
 * - Старые маршруты: /pay/*
 * - Новые маршруты: интегрированы в /v2/refactored/profile/* и /v2/refactored/auth/*
 * 
 * @see App\Http\Controllers\V2\Refactored\ProfileController для оплаты подписок
 * @see App\Services\PaymentService для логики оплаты
 */

namespace App\Http\Controllers;
use App\Http\Service\YookassaService;
use Illuminate\Http\Request;

class PaymentController
{

    protected $yookassa;

    public function __construct(YookassaService $yookassa)
    {
        $this->yookassa = $yookassa;
    }

    public function createPayment(Request $request)
    {
        $amount = $request->input('amount');
        $description = 'Оплата заказа';
        $returnUrl = route('payment.success');

        $payment = $this->yookassa->createPayment($amount, $description, $returnUrl);

        if (isset($payment['confirmation']['confirmation_url'])) {
            return redirect($payment['confirmation']['confirmation_url']);
        }

        return back()->withErrors(['msg' => 'Ошибка создания платежа']);
    }

    public function success()
    {
        return view('payment.success');
    }

}
