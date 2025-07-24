<?php

/**
 * @deprecated Этот контроллер устарел. Функционал клубов интегрирован в рефакторенную архитектуру
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Функционал клубов теперь находится в:
 * - app/Services/V2/ClubService.php (бизнес-логика клубов)
 * - app/Repositories/V2/ClubRepository.php (работа с данными)
 * - app/Services/V2/ProfileService.php (клубы в профиле пользователя)
 * 
 * Миграция:
 * - Старые маршруты: /clubs/*
 * - Новые маршруты: интегрированы в /v2/refactored/profile/* (для пользователей)
 * 
 * @see App\Services\V2\ClubService
 * @see App\Http\Controllers\V2\Refactored\ProfileController
 */

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Donat;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transactions;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClubController extends Controller
{

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $clubs = Club::with('clubDates')
            ->where('is_hidden', '!=', 1)
            ->whereHas('clubDates', function ($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->get();

        foreach ($clubs as $club) {
            $near = $club->clubDates->first(); // Берем первую дату для отображения
            if ($near && !empty($near->date)) {
                $near->start_time = Carbon::createFromFormat('H:i:s', $near->start_time)->format('H:i');
                $near->end_time = Carbon::createFromFormat('H:i:s', '23:59:59')->format('H:i');
                $date = Carbon::parse($near->date);

                $club->date = $date->translatedFormat('d F Y, H:i');
            }
        }

        $isPermittedAdd = false;
        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }

        return view('club.index', compact('user', 'clubs', 'isPermittedAdd'));
    }

    public function indexTest(Request $request)
    {
        $user = $request->user();
        
        $clubs = Club::with('clubDates')
            // ->where('is_hidden', '!=', 1)
            // ->whereHas('clubDates', function ($query) {
            //     $query->where('date', '>=', Carbon::now());
            // })
            ->get();
            
        $isPermittedAdd = $user && $user->group === 'admin';

        $title = 'Онлайн-клубы - АЧПП';
        $description = 'Присоединяйтесь к нашим онлайн-клубам для психологов и психотерапевтов. Обсуждайте актуальные темы, делитесь опытом и получайте поддержку коллег.';
        $keywords = 'онлайн-клубы для психологов, профессиональные встречи, групповые обсуждения, психологическое сообщество, вебинары для психологов';

        return view('club.index-test', compact('user', 'clubs', 'isPermittedAdd', 'title', 'description', 'keywords'));
    }

    public function indexOld(Request $request): View
    {
        $user = $request->user();
        $clubs = Club::where('date', '>=', Carbon::now())
            ->orderBy('date', 'desc')
            ->get();

        foreach ($clubs as $club) {
            if (!empty($club->date)) {
                $date = Carbon::parse($club->date);

                $club->date = $date->translatedFormat('d F Y, H:i');
            }
        }
        $isPermittedAdd = false;
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["club"]) && $perms["add"]["club"] == true) {
                $isPermittedAdd = true;
            }
        }

        return view('club.index', compact('user', 'clubs', 'isPermittedAdd'));
    }

    public function editClub($id, Request $request)
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }

        ini_set('memory_limit', '512M');
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '512M');

        $club = Club::where("id", $id)->first();
        $user = $request->user();
        $products = Product::all();
        if (!empty($request->get("title")) && !empty($club)) {
            $request->validate([
                'date' => 'required',
                'title' => 'required|string|max:255',
                'speakers' => 'required|string|max:255',
                'theory' => 'required|string|max:255',
                'feedback' => 'required|string|max:255',
                'course_category_id' => 'required|integer',
                'times.read' => 'nullable|string|max:255',
                'times.training' => 'nullable|string|max:255',
                //'times.start' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'desc' => 'nullable|string',
                'content.characteristics1' => 'nullable|string|max:255',
                'content.characteristics2' => 'nullable|string|max:255',
                'content.characteristics3' => 'nullable|string|max:255',
                'content.characteristics4' => 'nullable|string|max:255',
                'content.characteristics5' => 'nullable|string|max:255',
                'content.teach1.title' => 'nullable|string|max:255',
                'content.teach1.subtitle' => 'nullable|string|max:255',
                // Add validations for other fields as necessary
            ]);

            $club->title = $request->input('title');
            //  $club->course_category_id = $request->input('course_category_id'); // Set course_category_id
            $club->speakers = $request->input('speakers'); // Set course_category_id
            $club->theory = $request->input('theory'); // Set course_category_id
            $club->feedback = $request->input('feedback'); // Set course_category_id
            $club->text = $request->input('desc'); // Set course_category_id
            $club->date = $request->input("date");

            if (!empty($request->get("product_level"))) {
                $club->product_level = $request->get("product_level");
            }

            $club->times = json_encode($request->input('times'));

            if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
                $club->pay_method = $request->get("options-outlined");
                $club->practice = "0"; // Set course_category_id
            } else {
                $club->practice = $request->input('practice'); // Set course_category_id
            }

            if (!empty($request->input("uploaded_image"))) {
                $club->image = $request->input("uploaded_image");
            } else {
                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->image->extension();
                    $request->image->move(public_path('images'), $imageName);
                    $club->image = $imageName;
                }
            }



            if ($request->hasFile('video')) {
                $videoName = time().date("y-m-d").'.'.$request->video->extension();
                $request->video->move(public_path('images'), $videoName);
                $club->video = $videoName;
            }


            $club->save();

            return redirect("club");
        }

        return view('club.add', compact('club', 'user', 'products'));
    }

    public function show(int $id, Request $request): View
    {
        $user = $request->user();

        $club = Club::where('id', $id)->first();

        if (!$club) {
            abort(404);
        }

        if (!empty($club->date)) {
        $date = Carbon::parse($club->date);
        $club->date = $date->translatedFormat('d F Y, H:i');
        }

        //$content = CourseContent::where('course_id', $course_id)->first();
        $product = null;
        if (!empty($club->product_level)) {
        $product = Product::where("level", $club->product_level)->first();
        }

        // --- Получаем статус подписки через сервис ---
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);

        $title = $club->title . ' - Онлайн-клуб АЧПП';
        $description = 'Присоединяйтесь к онлайн-клубу "' . $club->title . '". ' . \Illuminate\Support\Str::limit(strip_tags($club->text), 120);
        $keywords = $club->title . ', онлайн-клуб, психология, психотерапия, АЧПП, ' . str_replace(' ', ', ', $club->speakers);

        return view('club.show', compact('club', 'user', 'product', 'subscriptionStatus', 'title', 'description', 'keywords'));
    }

    public function showTest(int $id, Request $request): View
    {
        $user = $request->user();

        $club = Club::where('id', $id)->first();

        if (!$club) {
            abort(404);
        }

        if (!empty($club->date)) {
            $date = Carbon::parse($club->date);
            $club->date = $date->translatedFormat('d F Y, H:i');
        }

        $product = null;
        if (!empty($club->product_level)) {
            $product = Product::where("level", $club->product_level)->first();
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);

        $title = $club->title . ' - Онлайн-клуб АЧПП';
        $description = 'Присоединяйтесь к онлайн-клубу "' . $club->title . '". ' . \Illuminate\Support\Str::limit(strip_tags($club->text), 120);
        $keywords = $club->title . ', онлайн-клуб, психология, психотерапия, АЧПП, ' . str_replace(' ', ', ', $club->speakers);

        return view('club.show-test', compact('club', 'user', 'product', 'subscriptionStatus', 'title', 'description', 'keywords'));
    }

    public function createShow()
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }
        $products = Product::all();

        return view('club.add', compact("products"));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }
        ini_set('memory_limit', '512M');
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '512M');

        //Course::where('id', 2)->delete();
        $request->validate([
            'date' => 'required',
            'title' => 'required|string|max:255',
            'speakers' => 'required|string|max:255',
            'theory' => 'required|string|max:255',
            'feedback' => 'required|string|max:255',
            'course_category_id' => 'required|integer',
            'times.read' => 'nullable|string|max:255',
            'times.training' => 'nullable|string|max:255',
            //'times.start' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'desc' => 'nullable|string',
            'content.characteristics1' => 'nullable|string|max:255',
            'content.characteristics2' => 'nullable|string|max:255',
            'content.characteristics3' => 'nullable|string|max:255',
            'content.characteristics4' => 'nullable|string|max:255',
            'content.characteristics5' => 'nullable|string|max:255',
            'content.teach1.title' => 'nullable|string|max:255',
            'content.teach1.subtitle' => 'nullable|string|max:255',
            // Add validations for other fields as necessary
        ]);
        $club = new Club();
        $club->title = $request->input('title');
      //  $club->course_category_id = $request->input('course_category_id'); // Set course_category_id
        $club->speakers = $request->input('speakers'); // Set course_category_id
        $club->theory = $request->input('theory'); // Set course_category_id
        $club->feedback = $request->input('feedback'); // Set course_category_id
        $club->text = $request->input('desc'); // Set course_category_id
        $club->date = $request->input("date");

        if (!empty($request->get("product_level"))) {
            $club->product_level = $request->get("product_level");
        }

        $club->times = json_encode($request->input('times'));

        if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
            $club->pay_method = $request->get("options-outlined");
            $club->practice = "0"; // Set course_category_id
        } else {
            $club->practice = $request->input('practice'); // Set course_category_id
        }

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $club->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $videoName = time().date("y-m-d").'.'.$request->video->extension();
            $request->video->move(public_path('images'), $videoName);
            $club->video = $videoName;
        }


        $club->save();

        /*foreach ($request->input('content') as $key => $value) {
            if (!empty($value)) {
                CourseContent::create([
                    'course_id' => $course->id,
                    'text' => json_encode($value)
                ]);
            }
        }*/

        return redirect()->route('club')->with('success', 'Курс успешно добавлен');
    }

    public function donate(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:club,id',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }
        
        $amount = (float)$request->amount;
        
        // Проверяем баланс пользователя
        if ($user->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 400);
        }
        
        try {
            // Начинаем транзакцию
            \DB::beginTransaction();
            
            // Списываем средства с баланса пользователя
            $user->balance -= $amount;
            $user->save();
            
            // Создаем запись о донате
            Donat::create([
                'user_id' => $user->id,
                'club_id' => $request->club_id,
                'amount' => $amount,
                'reason' => $request->reason,
            ]);
            
            // Создаем запись о транзакции
            Transactions::create([
                'user_id' => $user->id,
                'sum' => -$amount, // отрицательная сумма, т.к. это списание
                'state' => 'completed',
                'shop' => 'donation',
                'op_key' => 'club_donation_' . $request->club_id . '_' . time(),
            ]);
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Донат успешно отправлен',
                'new_balance' => $user->balance
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при отправке доната',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
