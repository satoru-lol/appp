<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\CoursesController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура находится в app/Http/Controllers/V2/Refactored/CoursesController.php
 * 
 * Миграция:
 * - Старые маршруты: /courses-v2/*
 * - Новые маршруты: /v2/refactored/courses/*
 * 
 * @see App\Http\Controllers\V2\Refactored\CoursesController
 */

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ParticipantActions;

class CoursesV2Controller extends Controller
{
    public function index(Request $request, int $category_id = 0): View
    {
        $user = $request->user();
        $today = now()->toDateString();
        
        $participantCourseIds = [];
        if ($user) {
            $participantCourseIds = ParticipantActions::where('user_id', $user->id)
                ->where('object_name', 'courses')
                ->pluck('object_id')
                ->toArray();
        }

        $query = CourseContent::whereHas('course')
            ->where('date', '>=', $today)
            ->with('course')
            ->orderBy('date');

        if ($category_id > 0) {
            $query->whereHas('course', function ($q) use ($category_id) {
                $q->where('id', $category_id);
            });
        }
        
        $courses = $query->get();
        
        $title = 'Курсы для психологов - АЧПП';
        $description = 'Профессиональные курсы и обучающие программы для психологов и психотерапевтов от Ассоциации частнопрактикующих психологов и психотерапевтов.';
        $keywords = 'курсы для психологов, обучение психотерапии, повышение квалификации, психология, психотерапия, АЧПП, образовательные программы';

        return view('courses-v2.index', compact('courses', 'user', 'title', 'description', 'keywords', 'participantCourseIds'));
    }

    public function show($id, Request $request): View
    {
        $user = $request->user();
        $today = now()->toDateString();
        
        // Получаем курс по ID
        $course = CourseContent::where('id', $id)
            ->with('course')
            ->first();
            
        if (!$course) {
            abort(404, 'Курс не найден');
        }
        
        // Получаем информацию о курсе
        $courseInfo = Course::where('id', $course->course_id)->first();
        
        // Проверяем, существует ли course->course и есть ли у него product_level
        $product = null;
        if ($course->course && isset($course->course->product_level)) {
            $product = Product::where('level', $course->course->product_level)->first();
        }
        
        $title = $course->course->title ?? $course->title . ' - Курс АЧПП';
        $description = 'Подробная информация о курсе "' . ($course->course->title ?? $course->title) . '". ' . \Illuminate\Support\Str::limit(strip_tags($course->course->text ?? $course->description), 120);
        $keywords = ($course->course->title ?? $course->title) . ', курс для психологов, ' . ($course->course->speakers ?? $course->speakers) . ', обучение, АЧПП';

        return view('courses-v2.show', compact('course', 'courseInfo', 'user', 'product', 'title', 'description', 'keywords'));
    }
    
    public function category($id, Request $request): View
    {
        return $this->index($request, $id);
    }
    
    /**
     * Запись пользователя на курс
     */
    public function subscribeToCourse($id, Request $request)
    {
        $user = $request->user();
        $course = CourseContent::where('id', $id)->with('course')->first();
        
        if (!$course) {
            return back()->with('error', 'Курс не найден');
        }
        
        // Проверяем уровень подписки пользователя
        $userSubscription = \App\Models\Subscription::where('user_id', $user->id)->first();
        $userLevel = $userSubscription ? $userSubscription->level : 0;
        
        // Проверяем, является ли уровень подписки пробным (-1) или выше нуля
        $hasAccess = ($userLevel == -1 || $userLevel > 0);
        
        // Для подписок выше нуля, но не пробных, проверяем разрешения
        if ($userLevel > 0 && $userLevel != -1) {
            $productPermission = \App\Models\ProductPermission::join('products', 'products.id', '=', 'product_permissions.product_id')
                ->where('products.level', $userLevel)
                ->first();
            
            $hasAccess = $productPermission && $productPermission->course;
        }
        
        // Если у пользователя нет доступа, перенаправляем на страницу подписок
        if (!$hasAccess) {
            return redirect()->route('v2.profile.index', ['tab' => 'profile'])
                ->with('error', 'Для записи на курс необходимо подключить подписку подходящего уровня');
        }
        
        // Проверяем, не записан ли пользователь уже на этот курс
        $isParticipant = \App\Models\ParticipantActions::where('object_id', $course->id)
            ->where('object_name', 'courses')
            ->where('user_id', $user->id)
            ->exists();
        
        if ($isParticipant) {
            return back()->with('info', 'Вы уже записаны на этот курс');
        }
        
        // Записываем пользователя на курс
        \App\Models\ParticipantActions::create([
            'user_id' => $user->id,
            'object_id' => $course->id,
            'object_name' => 'courses'
        ]);
        
        return back()->with('success', 'Вы успешно записались на курс!');
    }
} 