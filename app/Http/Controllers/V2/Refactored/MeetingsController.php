<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Meetings\StoreMeetingRequest;
use App\Services\V2\MeetingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeetingsController extends Controller
{
    public function __construct(
        private MeetingService $meetingService
    ) {}

    public function index(): View
    {
        $meetings = $this->meetingService->getUpcomingMeetings();
        
        $title = 'Наши встречи - АЧПП';
        $description = 'Календарь предстоящих встреч, семинаров и мероприятий для психологов и психотерапевтов. Присоединяйтесь к профессиональному сообществу.';
        $keywords = 'встречи психологов, семинары для психотерапевтов, мероприятия АЧПП, профессиональные события, календарь мероприятий';

        return view('v2.meetings.index', compact(
            'meetings', 'title', 'description', 'keywords'
        ));
    }

    public function previous(): View
    {
        $meetings = $this->meetingService->getPreviousMeetings();
        
        $title = 'Прошедшие встречи - АЧПП';
        $description = 'Архив прошедших встреч, семинаров и мероприятий Ассоциации. Материалы и записи для участников.';
        $keywords = 'архив встреч, прошедшие мероприятия, записи семинаров, материалы АЧПП, история событий';

        return view('v2.meetings.previous', compact(
            'meetings', 'title', 'description', 'keywords'
        ));
    }

    public function show(int $id): View
    {
        $userId = auth()->id();
        
        $data = $this->meetingService->getMeetingDetails($id, $userId);
        
        return view('v2.meetings.show', $data);
    }

    public function create(): View
    {
        $formats = $this->meetingService->getAllFormats();
        
        return view('v2.meetings.add', compact('formats'));
    }

    public function store(StoreMeetingRequest $request): RedirectResponse
    {
        $userId = auth()->id();
        $validated = $request->validated();
        
        // Добавляем файл изображения к данным если есть
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }
        
        try {
            $meeting = $this->meetingService->createMeeting($validated, $userId);
            
            return redirect()
                ->route('v2.meetings.show', $meeting->id)
                ->with('success', 'Встреча успешно создана!');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ошибка при создании встречи: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $userId = auth()->id();
        
        try {
            $data = $this->meetingService->getMeetingDetails($id, $userId);
            $formats = $this->meetingService->getAllFormats();
            
            return view('v2.meetings.add', array_merge($data, compact('formats')));
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
    }

    public function update(StoreMeetingRequest $request, int $id): RedirectResponse
    {
        $userId = auth()->id();
        $validated = $request->validated();
        
        // Добавляем файл изображения к данным если есть
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }
        
        try {
            $meeting = $this->meetingService->updateMeeting($id, $validated, $userId);
            
            return redirect()
                ->route('v2.meetings.show', $meeting->id)
                ->with('success', 'Встреча успешно обновлена!');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении встречи: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $userId = auth()->id();
        
        try {
            $this->meetingService->deleteMeeting($id, $userId);
            
            return redirect()
                ->route('v2.meetings.index')
                ->with('success', 'Встреча успешно удалена!');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка при удалении встречи: ' . $e->getMessage());
        }
    }

    public function takePart(int $id): RedirectResponse
    {
        $userId = auth()->id();
        
        try {
            $result = $this->meetingService->toggleParticipation($id, $userId);
            
            return redirect()
                ->route('v2.meetings.show', $id)
                ->with('success', $result['message']);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка при записи на встречу: ' . $e->getMessage());
        }
    }

    public function cancelPart(int $id): RedirectResponse
    {
        return $this->takePart($id); // Логика одинаковая - toggle
    }

    public function addComment(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $userId = auth()->id();
        $comment = $request->input('comment');
        
        try {
            $result = $this->meetingService->addComment($id, $userId, $comment);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении комментария: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addLike(int $id): JsonResponse
    {
        $userId = auth()->id();
        
        try {
            $result = $this->meetingService->toggleLike($id, $userId);
            
            return response()->json([
                'success' => true,
                'action' => $result['action'],
                'count' => $result['count']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обработке лайка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addDislike(int $id): JsonResponse
    {
        $userId = auth()->id();
        
        try {
            $result = $this->meetingService->toggleDislike($id, $userId);
            
            return response()->json([
                'success' => true,
                'action' => $result['action'],
                'count' => $result['count']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обработке дизлайка: ' . $e->getMessage()
            ], 500);
        }
    }
}