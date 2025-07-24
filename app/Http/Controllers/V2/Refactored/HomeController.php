<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Specialist;
use App\Models\User;
use App\Models\ViewParts;
use App\Models\Category;
use App\Models\Videos as Video;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Отображение главной страницы
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Блокируем заблокированных пользователей
        if ($user && $user->group == 'block') {
            auth()->logout();
            return redirect()->route('v2.refactored.auth.login')
                ->with('error', 'Ваш аккаунт заблокирован');
        }

        // Простые данные без кэширования для отладки
        $data = [
            'specialists' => $this->getTopSpecialists(),
            'courses' => $this->getPopularCourses(),
            'blogs' => $this->getLatestBlogs(),
            'videos' => $this->getPopularVideos(),
            'stats' => $this->getStats(),
            'viewParts' => collect([]) // Упрощаем для отладки
        ];

        // SEO данные
        $seoData = [
            'title' => 'АЧПП - Ассоциация частнопрактикующих психологов и психотерапевтов',
            'description' => 'Профессиональное развитие психологов: курсы, встречи, клубы, видеотека. Присоединяйтесь к сообществу практикующих специалистов.',
            'keywords' => 'психология, психотерапия, курсы психологов, профессиональное развитие, АЧПП, психологическое образование'
        ];

        return view('refactored.home.index', array_merge($data, $seoData, [
            'user' => $user
        ]));
    }

    /**
     * Получение топ специалистов
     */
    private function getTopSpecialists()
    {
        try {
            return Specialist::with(['user', 'category'])
                ->where('status', true)
                ->orderBy('rating', 'desc')
                ->limit(8)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Получение популярных курсов
     */
    private function getPopularCourses()
    {
        try {
            return Course::with(['category', 'user'])
                ->where('status', 1)
                ->orderBy('views', 'desc')
                ->limit(6)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Получение последних блогов
     */
    private function getLatestBlogs()
    {
        try {
            return Blog::with(['category', 'user'])
                ->where('status', 1)
                ->latest()
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Получение популярных видео
     */
    private function getPopularVideos()
    {
        try {
            return Video::where('status', 1)
                ->orderBy('views', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Получение статистики платформы
     */
    private function getStats()
    {
        try {
            return [
                'users_count' => User::count(),
                'specialists_count' => Specialist::where('status', true)->count(),
                'courses_count' => Course::where('status', 1)->count(),
                'videos_count' => Video::where('status', 1)->count(),
                'blogs_count' => Blog::where('status', 1)->count()
            ];
        } catch (\Exception $e) {
            return [
                'users_count' => 0,
                'specialists_count' => 0,
                'courses_count' => 0,
                'videos_count' => 0,
                'blogs_count' => 0
            ];
        }
    }

    /**
     * Поиск по сайту
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        
        if (empty($query)) {
            return redirect()->route('v2.refactored.home.index');
        }

        $results = [];
        
        try {
            if ($type === 'all' || $type === 'courses') {
                $results['courses'] = Course::where('status', 1)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->with(['category', 'user'])
                    ->limit(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'specialists') {
                $results['specialists'] = Specialist::where('status', true)
                    ->whereHas('user', function($q) use ($query) {
                        $q->where('firstname', 'LIKE', "%{$query}%")
                          ->orWhere('lastname', 'LIKE', "%{$query}%");
                    })
                    ->orWhere('about', 'LIKE', "%{$query}%")
                    ->orWhere('degree', 'LIKE', "%{$query}%")
                    ->with(['user', 'category'])
                    ->limit(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'blogs') {
                $results['blogs'] = Blog::where('status', 1)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->with(['category', 'user'])
                    ->limit(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'videos') {
                $results['videos'] = Video::where('status', 1)
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get();
            }
        } catch (\Exception $e) {
            $results = [];
        }

        return view('refactored.home.search', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'title' => "Поиск: {$query} - АЧПП",
            'description' => "Результаты поиска по запросу '{$query}' на платформе АЧПП"
        ]);
    }

    /**
     * Страница "О нас"
     */
    public function about(): View
    {
        return view('refactored.home.about', [
            'title' => 'О нас - АЧПП',
            'description' => 'Узнайте больше об Ассоциации частнопрактикующих психологов и психотерапевтов'
        ]);
    }

    /**
     * Контакты
     */
    public function contacts(): View
    {
        return view('refactored.home.contacts', [
            'title' => 'Контакты - АЧПП',
            'description' => 'Свяжитесь с нами - контактная информация АЧПП'
        ]);
    }
}