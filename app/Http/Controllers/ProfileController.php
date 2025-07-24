<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\ProfileController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура профиля находится в app/Http/Controllers/V2/Refactored/ProfileController.php
 * 
 * Миграция:
 * - Старые маршруты: /profile/*
 * - Новые маршруты: /v2/refactored/profile/*
 * 
 * Новая архитектура включает:
 * - ProfileService для бизнес-логики
 * - UserRepository для работы с данными
 * - Form Requests для валидации
 * - Оптимизированные запросы к БД
 * 
 * @see App\Http\Controllers\V2\Refactored\ProfileController
 */

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Donat;
use App\Models\Introduction;
use App\Models\Product;
use App\Models\Roles;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
class ProfileController extends Controller
{

    public function userInfo($id)
    {
        $user = User::where("id", $id)->first();
        $info = [];
        if (!empty($user)) {
            $info = Introduction::where("email", $user->email)->first();
        }

        return \view("user.info", compact("info"));
    }

    public function cancelMonthPay(Request $request){
        $user = $request->user();
        if ($user->auto){
            $user->auto = false;
        }else{
            $user->auto = true;
        }
        $user->save();
        return redirect("/profile");
   }

    public function productGet(Request $request)
    {
        $csrf = csrf_token();
        $html = "";
        $product = Product::where("id", $request->get("productId"))->first();
        $productId = !empty($product->id) ? $product->id : "";
        $name = !empty($product->name) ? $product->name : "";
        $price = !empty($product->price) ? $product->price : "";
        $level = !empty($product->level) ? $product->level : "";
        $descr = !empty($product->description) ? $product->description : "";


        $html = "<form method='get' action='/product/edit'>
                        <input type='hidden' name='token' value='$csrf' autocomplete='off'>
                        <input type='hidden' name='product_id' value='$productId'>
                        <input type='hidden' name='level' value='$level'>
                                <div id='hidEl'>
                                </div>
                                <div class='form-group'>
                                    <div class='mb-6'>
                                        <label for='name'>Имя подписки</label><br>
                                        <input type='text' name='name' id='name' class='form-control' placeholder='Имя подписки' value='$name'>
                                        <label for='price'>Цена</label><br>
                                        <input type='text' name='price' id='price' class='form-control' placeholder='Цена' value='$price'>
                                        <label for='descr'>Описание</label><br>
                                        <textarea type='text' name='descr' id='descr' class='form-control' placeholder='Описание'>$descr</textarea>
                                    </div>
                                </div><br>
                                <button type='submit' class='btn btn-primary'>Сохранить</button>
                            </form>";

        return $html;

    }

    public function productEdit(Request $request)
    {
          $productId = $request->query->get("product_id");
              $request->validate([
            'name' => 'required',
            'price' => 'required',
            ]);
        
      
        $product = Product::where("id", $productId)->first();
        $product->name = $request->get("name");
        $product->price = $request->get("price");
        $product->description = $request->get("descr");
        $product->save();

        return back();
    }

    public function productCreate(Request $request)
    {
              $request->validate([
            'name' => 'required',
            'price' => 'required',
            ]);
            
        // Генерируем уникальный уровень автоматически
        $maxLevel = Product::max('level') + 1;
            
        $product = Product::create([
            "name" => $request->get("name"),
            "price" => $request->get("price"),
            "level" => $maxLevel, // Автоматически присваиваем уникальный уровень
            "description" => $request->get("descr")
        ]);

        return redirect("/profile");

    }

    public function index(): View
    {
        $clubs = null;
        $subscriptionTxt = "Нет подписки";
        $user = auth()->user();
        $users = User::where("id", "!=", $user->id)->paginate(5);
        $transactions = Transactions::where("user_id", auth()->user()->id)->orderBy('created_at','desc')->get();
        $int = Introduction::where("email", $user->email)->first();
        $subscription = Subscription::where("user_id", $user->id)->first();
        
        // Восстанавливаем подписку из истории платежей, если она сбилась
        if ($subscription && $subscription->level == 0) {
            $lastPayment = SubscriptionPays::where('user_id', $user->id)
                ->where('action', 'buy')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($lastPayment) {
                $product = Product::find($lastPayment->product_id);
                if ($product) {
                    $subscription->level = $product->level;
                    $subscription->is_active = 1;
                    $subscription->save();
                }
            }
        }
        
        // Выборка клубов по уровню подписки
        if ($subscription && $subscription->level != 0) {
            $clubs = \App\Models\Club::where('product_level', $subscription->level)->get();
        } else {
            $clubs = collect();
        }
        
    $sub_pays = SubscriptionPays::where("user_id", $user->id)->get();
$mergedData = $transactions->merge($sub_pays)->sortByDesc('created_at');

// Manual pagination
$perPage = 5;
$currentPage = LengthAwarePaginator::resolveCurrentPage();
$currentItems = $mergedData->slice(($currentPage - 1) * $perPage, $perPage)->values(); // .values() to reset keys

$mergedDataPaginated = new LengthAwarePaginator(
    $currentItems,
    $mergedData->count(),
    $perPage,
    $currentPage,
    ['path' => request()->url(), 'query' => request()->query()]
);

    // $perPage = 5; 
    // $currentPage = LengthAwarePaginator::resolveCurrentPage(); 
    // $currentResults = $mergedData->slice(($currentPage - 1) * $perPage, $perPage)->all();
    // $mergedDataPaginated = new LengthAwarePaginator(
    //     $currentResults, 
    //     $mergedData->count(), 
    //     $perPage, 
    //     $currentPage,
    //     ['path' => LengthAwarePaginator::resolveCurrentPath()] 
    // );

        $premiumSub = Product::where("level", 5)->first(); 
        $final_pr = null;
        $prem_pr = null;
        $low_pr = null;
        
        
        
        if($premiumSub && $subscription->level == 6){
            $prem_pr = $premiumSub->price;
         
            $low_probj = Product::where("level", 6)->first(); 
            $low_pr = $low_probj->price;
            if($low_pr && $prem_pr >= $low_pr){
                  $final_pr = $prem_pr - $low_pr; 
            }
            
        }
    //     $subscription = Subscription::where('user_id', $user->id)
    // ->orderByDesc('id') 
    // ->first();
        $products = Product::where('visible','=',1)->get();

        $invID = Transactions::max('inv_id')+1;


        if (!empty($subscription)) {
            if ($subscription->level != "0") {
                $product = null;
                if (isset($subscription->product_id) && $subscription->product_id) {
                    $product = Product::find($subscription->product_id);
                }
                if (!$product) {
                    $product = Product::where('level', $subscription->level)->first();
                }
                if ($product) {
                    $subscriptionTxt = $product->name . " — " . ($product->price ?? '0') . " руб.";
                } else {
                    $subscriptionTxt = "Нет подписки";
                }
            }
        }

        foreach ($users as &$item) {
            if (!empty($item->group) && $item->group != "user") {
                $item["role"] = Roles::where("slug", $item->group)->first()->name;
            } else {
                $item["role"] = "Пользователь";
            }
        }
        if ($user->group == "user") {
            $authedRole = "Пользователь";
        } else {
            $authedRole = Roles::where("slug", $user->group)->first()->name;
        }

        $roles = Roles::all();
        
        // Фильтруем продукты, чтобы скрыть переходную подписку (level 7)
        $products = $products->filter(function($product) {
            return $product->level != 7;
        });

        foreach ($products as &$product) {
            if ($subscription->level != 0) {
                if ($product->level == $subscription->level) {
                    $product["current_subscription"] = true;
                } else {
                    $product["current_subscription"] = false;
                }
                
                // Деактивируем кнопки для базовой и премиум подписок
                if ($product->level == 6 || $product->level == 59) {
                    $product["disabled"] = true;
                }
            }
        }

        if ($subscription->level == 0) {
            $products[] = (object)[
                "slug" => "0",
                "name" => "Без подписки",
                "level" => -1,
                "no" => true,
                "current_subscription" => true
            ];
        }
        //dd($products);
        $currentSubscriptionLevel = $subscription->level;

        // Отладка: вывести все продукты с id и level
        $allProducts = Product::all(['id', 'level', 'name', 'price']);
        // dd($allProducts->toArray());

        return view('user.index', compact('user', 'mergedData','mergedDataPaginated', 'clubs','transactions', 'int', 'users', 'roles', 'authedRole', 'subscriptionTxt', 'products', 'currentSubscriptionLevel', 'subscription','invID','final_pr','prem_pr','low_pr'));


   }



    public function editRole(Request $request)
    {
        if (!empty($userID = $request->input("user_id")) && !empty($newRole = $request->get("selected_role"))) {
            $user = User::where("id", $userID)->first();
            if (!empty($user)) {
                $user->group = $newRole;
                $user->save();
                return redirect("/profile");
            }
        }
        return redirect("/profile");
    }

    public function editPerms($settings, Request $request)
    {
        $userId = "";
        $data = json_decode($settings, true);
        if (!empty($data["checkedValues"])) {
            $userId = $data["checkedValues"]["userId"];
            unset($data["checkedValues"]["userId"]);
            $perms["add"] = $data["checkedValues"];

            if (!empty($userId)) {
                $user = User::where("id", $userId)->first();
                $user->permissions = json_encode($perms);
                $user->save();
                return back();
            }
        }

        return back();

    }

    public function getPerms(Request $request)
    {
        $user = User::where("id", $request->get("userId"))->first();

        $html = "<h6>Разрешения на добавление</h6>";
        $user->permissions = json_decode($user->permissions, true);
        if (!empty($user->permissions)) {
            foreach ($user->permissions["add"] as $key => $perm) {
                $status = $perm == false ? 'off' : 'on';
                $checked = $perm == false ? '' : 'checked';
                $txt= "";
                if($key == 'forum') {
                    $txt = "Форум";
                } else if($key == 'course') {
                    $txt = "Курсы";
                } else if($key == 'club') {
                    $txt = "Клубы";
                } else if($key == 'blog') {
                    $txt = "Блоги";
                } else if($key == 'polygon') {
                    $txt = "Полигон";
                } else if($key == 'reg') {
                    $txt = "Регулярные Мероприятия";
                }

                $html .= "<div class='form-check'>
                        <input data-key='$key' data-perm='$status' class='form-check-input' $checked type='checkbox' id='{{$key}}'>
                        <label class='form-check-label' for='{{$key}}'>$txt</label>
                    </div>";
            }
        }


        return $html;

    }

    public function userAdmin(Request $request)
    {


        $telNum = $request->input("tel_num");
        if (!empty($telNum)) {
            $user = User::where("phone", str_replace(" ", "", $telNum))->first();
            if (!empty($user)) {
                $user->group = "admin";
                $user->save();
            }
            return back();
        }
        return back();
    }

    public function paymentSuccess(Request $request)
    {
        //Log::channel("test")->info($request);
        return redirect("/profile")->with('pay_success', __('Оплата прошла успешно'));
        /*if (!empty($request->get("InvId"))) {
            $subscriptionPay = SubscriptionPays::where("invoice_id", $request->get("InvId"))->first();


            if (!empty($subscriptionPay->id)) {


                $user = User::where("id", $subscriptionPay->user_id)->first();




                $subscription = Subscription::where('user_id', $user->id)->first();
                $product = Product::where("id", $subscriptionPay->product_id)->first();

                $today = Carbon::now();
                $nextMonth = $today->addMonth();
                $nextMonthFormatted = $nextMonth->format('Y-m-d H:i:s');


                $subscription->expired_at = $nextMonthFormatted;
                $subscription->level = $product->level;
                $subscription->save();

                $subscriptionPay->invoice_id = $request->get("InvId");
                $subscriptionPay->product_id = $product->id;
                $subscriptionPay->subscription_id = $subscription->id;
                $subscriptionPay->user_id = $user->id;
                $subscriptionPay->save();

				Transactions::create([
                        "shop" => "RoboKassa",
                        "op_key" => "",
                        "inv_id" => $request->get("InvId"),
                        "sum" => $product->price,
                        "state" => "success",
                        'user_id' => $user->id,
                        'accepted_perms' => null,
                        'product_id' => $subscriptionPay->product_id
                    ]);

                Auth::login($user, true);

               return redirect("/profile")->with('pay_success', __('Покупка подписки успешна'));


            } else {
                $user = User::where("id", $request->get("InvId"))->first();
                $user->balance = $user->balance + $request->get("OutSum");
                $user->save();

            }

            $transactions = Transactions::create([
                "shop" => "RoboKassa",
                "op_key" => $request->get("opKey"),
                "inv_id" => $request->get("InvId"),
                "sum" => $request->get("OutSum"),
                "state" => "success",
                'user_id' => 0
            ]);

            return redirect("/profile")->with('pay_success', __('Оплата прошла успешно'));
        }*/

//        return redirect("/profile")->with('pay_success', __('Ошибка оплаты'));

    }

    public function buy(Request $request)
    {
        Log::info('request',$request->all());
        if (!empty($request->get("product_id"))) {
            $product = Product::where("id", $request->get("product_id"))->first();
            $user = auth()->user();

            if (!empty($product->price)) {
                if ($user->balance >= $product->first_price) {
                    $subscription = Subscription::where('user_id', $user->id)->first();

                    $user->balance = $user->balance - $product->first_price;
                    $user->save();


                    $today = Carbon::now();
                    $nextMonth = $today->addMonth();
                    $nextMonthFormatted = $nextMonth->format('Y-m-d H:i:s');

                    $subscription->expired_at = $nextMonthFormatted;
                    $subscription->level = $product->level;
                    $subscription->save();

                    $inv = rand(100000, 999999);

                    SubscriptionPays::create([
                        "user_id" => auth()->user()->id,
                        "subscription_id" => $subscription->id,
                        "invoice_id" => $inv,
                        "active" => false,
                        "product_id" => $product->id
                    ]);

                    Transactions::create([
                        "shop" => "VseCatalogi",
                        "op_key" => "",
                        "inv_id" => $inv,
                        "sum" => $product->first_price,
                        "state" => "success",
                        'user_id' => auth()->user()->id,
                        'conf_url' => "",
                        'target' => "Покупка подписки (" . $product->name . ")"
                    ]);

                    return back();
                } else {
                    return back()->withErrors("Пополните баланс");
                }
            }
        }
    }

    public function paymentfail(Request $request)
    {
        $transactions = Transactions::create([
            "shop" => /*$request->get("shop")*/"RoboKassa",
            "op_key" => $request->get("opKey"),
            "inv_id" => $request->get("InvId"),
            "sum" => $request->get("OutSum"),
            "state" => "failed",
            'user_id' => 0
        ]);

        return redirect("/profile")->with('pay_error', __('Произошла ошибка на стороне платежной системы'));

    }

    public function webhookRoboKassa(Request $request)
    {
        // Log::channel("test")->info($request);

        Log::info('request_pay_'.date('d.m.Y'),$request->all());

         $transaction = Transactions::where('inv_id', $request->get('InvId'))->where('signature_value', $request->get('SignatureValue'))->first();
        if ($transaction) {
            return response()->json(['success' => false]);
        }
        $subPays = SubscriptionPays::where('invoice_id', $request->get('InvId'))->first();
               $subPays = null;
        //   $productSub = Product::where('id', $subPays->product_id)->first();
        if ($subPays) {
         
            // Subscription::where('user_id', $subPays->user_id)->update(['level' => $productSub->level]);
            Transactions::create([
                'shop' => 'Robokassa',
                'inv_id' => $request->get('InvId'),
                'user_id' => $subPays->user_id,
                'signature_value' => $request->get('SignatureValue'),
                'sum' => $request->get('out_summ'),
                'state' => 'success',
            ]);
            $subPays->active = 1;
            $subPays->save();
            
      
        } else {
            Transactions::create([
                'shop' => 'Robokassa',
                'inv_id' => $request->get('InvId'),
                'signature_value' => $request->get('SignatureValue'),
                'sum' => $request->get('out_summ'),
                'user_id' => $request->get('Shp_user'),
                'state' => 'success',
            ]);

        }
        
             $user = User::where('id',$request->get('Shp_user'))->first();
            $user->balance = $user->balance+$request->get('out_summ');
            $user->save();
        return response()->json(['success' => true]);


    }

    public function save(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required'
        ]);

        $user = $request->user();

        User::find($user->id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
        ]);

        return back()->with('success', __('Данные успешно обновлены'));
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'image.required' => 'Пожалуйста, выберите изображение.',
            'image.image' => 'Файл должен быть изображением.',
            'image.mimes' => 'Разрешены только файлы форматов: JPEG, PNG, JPG.',
            'image.max' => 'Не удалось загрузить изображение. Максимальный размер 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $user = $request->user();

        $files = glob(public_path('img/avatars/').md5($user->id.$user->phone).'.*');
/*
        foreach($files as $file){
            //echo $file;
			//unlink($file);
		}*/

        $imageName = md5($user->id.$user->phone).'.'.$request->image->extension();
        $request->image->move(public_path('img/avatars'), $imageName);

        return back()->with('success', __('Фотография успешно обновлена'));
    }

    public function remove(Request $request)
    {

        $user = $request->user();

        $filePath = public_path('img/avatars/' . md5($request->user()->id.$request->user()->phone).'.*');
        $files = glob($filePath);
        if (!empty($files)) {
            foreach ($files as $file) {
                if (File::exists($file)) {
                    File::delete($file); // Delete the file
                }
            }

//            return response()->json(['success' => 'Avatar deleted successfully']);
        }
        /*$imageName = md5($user->id.$user->phone).'.'.$request->image->extension();
        $request->image->move(public_path('img/avatars'), $imageName);*/

        return back()->with('success', __('Фотография успешно удален!'));
    }

    // Списывает с баланса юзера сумму пожертвования
    public function donat(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'club_id' => 'nullable|numeric',
            'course_id' => 'nullable|numeric',
        ]);
        Log::info('data',$data);
        $user = User::find(auth()->user()->id);

        if ($user->balance >= $data['amount']) {
            $balance = $user->balance - $data['amount'];
            $user->balance = $balance;
            $user->save();
            Donat::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'reason' => $data['reason'],
                'club_id' => $data['club_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
            ]);
            return response()->json(['success' => true, 'newBalance' => $balance]);
        } else {
            return response()->json(['success' => false, 'message' => 'Недостаточно средств. Пополните баланс!']);
        }
    }

    public function pay(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'id' => 'required|exists:products,id',
        ]);

        $user = User::find(auth()->user()->id);
        $subscribe = Subscription::where('user_id', $user->id)->first();
        $product = Product::find($data['id']);

        if ($user->balance >= $data['amount']) {
            $balance = $user->balance - $data['amount'];
            $user->balance = $balance;
            $subscribe->level = $product->level;

            $subscribe->save();
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Оплата успешна! Ваш новый баланс: ' . $balance . ' руб.',
                'balance' => $balance
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Недостаточно средств. Пополните баланс!'
            ]);
        }
    }
}
