<?php

namespace App\Services\V2;

use App\Models\Blog;
use App\Repositories\V2\MeetingRepository;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MeetingService
{
    public function __construct(
        private MeetingRepository $meetingRepository
    ) {}

    public function getUpcomingMeetings(int $perPage = 15): LengthAwarePaginator
    {
        $meetings = $this->meetingRepository->getUpcomingMeetings($perPage);
        
        // Обогащаем данные
        foreach ($meetings as &$meeting) {
            $meeting->format = $meeting->format;
            $meeting->formattedDate = Carbon::parse($meeting->date)
                ->translatedFormat('d F Y \г. \в H:i');
            $meeting->participants_count = $this->meetingRepository
                ->getParticipantsCount($meeting->id);
            $meeting->comments_count = $this->meetingRepository
                ->getCommentsCount($meeting->id);
        }

        return $meetings;
    }

    public function getPreviousMeetings(int $perPage = 15): LengthAwarePaginator
    {
        $meetings = $this->meetingRepository->getPreviousMeetings($perPage);
        
        // Обогащаем данные
        foreach ($meetings as &$meeting) {
            $meeting->format = $meeting->format;
            $meeting->formattedDate = Carbon::parse($meeting->date)
                ->translatedFormat('d F Y \г.');
            $meeting->participants_count = $this->meetingRepository
                ->getParticipantsCount($meeting->id);
            $meeting->comments_count = $this->meetingRepository
                ->getCommentsCount($meeting->id);
        }

        return $meetings;
    }

    public function getMeetingDetails(int $id, ?int $userId = null): array
    {
        $meeting = $this->meetingRepository->findMeetingWithDetails($id);
        
        // Увеличиваем счетчик просмотров для авторизованных пользователей
        if ($userId) {
            $this->meetingRepository->incrementViews($id, $userId);
        }

        $format = $meeting->format;
        $formattedDate = Carbon::parse($meeting->date)
            ->translatedFormat('d F Y \г. \в H:i');

        // Дополнительные данные
        $participantsCount = $this->meetingRepository->getParticipantsCount($id);
        $commentsCount = $this->meetingRepository->getCommentsCount($id);
        $isParticipant = $userId ? $this->meetingRepository->isUserParticipant($id, $userId) : false;

        // SEO данные
        $title = $meeting->name . ' - Встреча АЧПП';
        $description = 'Приглашаем на встречу "' . $meeting->name . '". ';
        if ($meeting->blogContent) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($meeting->blogContent->text), 
                120
            );
        }
        $keywords = $meeting->name . ', встреча, семинар, ' . $meeting->fio . ', АЧПП, психология';

        return compact(
            'meeting', 'format', 'formattedDate', 'participantsCount', 
            'commentsCount', 'isParticipant', 'title', 'description', 'keywords'
        );
    }

    public function createMeeting(array $data, int $userId): Blog
    {
        $meeting = $this->meetingRepository->createMeeting($data, $userId);

        // Сохраняем изображение если есть
        if (isset($data['image']) && $data['image']) {
            $this->meetingRepository->saveImage($meeting, $data['image'], $userId);
        }

        return $meeting;
    }

    public function updateMeeting(int $id, array $data, int $userId): Blog
    {
        $meeting = $this->meetingRepository->findMeetingWithDetails($id);
        
        // Проверяем права доступа
        if (!$this->canUserEditMeeting($meeting, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для редактирования этой встречи'
            );
        }

        $this->meetingRepository->updateMeeting($meeting, $data);

        // Обновляем изображение если есть
        if (isset($data['image']) && $data['image']) {
            $this->meetingRepository->saveImage($meeting, $data['image'], $userId);
        }

        return $meeting->fresh();
    }

    public function deleteMeeting(int $id, int $userId): bool
    {
        $meeting = $this->meetingRepository->findMeetingWithDetails($id);
        
        // Проверяем права доступа
        if (!$this->canUserEditMeeting($meeting, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для удаления этой встречи'
            );
        }

        return $meeting->delete();
    }

    public function toggleParticipation(int $meetingId, int $userId): array
    {
        $isParticipant = $this->meetingRepository->isUserParticipant($meetingId, $userId);
        
        if ($isParticipant) {
            $this->meetingRepository->removeParticipant($meetingId, $userId);
            $action = 'removed';
            $message = 'Вы отменили участие во встрече';
        } else {
            $this->meetingRepository->addParticipant($meetingId, $userId);
            $action = 'added';
            $message = 'Вы записались на встречу';
        }

        $participantsCount = $this->meetingRepository->getParticipantsCount($meetingId);

        return [
            'action' => $action,
            'message' => $message,
            'participants_count' => $participantsCount
        ];
    }

    public function addComment(int $meetingId, int $userId, string $comment): array
    {
        $commentModel = $this->meetingRepository->addComment($meetingId, $userId, $comment);
        
        return [
            'success' => true,
            'message' => 'Комментарий добавлен',
            'comment' => $commentModel->load('user')
        ];
    }

    public function toggleLike(int $meetingId, int $userId): array
    {
        return $this->meetingRepository->toggleLike($meetingId, $userId);
    }

    public function toggleDislike(int $meetingId, int $userId): array
    {
        return $this->meetingRepository->toggleDislike($meetingId, $userId);
    }

    public function getAllFormats(): Collection
    {
        return $this->meetingRepository->getAllFormats();
    }

    private function canUserEditMeeting(Blog $meeting, int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        return $meeting->user_id === $userId || $user?->isAdmin();
    }

    public function getMeetingMetaData(Blog $meeting): array
    {
        return [
            'title' => $meeting->name . ' - Встреча АЧПП',
            'description' => $this->generateDescription($meeting),
            'keywords' => $this->generateKeywords($meeting),
            'og_image' => $meeting->image ? asset('img/blog/' . $meeting->image) : null,
            'canonical' => route('v2.meetings.show', $meeting->id)
        ];
    }

    private function generateDescription(Blog $meeting): string
    {
        $description = 'Приглашаем на встречу "' . $meeting->name . '". ';
        
        if ($meeting->blogContent && $meeting->blogContent->text) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($meeting->blogContent->text), 
                120
            );
        }

        if ($meeting->fio) {
            $description .= ' Спикер: ' . $meeting->fio . '.';
        }

        return $description;
    }

    private function generateKeywords(Blog $meeting): string
    {
        $keywords = [$meeting->name, 'встреча', 'семинар', 'АЧПП', 'психология'];
        
        if ($meeting->fio) {
            $keywords[] = $meeting->fio;
        }

        if ($meeting->format) {
            $keywords[] = $meeting->format->name;
        }

        return implode(', ', $keywords);
    }
}