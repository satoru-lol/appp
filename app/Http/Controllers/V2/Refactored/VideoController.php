<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Services\V2\VideoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct(
        private VideoService $videoService
    ) {}

    public function index(Request $request): View
    {
        $userId = auth()->id();
        
        $data = $this->videoService->getVideoLibraryIndex($userId);
        
        $title = 'Видеотека для психологов - АЧПП';
        $description = 'Профессиональная видеотека с записями лекций, семинаров и мастер-классов для психологов и психотерапевтов от АЧПП.';
        $keywords = 'видеотека психологов, записи лекций, семинары психотерапия, мастер-классы, обучающие видео, АЧПП';
        
        return view('video-v2.index', array_merge($data, compact(
            'title', 'description', 'keywords'
        )));
    }

    public function showCategory(int $id, Request $request): View
    {
        $userId = auth()->id();
        
        $data = $this->videoService->getCategoryDetails($id, $userId);
        
        if (!$data['hasAccess']) {
            return redirect()->route('subscription.plans')
                ->with('warning', 'Для просмотра видеотеки необходима активная подписка');
        }
        
        return view('video-v2.category', $data);
    }

    public function showVideo(int $id, Request $request): View
    {
        $userId = auth()->id();
        
        try {
            $data = $this->videoService->getVideoDetails($id, $userId);
            
            if (!$data['hasAccess']) {
                return redirect()->route('subscription.plans')
                    ->with('warning', 'Для просмотра этого видео необходима активная подписка');
            }
            
            return view('video-v2.show', $data);
            
        } catch (\Exception $e) {
            return redirect()->route('v2.refactored.video.index')
                ->with('error', 'Видео не найдено или недоступно');
        }
    }

    public function search(Request $request): View|JsonResponse
    {
        $userId = auth()->id();
        $search = $request->input('search');
        
        if (!$search) {
            return redirect()->route('v2.refactored.video.index');
        }

        $data = $this->videoService->searchVideos($search, $userId);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'search' => $search
            ]);
        }

        $title = 'Поиск видео: ' . $search . ' - Видеотека АЧПП';
        $description = 'Результаты поиска видео по запросу "' . $search . '" в видеотеке АЧПП';
        
        return view('video-v2.search', array_merge($data, compact(
            'search', 'title', 'description'
        )));
    }

    public function popular(Request $request): View|JsonResponse
    {
        $userId = auth()->id();
        
        $data = $this->videoService->getPopularVideos($userId);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        $title = 'Популярные видео - Видеотека АЧПП';
        $description = 'Самые популярные видео в видеотеке АЧПП - лекции и семинары для психологов';
        
        return view('video-v2.popular', array_merge($data, compact(
            'title', 'description'
        )));
    }

    public function recent(Request $request): View|JsonResponse
    {
        $userId = auth()->id();
        
        $data = $this->videoService->getRecentVideos($userId);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        $title = 'Новые видео - Видеотека АЧПП';
        $description = 'Последние добавленные видео в видеотеку АЧПП';
        
        return view('video-v2.recent', array_merge($data, compact(
            'title', 'description'
        )));
    }

    // API методы
    public function getCategoryVideos(Request $request, int $id): JsonResponse
    {
        $userId = auth()->id();
        
        try {
            $data = $this->videoService->getCategoryVideos($id, $userId);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке видео категории'
            ], 500);
        }
    }

    public function searchVideos(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $search = $request->input('search');
        
        if (!$search) {
            return response()->json([
                'success' => false,
                'message' => 'Поисковый запрос не может быть пустым'
            ], 400);
        }

        try {
            $data = $this->videoService->searchVideos($search, $userId);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'search' => $search
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске видео'
            ], 500);
        }
    }

    public function incrementViews(Request $request, int $id): JsonResponse
    {
        $userId = auth()->id();
        
        try {
            $this->videoService->incrementVideoViews($id, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Просмотр засчитан'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при учете просмотра'
            ], 500);
        }
    }

    // Админские методы (если нужны)
    public function create(Request $request): View
    {
        $this->authorize('create-video');
        
        $categories = $this->videoService->getCategoryTree();
        
        return view('video-v2.admin.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create-video');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'access_level' => 'required|integer',
            'duration' => 'nullable|integer|min:0',
        ]);

        try {
            $video = $this->videoService->createVideo($validated, auth()->id());
            
            return redirect()->route('v2.refactored.video.show', $video->id)
                ->with('success', 'Видео успешно создано');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании видео: ' . $e->getMessage());
        }
    }

    public function edit(int $id, Request $request): View
    {
        $this->authorize('edit-video');
        
        $data = $this->videoService->getVideoDetails($id, auth()->id(), true);
        $categories = $this->videoService->getCategoryTree();
        
        return view('video-v2.admin.edit', array_merge($data, compact('categories')));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorize('edit-video');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'access_level' => 'required|integer',
            'duration' => 'nullable|integer|min:0',
        ]);

        try {
            $video = $this->videoService->updateVideo($id, $validated, auth()->id());
            
            return redirect()->route('v2.refactored.video.show', $video->id)
                ->with('success', 'Видео успешно обновлено');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении видео: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('delete-video');
        
        try {
            $this->videoService->deleteVideo($id, auth()->id());
            
            return redirect()->route('v2.refactored.video.index')
                ->with('success', 'Видео успешно удалено');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении видео: ' . $e->getMessage());
        }
    }
}