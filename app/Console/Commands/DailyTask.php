<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Models\Transactions;
use http\Client\Curl\User;
use Illuminate\Console\Command;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Log;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
class DailyTask extends Command
{
    const SHOP_ID = "APPP";
    const SHOP_PASS = "LFuWhwWF2H63Uaf3Wwz1";

    protected $signature = 'task:daily';
    protected $description = 'Run daily task';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('DailyTask started at ' . now());

        $subscriptionPays = SubscriptionPays::all();
        $shopPass = self::SHOP_PASS;

        foreach ($subscriptionPays as $subscriptionPay) {
            $user = \App\Models\User::where("id", $subscriptionPay->user_id)->first();
            if (empty($user->id)) continue;
            $subscriptionPay = SubscriptionPays::where("user_id", $user->id)->first();
            $subscription = Subscription::where("id", $subscriptionPay->subscription_id)->first();
            $product = Product::where("level", $subscription->level)->first();
            if ($subscriptionPay->active == true) {
                if ($subscription->expired_at && Carbon::parse($subscription->expired_at)->isBefore(Carbon::now())) {
                 /*   if ($subscription->level == 1) {
                        $product = Product::where("level", 1)->first();
                        $subscriptionPay->level = $product->level;
                        $subscriptionPay->save();

                        $subscription->test_period = false;
                    }*/

                    $newInvoiceID = rand(1000000, 999999999);
                    $merchantLogin = self::SHOP_ID;
                    $invoiceID = $subscriptionPay->invoice_id;
                    $price = $product->price;
                    $description = $product->name;

                    if (!empty($product->first_week_price) && $subscription->test_period) {
                        $price = $product->first_week_price;
                    }

                    $signatureValue = md5("$merchantLogin:$price:$newInvoiceID:$shopPass");

                    $client = new Client();

                    $formParams = [
                        'MerchantLogin' => self::SHOP_ID,
                        'InvoiceID' => $newInvoiceID,
                        'PreviousInvoiceID' => $invoiceID,
                        'Description' => $description,
                        'SignatureValue' => $signatureValue,
                        'OutSum' => $price,
                    ];

                    try {
                        $response = $client->post('https://auth.robokassa.ru/Merchant/Recurring', [
                            'form_params' => $formParams,
                        ]);

                        $responseBody = $response->getBody()->getContents();
                        //$this->logMessage(json_encode($responseBody));

                        if (str_contains($responseBody, 'ERROR')) {
                            $subscription->level = "0";
                            $subscription->expired_at = null;
                            $subscription->save();
                        } else {
                            $today = Carbon::now();
                            $nextMonth = $today->addMonth();
                            $nextMonthFormatted = $nextMonth->format('Y-m-d H:i:s');

                            $subscription->level = $product->level;
                            $subscription->expired_at = $nextMonthFormatted;
                            $subscription->test = $responseBody;
                            $subscription->save();

                            $subscriptionPay->active = true;
                            $subscriptionPay->save();

                            $transaction = Transactions::where("inv_id", $newInvoiceID)->first();
                            if (empty($transaction->inv_id)) {
                                Transactions::create([
                                    "shop" => /*$request->get("shop")*/"RoboKassa",
                                    "op_key" => "",
                                    "inv_id" => $newInvoiceID,
                                    "sum" => $price,
                                    "state" => "success",
                                    'user_id' => $subscription->user_id,
                                    'product_id' => $product->id,
                                    'accepted_perms' => true
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            } else {
                if ($subscription->expired_at && Carbon::parse($subscription->expired_at)->isBefore(Carbon::now())) {
                    $subscription->level = "0";
                    $subscription->expired_at = null;
                    $subscription->save();
                }
            }
        }
    }

    function logMessage($message) {
        // Устанавливаем путь к файлу лога
        $logFile = "log.txt";

        // Получаем текущую дату и время
        $currentDateTime = date('Y-m-d H:i:s');

        // Формируем строку для записи в лог
        $logEntry = "[$currentDateTime] $message" . PHP_EOL;

        // Открываем файл для записи (режим "a" означает, что данные будут дописываться в конец файла)
        $fileHandle = fopen($logFile, 'a');

        if ($fileHandle) {
            // Записываем сообщение в файл
            fwrite($fileHandle, $logEntry);

            // Закрываем файл
            fclose($fileHandle);
        } else {
            // Если не удалось открыть файл, выводим ошибку
            echo "Could not open log file for writing.";
        }
    }
}
