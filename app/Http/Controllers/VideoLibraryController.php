<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\VideoController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура видеотеки находится в app/Http/Controllers/V2/Refactored/VideoController.php
 * 
 * Миграция:
 * - Старые маршруты: /video-library/*
 * - Новые маршруты: /v2/refactored/video/*
 * 
 * Новая архитектура включает:
 * - VideoService для бизнес-логики с проверкой подписки
 * - VideoRepository для работы с данными
 * - Оптимизированные запросы к БД
 * - API поддержку
 * 
 * @see App\Http\Controllers\V2\Refactored\VideoController
 */

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Course;
use App\Models\ParticipantActions;
use App\Models\User;
use App\Models\VideoLibrary;
use App\Models\Videos;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class VideoLibraryController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {

        $libraries = VideoLibrary::all();
        return view('video_libraries.index', compact('libraries'));
    }

    public function create()
    {
        return view('video_libraries.create');
    }

    public function store(Request $request)
    {
        // Валидация
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'videos.*.google_url' => 'nullable|url',
            'videos.*.yandex_url' => 'nullable|url',
            'videos.*.path' => 'nullable|string',
        ]);

        // Сохранение библиотеки видео
        $videoLibrary = VideoLibrary::create([
            'title' => $validated['title'],
        ]);

        // Сохранение видео
        if (!empty($validated['videos'])) {
            foreach ($validated['videos'] as $videoData) {
                Videos::create([
                    'google_url' => $videoData['google_url'],
                    'yandex_url' => $videoData['yandex_url'],
                    'path' => $videoData['path'],
                    'title' => $videoLibrary->title,  // Привязка к названию библиотеки
                    'video_library_id' => $videoLibrary->id,
                ]);
            }
        }

        return redirect()->route('video-libraries.index')->with('success', 'Video library created successfully.');
    }

    public function show($id)
    {
        // Проверяем, авторизован ли пользователь
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для доступа к видео необходимо авторизоваться');
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        // Проверяем, есть ли у пользователя подписка уровня "Пробная" (-1), "Премиум" (2) или "Базовый" (1)
        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return redirect()->route('videostream')->with('error', 'Видеотека доступна только по подписке "Пробная", "Премиум" или "Базовый"');
        }

        $videos = Videos::where('video_library_id', $id)->get();

        return view('video_libraries.show', compact('videos'));
    }

    public function edit($id)
    {
        $library = VideoLibrary::findOrFail($id);
        $videos = Videos::where('video_library_id', $id)->get();

        return view('video_libraries.edit', compact('library', 'videos'));
    }

    public function update(Request $request, $id)
    {
        // Валидация
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'videos.*.google_url' => 'nullable|url',
            'videos.*.yandex_url' => 'nullable|url',
            'videos.*.path' => 'nullable|string',
        ]);

        // Обновление библиотеки
        $videoLibrary = VideoLibrary::findOrFail($id);
        $videoLibrary->update(['title' => $validated['title']]);

        // Удаление старых видео и добавление новых
        Videos::where('video_library_id', $id)->delete();

        if (!empty($validated['videos'])) {
            foreach ($validated['videos'] as $videoData) {
                Videos::create([
                    'google_url' => $videoData['google_url'],
                    'yandex_url' => $videoData['yandex_url'],
                    'path' => $videoData['path'],
                    'title' => $videoLibrary->title,
                    'video_library_id' => $videoLibrary->id,
                ]);
            }
        }

        return redirect()->route('video-libraries.index')->with('success', 'Video library updated successfully.');
    }

    public function destroy($id)
    {
        VideoLibrary::destroy($id);
        Videos::where('video_library_id', $id)->delete();

        return redirect()->route('video-libraries.index')->with('success', 'Video library deleted successfully.');
    }
}
