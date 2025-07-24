<?php

namespace App\Services\V2;

use App\Models\Category;
use App\Models\ContentVideo;
use App\Repositories\V2\VideoRepository;
use App\Services\SubscriptionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VideoService
{
    public function __construct(
        private VideoRepository $videoRepository,
        private SubscriptionService $subscriptionService
    ) {}

    public function getVideoLibraryIndex(?int $userId = null): array
    {
        // Проверяем доступ к видеотеке
        if (!$userId) {
            throw new \Illuminate\Auth\AuthenticationException('Для доступа к видео необходимо авторизоваться');
        }

        $user = \App\Models\User::find($userId);
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Для доступа к видеотеке необходима активная подписка');
        }

        $categories = $this->videoRepository->getCategoriesWithVideos();

        return [
            'categories' => $categories,
            'subscriptionStatus' => $subscriptionStatus
        ];
    }

    public function getCategoryDetails(int $categoryId, ?int $userId = null): array
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        $category = $this->videoRepository->getCategoryWithDetails($categoryId);
        $videos = $this->videoRepository->getCategoryVideos($categoryId);

        return [
            'category' => $category,
            'videos' => $videos
        ];
    }

    public function getVideoDetails(int $videoId, ?int $userId = null): array
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        $video = $this->videoRepository->findVideoWithDetails($videoId);
        
        // Увеличиваем счетчик просмотров
        $this->videoRepository->incrementViews($videoId);

        // Получаем связанные видео
        $relatedVideos = $this->videoRepository->getRelatedVideos(
            $video->category_id, 
            $videoId
        );

        // SEO данные
        $title = $video->title . ' - Видео АЧПП';
        $description = 'Смотрите видео "' . $video->title . '". ';
        if ($video->description) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($video->description), 
                120
            );
        }
        $keywords = $video->title . ', видео, обучение, ' . $video->category->name . ', АЧПП';

        return compact(
            'video', 'relatedVideos', 'title', 'description', 'keywords'
        );
    }

    public function searchVideos(string $search, ?int $userId = null): array
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        $videos = $this->videoRepository->searchVideos($search);

        return [
            'videos' => $videos,
            'search' => $search
        ];
    }

    public function getCategoryVideosApi(int $categoryId, ?int $userId = null): LengthAwarePaginator
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        return $this->videoRepository->getCategoryVideos($categoryId);
    }

    public function getPopularVideos(?int $userId = null): Collection
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        return $this->videoRepository->getPopularVideos();
    }

    public function getRecentVideos(?int $userId = null): Collection
    {
        // Проверяем доступ
        $this->checkVideoAccess($userId);

        return $this->videoRepository->getRecentVideos();
    }

    public function createVideo(array $data, ?int $userId = null): ContentVideo
    {
        // Проверяем права администратора
        $this->checkAdminAccess($userId);

        return $this->videoRepository->createVideo($data);
    }

    public function updateVideo(int $id, array $data, ?int $userId = null): ContentVideo
    {
        // Проверяем права администратора
        $this->checkAdminAccess($userId);

        $video = $this->videoRepository->findVideoWithDetails($id);
        $this->videoRepository->updateVideo($video, $data);

        return $video->fresh();
    }

    public function deleteVideo(int $id, ?int $userId = null): bool
    {
        // Проверяем права администратора
        $this->checkAdminAccess($userId);

        return $this->videoRepository->deleteVideo($id);
    }

    public function getCategoryTree(): Collection
    {
        return $this->videoRepository->getCategoryTree();
    }

    public function getVideoMetaData(ContentVideo $video): array
    {
        return [
            'title' => $video->title . ' - Видео АЧПП',
            'description' => $this->generateDescription($video),
            'keywords' => $this->generateKeywords($video),
            'og_image' => $video->thumbnail_url,
            'canonical' => route('v2.video.show', $video->id),
            'duration' => $video->duration,
            'views' => $video->views
        ];
    }

    private function checkVideoAccess(?int $userId): void
    {
        if (!$userId) {
            throw new \Illuminate\Auth\AuthenticationException('Для доступа к видео необходимо авторизоваться');
        }

        $user = \App\Models\User::find($userId);
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Для доступа к видеотеке необходима активная подписка');
        }
    }

    private function checkAdminAccess(?int $userId): void
    {
        if (!$userId) {
            throw new \Illuminate\Auth\AuthenticationException('Необходима авторизация');
        }

        $user = \App\Models\User::find($userId);
        if (!$user?->isAdmin()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Недостаточно прав доступа');
        }
    }

    private function generateDescription(ContentVideo $video): string
    {
        $description = 'Смотрите видео "' . $video->title . '". ';
        
        if ($video->description) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($video->description), 
                120
            );
        }

        if ($video->duration) {
            $description .= ' Длительность: ' . $video->duration . '.';
        }

        return $description;
    }

    private function generateKeywords(ContentVideo $video): string
    {
        $keywords = [$video->title, 'видео', 'обучение', 'АЧПП'];
        
        if ($video->category) {
            $keywords[] = $video->category->name;
        }

        return implode(', ', $keywords);
    }
}