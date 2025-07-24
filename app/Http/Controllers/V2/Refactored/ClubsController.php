<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Services\V2\ClubService;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ClubsController extends Controller
{
    public function __construct(
        private ClubService $clubService
    ) {}

    /**
     * Отображение списка клубов
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $userLevel = $user ? $user->level ?? 1 : 1;

        try {
            // Получаем клубы в зависимости от уровня пользователя
            $clubs = $this->clubService->getClubsByUserLevel($userLevel);
            
            // Получаем предстоящие клубы
            $upcomingClubs = $this->clubService->getUpcomingClubs();
            
            // Получаем клубы пользователя, если он авторизован
            $userClubs = $user ? $this->clubService->getUserClubs($user->id) : collect([]);
            
        } catch (\Exception $e) {
            $clubs = collect([]);
            $upcomingClubs = collect([]);
            $userClubs = collect([]);
        }

        return view('refactored.clubs.index', [
            'clubs' => $clubs,
            'upcomingClubs' => $upcomingClubs,
            'userClubs' => $userClubs,
            'user' => $user,
            'title' => 'Онлайн-клубы - АЧПП',
            'description' => 'Присоединяйтесь к онлайн-клубам АЧПП для профессионального общения и развития'
        ]);
    }

    /**
     * Отображение конкретного клуба
     */
    public function show(Request $request, int $id): View
    {
        try {
            $club = $this->clubService->getClubById($id);
            
            if (!$club) {
                abort(404, 'Клуб не найден');
            }

            $user = Auth::user();
            $canJoin = $user ? $this->clubService->canUserJoinClub($user->id, $id) : false;
            $isParticipant = $user ? $this->clubService->isUserParticipant($user->id, $id) : false;
            
            // Получаем участников клуба
            $participants = $this->clubService->getClubParticipants($id);
            
            // Получаем похожие клубы
            $similarClubs = $this->clubService->getSimilarClubs($id, 4);

        } catch (\Exception $e) {
            abort(404, 'Клуб не найден');
        }

        return view('refactored.clubs.show', [
            'club' => $club,
            'canJoin' => $canJoin,
            'isParticipant' => $isParticipant,
            'participants' => $participants,
            'similarClubs' => $similarClubs,
            'user' => $user,
            'title' => $club->title . ' - АЧПП',
            'description' => $club->description ? strip_tags($club->description) : 'Онлайн-клуб АЧПП'
        ]);
    }

    /**
     * Присоединение к клубу
     */
    public function join(Request $request, int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('v2.refactored.auth.login')
                ->with('error', 'Для участия в клубе необходимо авторизоваться');
        }

        try {
            $result = $this->clubService->joinClub(Auth::id(), $id);
            
            if ($result['success']) {
                return redirect()->route('v2.refactored.clubs.show', $id)
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('v2.refactored.clubs.show', $id)
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('v2.refactored.clubs.show', $id)
                ->with('error', 'Произошла ошибка при присоединении к клубу');
        }
    }

    /**
     * Покинуть клуб
     */
    public function leave(Request $request, int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('v2.refactored.auth.login');
        }

        try {
            $result = $this->clubService->leaveClub(Auth::id(), $id);
            
            if ($result['success']) {
                return redirect()->route('v2.refactored.clubs.show', $id)
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('v2.refactored.clubs.show', $id)
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('v2.refactored.clubs.show', $id)
                ->with('error', 'Произошла ошибка при выходе из клуба');
        }
    }

    /**
     * Поиск клубов
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $category = $request->get('category', '');
        
        try {
            $clubs = $this->clubService->searchClubs($query, $category);
        } catch (\Exception $e) {
            $clubs = collect([]);
        }

        return view('refactored.clubs.search', [
            'clubs' => $clubs,
            'query' => $query,
            'category' => $category,
            'title' => $query ? "Поиск клубов: {$query} - АЧПП" : 'Поиск клубов - АЧПП',
            'description' => 'Найдите подходящий онлайн-клуб для профессионального развития'
        ]);
    }

    /**
     * Мои клубы (для авторизованных пользователей)
     */
    public function myClubs(Request $request): View
    {
        if (!Auth::check()) {
            return redirect()->route('v2.refactored.auth.login');
        }

        try {
            $userClubs = $this->clubService->getUserClubs(Auth::id());
            $upcomingClubs = $this->clubService->getUserUpcomingClubs(Auth::id());
        } catch (\Exception $e) {
            $userClubs = collect([]);
            $upcomingClubs = collect([]);
        }

        return view('refactored.clubs.my-clubs', [
            'userClubs' => $userClubs,
            'upcomingClubs' => $upcomingClubs,
            'title' => 'Мои клубы - АЧПП',
            'description' => 'Управление участием в онлайн-клубах АЧПП'
        ]);
    }

    /**
     * Клубы по категориям
     */
    public function category(Request $request, int $categoryId): View
    {
        try {
            $clubs = $this->clubService->getClubsByCategory($categoryId);
            $categoryName = $this->clubService->getCategoryName($categoryId);
        } catch (\Exception $e) {
            $clubs = collect([]);
            $categoryName = 'Неизвестная категория';
        }

        return view('refactored.clubs.category', [
            'clubs' => $clubs,
            'categoryName' => $categoryName,
            'categoryId' => $categoryId,
            'title' => "Клубы: {$categoryName} - АЧПП",
            'description' => "Онлайн-клубы по направлению {$categoryName} в АЧПП"
        ]);
    }

    /**
     * API методы для AJAX запросов
     */
    
    /**
     * Получить клубы для API
     */
    public function apiIndex(Request $request)
    {
        $user = Auth::user();
        $userLevel = $user ? $user->level ?? 1 : 1;

        try {
            $clubs = $this->clubService->getClubsByUserLevel($userLevel);
            
            return response()->json([
                'success' => true,
                'clubs' => $clubs->map(function($club) {
                    return [
                        'id' => $club->id,
                        'title' => $club->title,
                        'description' => strip_tags($club->description),
                        'date' => $club->formatted_date ?? null,
                        'time' => $club->formatted_time ?? null,
                        'participants_count' => $club->participants_count ?? 0,
                        'max_participants' => $club->max_participants,
                        'image' => $club->image ? asset('storage/' . $club->image) : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке клубов'
            ], 500);
        }
    }

    /**
     * Присоединиться к клубу через API
     */
    public function apiJoin(Request $request, int $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация'
            ], 401);
        }

        try {
            $result = $this->clubService->joinClub(Auth::id(), $id);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при присоединении к клубу'
            ], 500);
        }
    }
}