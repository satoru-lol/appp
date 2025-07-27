<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ContentVideo;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Отображение главной страницы видеотеки
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для доступа к видео необходимо авторизоваться');
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return redirect()->route('v2.profile.index', ['tab' => 'profile'])->with('pay_error', 'Для доступа к видеотеке необходима активная подписка.');
        }

        $categories = Category::whereHas('videos')
            ->withCount('videos')
            ->get();

        return view('v2.video.index', compact('categories'));
    }

    /**
     * Отображение страницы категории
     */
    public function showCategory($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для доступа к видео необходимо авторизоваться');
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return redirect()->route('v2.profile.index', ['tab' => 'subscription'])->with('pay_error', 'Для доступа к видеотеке необходима активная подписка.');
        }

        $category = Category::with('children')->findOrFail($id);
        $videos = ContentVideo::where('category_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('v2.video.category', compact('category', 'videos'));
    }

    /**
     * Отображение страницы видео
     */
    public function showVideo($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для доступа к видео необходимо авторизоваться');
        }

        $video = ContentVideo::findOrFail($id);
        
        // Проверка доступа к видео на основе подписки
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return redirect()->route('v2.profile.index', ['tab' => 'subscription'])->with('pay_error', 'Для доступа к видеотеке необходима активная подписка.');
        }

        return view('v2.video.video', compact('video'));
    }

    /**
     * API для получения списка видео по категории
     */
    public function getCategoryVideos($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Для доступа к видео необходимо авторизоваться'], 401);
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return response()->json(['error' => 'Видеотека доступна только по подписке "Пробная", "Премиум" или "Базовый"'], 403);
        }

        $category = Category::findOrFail($id);
        $videos = ContentVideo::where('category_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Формируем массив с нужными полями и форматируем дату
        $videos->getCollection()->transform(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'url' => $video->url,
                'thumbnail_url' => $video->thumbnail_url,
                'created_at' => $video->created_at ? $video->created_at->format('d.m.Y') : null,
                'speakers' => $video->speakers ?? null,
            ];
        });

        return response()->json([
            'videos' => $videos,
            'category' => $category->only(['id', 'name'])
        ]);
    }
    
    /**
     * Поиск видео
     */
    public function searchVideos(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Для доступа к видео необходимо авторизоваться'], 401);
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return response()->json(['error' => 'Видеотека доступна только по подписке "Пробная", "Премиум" или "Базовый"'], 403);
        }
        
        $query = $request->input('query');
        $categoryId = $request->input('category_id');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['error' => 'Запрос должен содержать минимум 2 символа'], 400);
        }
        
        // Поиск видео по заголовку, с фильтрацией по категории, если она указана
        $videosQuery = ContentVideo::where('title', 'LIKE', '%' . $query . '%');
        
        // Если указана категория, ищем только в ней
        if ($categoryId) {
            $videosQuery->where('category_id', $categoryId);
        }
        
        $videos = $videosQuery->orderBy('created_at', 'desc')
            ->with('category') // Загружаем связанную категорию для каждого видео
            ->get();
        
        // Группировка видео по категориям
        $results = [];
        foreach ($videos as $video) {
            $videoCategoryId = $video->category_id;
            $categoryName = $video->category ? $video->category->name : 'Без категории';
            
            if (!isset($results[$videoCategoryId])) {
                $results[$videoCategoryId] = [
                    'category' => [
                        'id' => $videoCategoryId,
                        'name' => $categoryName
                    ],
                    'videos' => []
                ];
            }
            
            $results[$videoCategoryId]['videos'][] = [
                'id' => $video->id,
                'title' => $video->title,
                'url' => $video->url,
                'thumbnail_url' => $video->thumbnail_url, // Добавляем URL превью
                'created_at' => $video->created_at->format('d.m.Y'),
                'speakers' => $video->speakers ?? null
            ];
        }
        
        return response()->json([
            'success' => true,
            'results' => array_values($results),
            'total' => $videos->count()
        ]);
    }
} 