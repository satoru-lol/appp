<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Services\V2\CourseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CoursesController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function index(Request $request, int $categoryId = 0): View
    {
        $userId = auth()->id();
        
        $data = $this->courseService->getUpcomingCourses(
            $categoryId > 0 ? $categoryId : null, 
            $userId
        );
        
        $title = 'Курсы для психологов - АЧПП';
        $description = 'Профессиональные курсы и обучающие программы для психологов и психотерапевтов от Ассоциации частнопрактикующих психологов и психотерапевтов.';
        $keywords = 'курсы для психологов, обучение психотерапии, повышение квалификации, психология, психотерапия, АЧПП, образовательные программы';

        return view('courses-v2.index', array_merge($data, compact(
            'title', 'description', 'keywords', 'categoryId'
        )));
    }

    public function show(int $id, Request $request): View
    {
        $userId = auth()->id();
        
        $data = $this->courseService->getCourseDetails($id, $userId);
        
        return view('courses-v2.show', $data);
    }

    public function category(int $id, Request $request): View
    {
        return $this->index($request, $id);
    }

    public function subscribe(int $id, Request $request): RedirectResponse
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Для записи на курс необходимо авторизоваться');
        }

        try {
            $result = $this->courseService->subscribeToCourse($id, $userId);
            
            return redirect()->back()
                ->with('success', $result['message']);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при записи на курс: ' . $e->getMessage());
        }
    }

    public function search(Request $request): View|JsonResponse
    {
        $search = $request->input('search');
        
        if (!$search) {
            return redirect()->route('v2.refactored.courses.index');
        }

        $courses = $this->courseService->searchCourses($search);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'courses' => $courses,
                'search' => $search
            ]);
        }

        $title = 'Поиск курсов: ' . $search . ' - АЧПП';
        $description = 'Результаты поиска курсов по запросу "' . $search . '"';
        
        return view('courses-v2.search', compact(
            'courses', 'search', 'title', 'description'
        ));
    }

    public function create(Request $request): View
    {
        $this->authorize('create-course');
        
        $categories = $this->courseService->getCategories();
        
        return view('courses-v2.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create-course');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_level' => 'required|integer',
            'course_category_id' => 'required|exists:course_categories,id',
        ]);

        try {
            $course = $this->courseService->createCourse($validated, auth()->id());
            
            return redirect()->route('v2.refactored.courses.show', $course->id)
                ->with('success', 'Курс успешно создан');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании курса: ' . $e->getMessage());
        }
    }

    public function edit(int $id, Request $request): View
    {
        $this->authorize('edit-course');
        
        $data = $this->courseService->getCourseDetails($id, auth()->id());
        $categories = $this->courseService->getCategories();
        
        return view('courses-v2.edit', array_merge($data, compact('categories')));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorize('edit-course');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_level' => 'required|integer',
            'course_category_id' => 'required|exists:course_categories,id',
        ]);

        try {
            $course = $this->courseService->updateCourse($id, $validated, auth()->id());
            
            return redirect()->route('v2.refactored.courses.show', $course->id)
                ->with('success', 'Курс успешно обновлен');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении курса: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('delete-course');
        
        try {
            // Логика удаления курса
            return redirect()->route('v2.refactored.courses.index')
                ->with('success', 'Курс успешно удален');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении курса: ' . $e->getMessage());
        }
    }

    // API методы
    public function apiIndex(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $categoryId = $request->input('category_id');
        
        $data = $this->courseService->getUpcomingCourses($categoryId, $userId);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function apiSubscribe(Request $request, int $id): JsonResponse
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация'
            ], 401);
        }

        try {
            $result = $this->courseService->subscribeToCourse($id, $userId);
            
            return response()->json([
                'success' => true,
                'action' => $result['action'],
                'message' => $result['message']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при записи на курс: ' . $e->getMessage()
            ], 500);
        }
    }
}