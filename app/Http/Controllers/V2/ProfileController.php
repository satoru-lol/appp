<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Product;
use App\Models\ProductPermission;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Models\Transactions;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\Payments\RobokassaGateway;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ParticipantActions;
use App\Models\Blog;

class ProfileController extends Controller
{

    protected $subscriptionService;
    protected $paymentService;
    protected $robokassaGateway;

    public function __construct(
        SubscriptionService $subscriptionService,
        PaymentService $paymentService,
        RobokassaGateway $robokassaGateway
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->paymentService = $paymentService;
        $this->robokassaGateway = $robokassaGateway;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $activeTab = $request->query('tab', 'profile'); // По умолчанию 'profile'
        
        // --- Получаем статус подписки через сервис ---
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);
        $subscription = $subscriptionStatus->subscription;

        if (!$subscription) {
            // Если у пользователя нет подписки, создаем пробную (уровень 1) на 7 дней
            $trialLevel = config('subscriptions.products.trial.level', 1);
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'level' => $trialLevel,
                'auto' => 0,
                'is_active' => 1,
                'expired_at' => Carbon::now()->addDays(7),
            ]);
            // Обновляем статус, так как мы только что создали подписку
            $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);
        }
        
        $expiredAt = $subscription->expired_at ? Carbon::parse($subscription->expired_at)->startOfDay() : null;
        $daysLeft = $expiredAt && $subscriptionStatus->isActive ? Carbon::now()->startOfDay()->diffInDays($expiredAt, false) : 0;
        
        // --- Получение доступных курсов и клубов ---
        $productPermission = ProductPermission::where('product_id', $subscription->level)->first();

        $courseContents = collect();
        if ($subscriptionStatus->isActive && $productPermission && $productPermission->course) {
            $courseContents = \App\Models\CourseContent::with('course')->get();
        }
        
        // 4. Получаем клубы (логика остается прежней)
        $clubs = collect();
        if ($subscriptionStatus->isActive && $subscriptionStatus->hasClubAccess) {
            $clubs = \App\Models\Club::with('clubDates')->orderBy('id', 'desc')->get();
        }
        
        // --- Сначала определяем название ТЕКУЩЕЙ подписки ---
        // Для этого нам нужны ВСЕ продукты, а не отфильтрованные.
        $allVisibleProducts = Product::where('visible', true)->get();
        $currentSubscriptionProduct = $allVisibleProducts->firstWhere('level', $subscription->level);
        $subscriptionTxt = $currentSubscriptionProduct->name ?? 'Нет подписки';

        // --- Теперь готовим список ДОСТУПНЫХ для выбора подписок ---
        $transitionalLevel = config('subscriptions.products.transitional.level', 8);
        $trialLevel = config('subscriptions.products.trial.level', 1);

        $products = $allVisibleProducts
            ->whereNotIn('level', [$transitionalLevel, $trialLevel])
            ->map(function ($product) use ($subscription) {
                $product->current_subscription = ($product->level == $subscription->level);
                return $product;
            });

        $currentSubscriptionLevel = $subscription->level;

        $transactions = Transactions::where('user_id', $user->id)->latest()->get();
        $subscriptionPays = SubscriptionPays::where('user_id', $user->id)->with('product')->latest()->get();
        $mergedData = $transactions->concat($subscriptionPays)->sortByDesc('created_at');
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page', 1);
        $perPage = 5;
        $mergedDataPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $mergedData->forPage($page, $perPage),
            $mergedData->count(),
            $perPage,
            $page,
            ['path' => route('v2.profile.transactions')]
        );
        
        // $clubs = \App\Models\Club::all(); // Старая логика, больше не нужна
        $invID = time() . '_' . $user->id;

        $participantActions = ParticipantActions::where("user_id", $user->id)
            ->where("object_name", "meeting")
            ->pluck('object_id');

        $meetings = Blog::whereIn("id", $participantActions)->paginate(5, ['*'], 'meetings_page');

        return view('v2.user.profile', compact(
            'user',
            'subscription',
            'daysLeft',
            'products',
            'subscriptionTxt',
            'currentSubscriptionLevel',
            'mergedDataPaginated',
            'invID',
            'productPermission',
            'courseContents',
            'clubs',
            'meetings',
            'subscriptionStatus',
            'activeTab'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // --- Handle Other Profile Data ---
        $user->firstname = $request->input('firstname', $user->firstname);
        $user->lastname = $request->input('lastname', $user->lastname);
        $user->phone = $request->input('phone', $user->phone);
        $user->save();

        return redirect()->route('v2.profile.index')->with('success', 'Профиль успешно обновлен.');
    }

    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();

        // Удаляем старый аватар, если есть
        $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
        foreach (glob($pattern) as $oldFile) {
            @unlink($oldFile);
        }

        $file = $request->file('avatar');
        $ext = $file->getClientOriginalExtension();
        $filename = md5($user->id . $user->phone) . '.' . $ext;
        $file->move(public_path('img/avatars/'), $filename);

        $avatarUrl = url('/v2/avatar/' . $user->id); // Используем новый маршрут
        return response()->json(['success' => true, 'message' => 'Аватар успешно обновлен.', 'avatar_url' => $avatarUrl]);
    }

    public function removeAvatar(Request $request)
    {
        $user = Auth::user();
        $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
        $deleted = false;
        foreach (glob($pattern) as $oldFile) {
            @unlink($oldFile);
            $deleted = true;
        }
        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Аватар удален.' : 'Аватар не найден.'
        ]);
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $request->input('amount');
        $user = Auth::user();
        $description = "Пополнение баланса на сайте";

        $transaction = $this->paymentService->createBalanceTransaction(
            $user->id,
            $amount,
            $this->robokassaGateway->getName()
        );

        $paymentUrl = $this->robokassaGateway->generatePaymentUrl(
            $transaction->id,
            $amount,
            $description,
            [
                'Shp_user' => $user->id,
                'Shp_tr_id' => $transaction->id,
                'Email' => $user->email,
                'receipt' => [
                    [
                        'name' => $description,
                        'quantity' => 1,
                        'sum' => $amount,
                    ]
                ]
            ]
        );

        return Redirect::to($paymentUrl);
    }

    public function fetchTransactions(Request $request)
    {
        $user = Auth::user();
        $transactions = Transactions::where('user_id', $user->id)->latest()->get();
        $subscriptionPays = SubscriptionPays::where('user_id', $user->id)->with('product')->latest()->get();
        $mergedData = $transactions->concat($subscriptionPays)->sortByDesc('created_at');

        $page = $request->input('page', 1);

        $mergedDataPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $mergedData->forPage($page, 5),
            $mergedData->count(),
            5,
            $page,
            ['path' => route('v2.profile.transactions')]
        );

        return view('v2.user.partials.tabs._transactions_list', compact('mergedDataPaginated'))->render();
    }

    public function generateQrLink(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $amount = $request->input('amount');
        $user = Auth::user();
        $description = "Пополнение баланса (QR)";

        $transaction = $this->paymentService->createBalanceTransaction(
            $user->id,
            $amount,
            $this->robokassaGateway->getName()
        );

        $paymentUrl = $this->robokassaGateway->generatePaymentUrl(
            $transaction->id,
            $amount,
            $description,
            [
                'Shp_user' => $user->id,
                'Shp_tr_id' => $transaction->id,
                'Email' => $user->email,
                'IncCurrLabel' => 'BANKEXPRESS',
                'receipt' => [
                    [
                        'name' => $description,
                        'quantity' => 1,
                        'sum' => $amount,
                    ]
                ]
            ]
        );

        return response()->json(['url' => $paymentUrl]);
    }
    
    public function handleSubscription(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Определяем, была ли у пользователя уже такая подписка
        $hasHadSubscription = SubscriptionPays::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        // Цена, которую нужно списать с баланса (может быть 0 для триала)
        $chargePrice = ($product->first_week_price !== null && !$hasHadSubscription)
            ? $product->first_week_price
            : $product->price;
            
        // Цена, которую нужно записать в историю (всегда основная цена)
        $logPrice = $product->price;

        // Проверяем, достаточно ли средств на балансе для списания
        if ($user->balance >= $chargePrice) {
            // Списываем средства с баланса
            $user->balance -= $chargePrice;
            $user->save();

            // Определяем, будет ли подписка активной сразу
            $isActive = ($product->price == 0); // Активна, если бесплатная (триал)

            // Активируем подписку
            $subscription = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level' => $product->level,
                    'expired_at' => Carbon::now()->addMonth(),
                    'auto' => 0,
                    'is_active' => $isActive
                ]
            );

            // Создаем новую запись в SubscriptionPays, теперь с subscription_id
            SubscriptionPays::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'active' => 1, // Эта запись - активный платеж
                'action' => 'buy',
                'auto' => 0,
            ]);

            return redirect()->route('v2.profile.index')->with('pay_success', 'Подписка успешно оформлена!');
        }

        // Если средств на балансе недостаточно, перенаправляем на Robokassa
        $description = "Оплата подписки: {$product->name}";

        $subscriptionPay = SubscriptionPays::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $chargePrice,
            'active' => 0,
            'action' => 'buy_pending',
            'auto' => 0,
        ]);

        $paymentUrl = $this->robokassaGateway->generatePaymentUrl(
            $subscriptionPay->id,
            $chargePrice,
            $description,
            [
                'Shp_user' => $user->id,
                'Shp_product' => $product->id,
                'Shp_subpay' => $subscriptionPay->id,
                'Email' => $user->email,
                'receipt' => [
                    [
                        'name' => "Оплата подписки: {$product->name}",
                        'quantity' => 1,
                        'sum' => $chargePrice,
                    ]
                ]
            ]
        );

        return Redirect::to($paymentUrl);
    }
    
    public function paymentSuccess(Request $request)
    {
        return redirect()->route('v2.profile.index', ['tab' => 'balance'])->with('pay_success', 'Ваш платеж обрабатывается. Баланс будет пополнен в течение нескольких минут.');
    }

    public function paymentFail()
    {
        return redirect()->route('v2.profile.index', ['tab' => 'balance'])->with('pay_error', 'Произошла ошибка во время оплаты. Пожалуйста, попробуйте снова или обратитесь в поддержку.');
    }

    public function getAvatar($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            abort(404);
        }

        // Ищем аватар пользователя по текущей логике (md5-хеш или другой велосипед)
        $files = glob(public_path('img/avatars/') . md5($user->id . $user->phone) . '.*');

        if (!empty($files)) {
            $filePath = $files[0];
            return response()->file($filePath);
        }

        // Если файл не найден — редирект на ui-avatars
        $name = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'A') . '&background=random&color=fff&size=128';

        return redirect($avatarUrl);
    }
} 