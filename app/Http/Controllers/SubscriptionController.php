<?php

/**
 * @deprecated Этот контроллер устарел. Функционал подписок перенесен в рефакторенную архитектуру
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Функционал подписок теперь находится в:
 * - app/Services/V2/ProfileService.php (управление подписками)
 * - app/Services/SubscriptionService.php (логика подписок)
 * - app/Repositories/V2/UserRepository.php (данные подписок)
 * 
 * Миграция:
 * - Старые маршруты: /subscription/*
 * - Новые маршруты: /v2/refactored/profile/* (управление подписками)
 * 
 * @see App\Http\Controllers\V2\Refactored\ProfileController для управления подписками
 * @see App\Services\V2\ProfileService для логики подписок
 */

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Transactions;
use App\Models\User;
use App\Models\SubscriptionPays;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Carbon\Carbon; 

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

    public function authPay($id, $userId, $auto)
    {
    
        $product = Product::where('id', $id)->first();
        $subscription = Subscription::where('user_id', $userId)->first();
        $userBalance = User::where('id',$userId)->first();
        
        $temp = Product::where('level',$subscription->level)->first();
        
        $priceToPay = $product->price;
        $lessPay = false;
        
       
        if($subscription->level == 6 && $product->level == 5 && $userBalance->balance >= ($product->price - $temp->price) && $temp){
            
              $priceToPay = $product->price - $temp->price;
              $lessPay = true;
            
        }
        
     
        if ($userBalance->balance >= $priceToPay){
            
            if($product->level == 1){
                $userBalance->used_sub = 1;
                $userBalance->save();
            }
            $subscription->level = $product->level;
             $subscription->auto = $auto;
             
             if(!$lessPay){
                     $subscription->expired_at = Carbon::now()->addDays(30);
             }
         
        
            $subscription->save();
            $userBalance->balance = $userBalance->balance - $priceToPay;
            $userBalance->save();
            
                 $invoiceID = Transactions::max('inv_id')+1;
           
              SubscriptionPays::create([
          'user_id' => $userBalance->id,
          'invoice_id' => $invoiceID,
          'action' => 'buy',
          'subscription_id' => $subscription->id,
            'active' => false,
            'product_id' => $product->id,
            'price' => $priceToPay,
            'auto' => $auto
        ]);
            return redirect()->back();
        }
        
      
        
  return redirect()->route('profile')->withErrors(['balance' => 'У вас недостаточно средств, пополните баланс']);
  
        // $merchantLogin = "APPP";
        // $invoiceID = Transactions::max('inv_id')+1;;
        // $price = $product->price;
        // $description = $product->name;

        // $paymentUrl = "https://auth.robokassa.ru/Merchant/Index.aspx";
        // $receipt = [
        //     'sno' => 'osn',
        //     'items' => [
        //         [
        //             'name' => $product->name,
        //             'quantity' => 1,
        //             'sum' => $price,
        //             'tax' => 'vat10',
        //             'payment_method' => 'full_payment',
        //             'payment_object' => 'commodity'
        //         ]
        //     ]
        // ];

        // $receiptJson = json_encode($receipt, JSON_UNESCAPED_UNICODE);
        // $receipt = urlencode($receiptJson);

        // $signatureValue = md5("$merchantLogin:$price:$invoiceID:$receipt:LFuWhwWF2H63Uaf3Wwz1");


        // SubscriptionPays::create([
        //   'user_id' => $userId,
        //   'invoice_id' => $invoiceID,
        //   'subscription_id' => $subscription->id,
        //     'active' => false,
        //     'product_id' => $product->id,
        // ]);

        // return view("redirect_to_payment", compact('merchantLogin', 'invoiceID', 'price', 'description', 'paymentUrl', 'receipt', 'signatureValue'));

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

    public function cancelSubscribe(Request $request){
        $userID = $request->user()->id;
        $subs = Subscription::where("user_id", $userID)->first();
        $product = Product::where('level', $subs->level)->first();
        if ($subs){
            $user = User::find($userID);
            $level = $subs->level;
            $subs->level = "0";
            $subs->expired_at = null;
            $subs->save();
              $invoiceID = Transactions::max('inv_id')+1;
            
                       SubscriptionPays::create([
          'user_id' => $userID,
          'invoice_id' => $invoiceID,
          'action' => 'cancel',
          'subscription_id' => $subs->id,
            'active' => false,
            'product_id' => $product ? $product->id : 0,
            'price' => null,
            'auto' => 0
        ]);
            // $user->balance = $user->balance + Product::where("level", $level)->first()->price;
            $user->save();
//            User::where("id", $userID)->first()->balance = User::where("id", $userID)->first()->balance + Product::where("level", $level)->first()->price;
        }
        return response()->json(['success' => true]);
    }

    public function changeToHigher(Request $request){
        $userID = $request->user()->id;
        $subs = Subscription::where("level", 5)->first();
        $productTemp = Product::where('level', 5)->first();
        $mySubs = Subscription::where("user_id", $userID)->first();
        
        $tempPrice = $subs->price - $mySubs->price;
      
        if ($subs && $mySubs && $request->user()->balance >=  $tempPrice ){
            $user = User::find($userID);
      
 
             $user->balance = $user->balance - $tempPrice;
             $user->save();
                       
             $mySubs->level = "5";
            //  $mySubs->expired_at = Carbon::now()->addDays(30);
             $mySubs->save();
             
                $invoiceID = Transactions::max('inv_id')+1;
                       SubscriptionPays::create([
          'user_id' => $userBalance->id,
          'invoice_id' => $invoiceID,
          'action' => 'buy',
          'subscription_id' => $mySubs->id,
            'active' => true,
            'product_id' => $productTemp->id,
            'price' => $tempPrice
        ]);
            
        }
        return redirect()->route('profile');
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
