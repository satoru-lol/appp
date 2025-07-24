<?php

namespace app\Http\Controllers;

use App\Models\Introduction;
use App\Models\Product;
use App\Models\Roles;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon;

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
        $product = Product::where("id", $productId)->first();
        $product->name = $request->get("name");
        $product->price = $request->get("price");
        $product->description = $request->get("descr");
        $product->save();

        return back();
    }

    public function productCreate(Request $request)
    {
        $product = Product::create([
            "name" => $request->get("name"),
            "price" => $request->get("price"),
            "level" => $request->get("level"),
            "description" => $request->get("descr")
        ]);

        return redirect("/profile");

    }

    public function index(): View
    {
        $subscriptionTxt = "";
        $user = auth()->user();
        $users = User::where("id", "!=", $user->id)->get();
        $transactions = Transactions::where("inv_id", auth()->user()->id)->get();
        $int = Introduction::where("email", $user->email)->first();
        $subscription = Subscription::where("user_id", $user->id)->first();
        $products = Product::all();

        if (!empty($subscription)) {
            if ($subscription->level == "0") {
                $subscriptionTxt = "Нет подписки";
            } else {
                $product = Product::whereNotNull("level", $subscription->level)->first();
                $subscriptionTxt = $product->name . " подписка";
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

        foreach ($products as &$product) {
            if ($subscription->level != 0) {
                if ($product->level == $subscription->level) {
                    $product["current_subscription"] = true;
                } else {
                    $product["current_subscription"] = false;
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

        $currentSubscriptionLevel = $subscription->level;

        return view('user.index', compact('user', 'transactions', 'int', 'users', 'roles', 'authedRole', 'subscriptionTxt', 'products', 'currentSubscriptionLevel', 'subscription'));
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
                $txt = "";
                if ($key == 'forum') {
                    $txt = "Форум";
                } else if ($key == 'course') {
                    $txt = "Курсы";
                } else if ($key == 'club') {
                    $txt = "Клубы";
                } else if ($key == 'blog') {
                    $txt = "Блоги";
                } else if ($key == 'polygon') {
                    $txt = "Полигон";
                } else if ($key == 'reg') {
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
        $subscribePay = SubscriptionPays::where("invoice_id", $request->get("InvId"))->first();
        $product = Product::where("price", (int)$request->get("OutSum"))->first();

        if (!empty($subscribePay->id)) {
            if (!empty($product->id)) {
                $user = User::where("id", $subscribePay->user_id)->first();
                if (!empty($product->price)) {

                    $subscription = Subscription::where('user_id', $user->id)->first();

                    $today = Carbon::now();
                    $nextMonth = $today->addMonth();
                    $nextMonthFormatted = $nextMonth->format('Y-m-d H:i:s');

                    $subscription->expired_at = $nextMonthFormatted;
                    $subscription->level = $product->level;
                    $subscription->save();

                    $subscribePay->user_id = $user->id;
                    $subscribePay->product_id = $product->id;
                    $subscribePay->save();

                    return redirect("/profile")->with('pay_success', __('Покупка подписки прошла успешно'));

                }
            }

        } else {
            $user = User::where("id", $request->get("InvId"))->first();
            $user->balance = $user->balance + $request->get("OutSum");
            $user->save();
        }


        if (!empty($user->id)) {
            Transactions::create([
                "shop" => /*$request->get("shop")*/ "RoboKassa",
                "op_key" => $request->get("opKey"),
                "inv_id" => $request->get("InvId"),
                "sum" => $request->get("OutSum"),
                "state" => "success",
                'user_id' => $user->id
            ]);
        }

        return redirect("/profile")->with('pay_success', __('Оплата прошла успешно'));
    }

    public function paymentfail(Request $request)
    {
        $transactions = Transactions::create([
            "shop" => /*$request->get("shop")*/ "RoboKassa",
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
//        Log::channel("test")->info($request);

        Log::info('request_pay_'.date('d.m.Y'),$request->all());

        $transaction = Transactions::where('inv_id', $request->get('InvId'))->where('signature_value', $request->get('SignatureValue'))->first();
        if ($transaction) {
            return response()->json(['success' => false]);
        }
        $subPays = SubscriptionPays::where('invoice_id', $request->get('InvId'))->first();
  
        if ($subPays) {
 
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
                'user_id' => $request->get('InvId'),
                'state' => 'success',
            ]);
        
        }
        
            $user = User::where('id',$request->get('Shp_user'))->first();
            $user->balance = $user->balance+$request->get('out_summ');
            $user->save();
        return response()->json(['success' => true]);

//        $transactions = Transactions::create([
//            "shop" => /*$request->get("shop")*/"RoboKassa",
//            "op_key" => $request->get("opKey"),
//            "inv_id" => $request->get("InvId"),
//            "sum" => $request->get("IncSum"),
//            "state" => $request->get("state") ?? "failed",
//            'user_id' => 0
//        ]);


    }

    public function save(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        $user = $request->user();

        User::find($user->id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname
        ]);

        return back()->with('success', __('Данные успешно обновлены'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        $files = glob(public_path('img/avatars/') . md5($user->id . $user->phone) . '.*');

        foreach ($files as $file) {
            //echo $file;
            //unlink($file);
        }

        $imageName = md5($user->id . $user->phone) . '.' . $request->image->extension();
        $request->image->move(public_path('img/avatars'), $imageName);

        return back()->with('success', __('Фотография успешно обновлена'));
    }

    // Списывает с баланса юзера сумму пожертвования
    public function donat(Request $request)
    {
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);
        $user = User::find(auth()->user()->id);

        if ($user->balance >= $data['price']) {
            $balance = $user->balance - $data['price'];
            $user->balance = $balance;
            $user->save();
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
