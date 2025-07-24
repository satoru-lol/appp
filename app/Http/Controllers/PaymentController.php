<?php

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
