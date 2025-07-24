<?php

namespace App\Repositories\V2;

use App\Models\Category;
use App\Models\ContentVideo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoRepository
{
    public function getCategoriesWithVideos(): Collection
    {
        return Category::select(['id', 'name', 'description', 'parent_id'])
            ->whereHas('videos')
            ->withCount('videos')
            ->orderBy('name')
            ->get();
    }

    public function getCategoryWithDetails(int $id): Category
    {
        return Category::with([
            'parent:id,name',
            'children:id,name,parent_id'
        ])->findOrFail($id);
    }

    public function getCategoryVideos(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return ContentVideo::select([
                'id', 'title', 'description', 'video_url', 'thumbnail_url', 
                'duration', 'views', 'category_id', 'created_at'
            ])
            ->where('category_id', $categoryId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findVideoWithDetails(int $id): ContentVideo
    {
        return ContentVideo::with([
            'category:id,name,parent_id'
        ])->findOrFail($id);
    }

    public function getRelatedVideos(int $categoryId, int $excludeId, int $limit = 5): Collection
    {
        return ContentVideo::select([
                'id', 'title', 'thumbnail_url', 'duration', 'views'
            ])
            ->where('category_id', $categoryId)
            ->where('id', '!=', $excludeId)
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    public function searchVideos(string $search, int $perPage = 10): LengthAwarePaginator
    {
        return ContentVideo::select([
                'id', 'title', 'description', 'thumbnail_url', 
                'duration', 'views', 'category_id'
            ])
            ->with('category:id,name')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('views', 'desc')
            ->paginate($perPage);
    }

    public function incrementViews(int $videoId): void
    {
        ContentVideo::where('id', $videoId)->increment('views');
    }

    public function getPopularVideos(int $limit = 10): Collection
    {
        return ContentVideo::select([
                'id', 'title', 'thumbnail_url', 'duration', 'views'
            ])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentVideos(int $limit = 10): Collection
    {
        return ContentVideo::select([
                'id', 'title', 'thumbnail_url', 'duration', 'views', 'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function createVideo(array $data): ContentVideo
    {
        return ContentVideo::create($data);
    }

    public function updateVideo(ContentVideo $video, array $data): bool
    {
        return $video->update($data);
    }

    public function deleteVideo(int $id): bool
    {
        return ContentVideo::destroy($id);
    }

    public function getVideosByCategory(int $categoryId): Collection
    {
        return ContentVideo::select([
                'id', 'title', 'description', 'thumbnail_url', 'duration', 'views'
            ])
            ->where('category_id', $categoryId)
            ->orderBy('title')
            ->get();
    }

    public function getCategoryTree(): Collection
    {
        return Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }
}