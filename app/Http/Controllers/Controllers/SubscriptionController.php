<?php

namespace app\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        return view('subscriptions.index');
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        /*$subscription->level = !$subscription->current_subscription;
        $subscription->save();*/

        return response()->json($subscription);
    }

    public function confirmPurchase(Request $request)
    {
        // Здесь можно добавить логику для выполнения покупки подписки
        // Например, запись в логи или обновление состояния

        return response()->json(['message' => 'Подписка подтверждена']);
    }

    public function cancel(Request $request)
    {
        // Логика для отмены подписки
        // Например, отправка кода подтверждения на телефон

        return response()->json(['message' => 'Подписка отменена']);
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 400);
        }

        $productSlug = $request->input('product');
        // Логика покупки подписки

        return response()->json(['success' => true]);
    }

    public function verifyCode(Request $request)
    {
        /*$validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'product' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 400);
        }*/
/*
        $verificationCode = $request->input('code');
        $productSlug = $request->input('product');*/
        // Логика проверки кода и понижения уровня подписки
        $code = rand(100000, 999999);

        $user = User::where("id", auth()->user()->id)->first();
        $user->verification_code = $code;
        $user->action = $request->get("action");

        $status = $this->sendSms($user->phone, $code, 1);
        $user->save();

        return response()->json(['success' => $status]);
    }

    public function verifyCodeSuccess(Request $request)
    {
        //$productSlug = $request->input('product');
        $user = User::where("id", auth()->user()->id)->first();
        $action = $user->action;
        if (!empty($user->verification_code) && $action) {
            if ($request->get("code") == $user->verification_code) {
                if ($action == "cancel") {
                    $subs = Subscription::where("user_id", $user->id)->first();
                    $subs->level = "0";
                    $subs->save();
                }
                $user->verification_code = null;
                $user->action = "";
                $user->save();
                return true;
            }
            return false;
        }
        return false;
    }

    private function sendSms(string $phone, string $code, $type)
    {
        $smsAeroMessage = new \SmsAero\SmsAeroMessage(config('settings.smsaero_email'), config('settings.smsaero_apikey'));

        $response = $smsAeroMessage->send(['number' => $phone, 'text' => 'Ваш код подтверждения: '.$code, 'sign' => config('settings.smsaero_sign')]);

        return (bool)$response['success'];
    }
}
