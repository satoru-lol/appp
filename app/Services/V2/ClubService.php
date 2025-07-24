<?php

namespace App\Services\V2;

use App\Models\Club;
use App\Repositories\V2\ClubRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClubService
{
    public function __construct(
        private ClubRepository $clubRepository
    ) {}

    public function getUpcomingClubs(): Collection
    {
        $clubs = $this->clubRepository->getUpcomingClubs();
        
        // Обогащаем данные форматированными датами
        return $clubs->map(function($club) {
            $this->formatClubDates($club);
            return $club;
        });
    }

    public function getClubsByUserLevel(int $userLevel): Collection
    {
        $clubs = $this->clubRepository->getClubsByLevel($userLevel);
        
        return $clubs->map(function($club) {
            $this->formatClubDates($club);
            return $club;
        });
    }

    public function getClubDetails(int $id): array
    {
        $club = $this->clubRepository->findClubWithDetails($id);
        
        // Форматируем даты
        $this->formatClubDates($club);
        
        // Получаем ближайшую дату
        $nearestDate = $club->allClubDates
            ->where('date', '>=', Carbon::now())
            ->first();
            
        // SEO данные
        $title = $club->title . ' - Онлайн-клуб АЧПП';
        $description = 'Присоединяйтесь к онлайн-клубу "' . $club->title . '". ';
        if ($club->text) {
            $description .= \Illuminate\Support\Str::limit(strip_tags($club->text), 120);
        }
        $keywords = $club->title . ', онлайн-клуб, ' . $club->speakers . ', АЧПП, психология';

        return compact('club', 'nearestDate', 'title', 'description', 'keywords');
    }

    public function createClub(array $data, ?int $userId = null): Club
    {
        // Добавляем пользователя если передан
        if ($userId) {
            $data['user_id'] = $userId;
        }

        $club = $this->clubRepository->createClub($data);

        // Сохраняем изображение если есть
        if (isset($data['image']) && $data['image']) {
            $this->clubRepository->saveClubImage($club, $data['image']);
        }

        // Сохраняем видео если есть
        if (isset($data['video']) && $data['video']) {
            $this->clubRepository->saveClubVideo($club, $data['video']);
        }

        return $club;
    }

    public function updateClub(int $id, array $data, ?int $userId = null): Club
    {
        $club = $this->clubRepository->findClubWithDetails($id);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditClub($club, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для редактирования этого клуба'
            );
        }

        $this->clubRepository->updateClub($club, $data);

        // Обновляем изображение если есть
        if (isset($data['image']) && $data['image']) {
            $this->clubRepository->saveClubImage($club, $data['image']);
        }

        // Обновляем видео если есть
        if (isset($data['video']) && $data['video']) {
            $this->clubRepository->saveClubVideo($club, $data['video']);
        }

        return $club->fresh();
    }

    public function toggleClubVisibility(int $id, string $action, ?int $userId = null): bool
    {
        $club = $this->clubRepository->findClubWithDetails($id);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditClub($club, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для изменения видимости этого клуба'
            );
        }

        if ($action === 'hide') {
            return $this->clubRepository->hideClub($id);
        } else {
            return $this->clubRepository->showClub($id);
        }
    }

    public function addClubDate(int $clubId, array $dateData, ?int $userId = null): \App\Models\ClubDate
    {
        $club = $this->clubRepository->findClubWithDetails($clubId);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditClub($club, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для добавления дат к этому клубу'
            );
        }

        return $this->clubRepository->addClubDate($clubId, $dateData);
    }

    public function updateClubDate(int $dateId, array $dateData, ?int $userId = null): bool
    {
        $clubDate = \App\Models\ClubDate::findOrFail($dateId);
        $club = $this->clubRepository->findClubWithDetails($clubDate->club_id);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditClub($club, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для редактирования дат этого клуба'
            );
        }

        return $this->clubRepository->updateClubDate($dateId, $dateData);
    }

    public function deleteClubDate(int $dateId, ?int $userId = null): bool
    {
        $clubDate = \App\Models\ClubDate::findOrFail($dateId);
        $club = $this->clubRepository->findClubWithDetails($clubDate->club_id);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditClub($club, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для удаления дат этого клуба'
            );
        }

        return $this->clubRepository->deleteClubDate($dateId);
    }

    public function getClubMetaData(Club $club): array
    {
        return [
            'title' => $club->title . ' - Онлайн-клуб АЧПП',
            'description' => $this->generateDescription($club),
            'keywords' => $this->generateKeywords($club),
            'og_image' => $club->image ? asset('images/' . $club->image) : null,
            'canonical' => route('v2.club.show', $club->id)
        ];
    }

    private function formatClubDates(Club $club): void
    {
        if ($club->clubDates && $club->clubDates->isNotEmpty()) {
            $near = $club->clubDates->first();
            if ($near && !empty($near->date)) {
                $near->start_time = Carbon::createFromFormat('H:i:s', $near->start_time)->format('H:i');
                $date = Carbon::parse($near->date);
                $club->formatted_date = $date->translatedFormat('d F Y, H:i');
            }
        }
    }

    private function canUserEditClub(Club $club, int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        // Админы могут редактировать любые клубы
        // Создатели могут редактировать свои клубы
        return $user?->isAdmin() || $club->user_id === $userId;
    }

    private function generateDescription(Club $club): string
    {
        $description = 'Присоединяйтесь к онлайн-клубу "' . $club->title . '". ';
        
        if ($club->text) {
            $description .= \Illuminate\Support\Str::limit(strip_tags($club->text), 120);
        }

        if ($club->speakers) {
            $description .= ' Спикеры: ' . $club->speakers . '.';
        }

        return $description;
    }

    private function generateKeywords(Club $club): string
    {
        $keywords = [$club->title, 'онлайн-клуб', 'АЧПП', 'психология'];
        
        if ($club->speakers) {
            $keywords[] = $club->speakers;
        }

        if ($club->theory) {
            $keywords[] = $club->theory;
        }

        return implode(', ', $keywords);
    }
}