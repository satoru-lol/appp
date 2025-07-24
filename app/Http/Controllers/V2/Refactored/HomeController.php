<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Specialist;
use App\Models\User;
use App\Models\ViewParts;
use App\Models\Category;
use App\Models\Video;
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

        // Кэшируем данные на 10 минут для оптимизации
        $data = Cache::remember('home_page_data', 600, function () {
            return [
                'specialists' => $this->getTopSpecialists(),
                'courses' => $this->getPopularCourses(),
                'blogs' => $this->getLatestBlogs(),
                'videos' => $this->getPopularVideos(),
                'stats' => $this->getStats(),
                'viewParts' => ViewParts::all()
            ];
        });

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
        return Specialist::with(['user', 'category'])
            ->active()
            ->topRated()
            ->limit(8)
            ->get();
    }

    /**
     * Получение популярных курсов
     */
    private function getPopularCourses()
    {
        return Course::with(['category', 'user'])
            ->where('status', 1)
            ->orderBy('views', 'desc')
            ->limit(6)
            ->get();
    }

    /**
     * Получение последних блогов
     */
    private function getLatestBlogs()
    {
        return Blog::with(['category', 'user'])
            ->where('status', 1)
            ->latest()
            ->limit(4)
            ->get();
    }

    /**
     * Получение популярных видео
     */
    private function getPopularVideos()
    {
        return Video::with(['category'])
            ->where('status', 1)
            ->orderBy('views', 'desc')
            ->limit(4)
            ->get();
    }

    /**
     * Получение статистики платформы
     */
    private function getStats()
    {
        return Cache::remember('platform_stats', 3600, function () {
            return [
                'users_count' => User::count(),
                'specialists_count' => Specialist::active()->count(),
                'courses_count' => Course::where('status', 1)->count(),
                'videos_count' => Video::where('status', 1)->count(),
                'blogs_count' => Blog::where('status', 1)->count()
            ];
        });
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
            $results['specialists'] = Specialist::active()
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
                ->with(['category'])
                ->limit(10)
                ->get();
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