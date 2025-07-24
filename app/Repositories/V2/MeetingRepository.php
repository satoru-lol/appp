<?php

namespace App\Repositories\V2;

use App\Models\Blog;
use App\Models\BlogContent;
use App\Models\BlogComment;
use App\Models\MeetingFormat;
use App\Models\ParticipantActions;
use App\Models\ObjectViewControl;
use App\Models\Like;
use App\Models\Dislike;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MeetingRepository
{
    public function getUpcomingMeetings(int $perPage = 15): LengthAwarePaginator
    {
        return Blog::select([
                'id', 'name', 'date', 'fio', 'format_id', 'image', 
                'views', 'amount', 'quantity', 'place'
            ])
            ->with(['format:id,name'])
            ->where('status', 1)
            ->where('is_meeting', true)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->paginate($perPage);
    }

    public function getPreviousMeetings(int $perPage = 15): LengthAwarePaginator
    {
        return Blog::select([
                'id', 'name', 'date', 'fio', 'format_id', 'image', 
                'views', 'amount', 'quantity', 'place'
            ])
            ->with(['format:id,name'])
            ->where('status', 1)
            ->where('is_meeting', true)
            ->where('date', '<', Carbon::today())
            ->orderByDesc('date')
            ->paginate($perPage);
    }

    public function findMeetingWithDetails(int $id): Blog
    {
        return Blog::with([
            'blogContent:id,text',
            'format:id,name'
        ])->where('status', 1)
          ->where('is_meeting', true)
          ->findOrFail($id);
    }

    public function createMeeting(array $data, int $userId): Blog
    {
        $blogContent = BlogContent::create([
            'text' => $data['text'] ?? ''
        ]);

        $meeting = Blog::create([
            'name' => $data['name'],
            'date' => $data['date'],
            'fio' => $data['fio'],
            'format_id' => $data['format_id'],
            'feedback' => $data['feedback'] ?? null,
            'text' => $data['text'] ?? null,
            'amount' => $data['amount'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'place' => $data['place'] ?? null,
            'is_meeting' => true,
            'status' => 1,
            'user_id' => $userId,
            'blog_content_id' => $blogContent->id,
        ]);

        return $meeting;
    }

    public function updateMeeting(Blog $meeting, array $data): bool
    {
        // Обновляем контент
        if ($meeting->blogContent) {
            $meeting->blogContent->update([
                'text' => $data['text'] ?? ''
            ]);
        }

        // Обновляем основные данные
        return $meeting->update([
            'name' => $data['name'],
            'date' => $data['date'],
            'fio' => $data['fio'],
            'format_id' => $data['format_id'],
            'feedback' => $data['feedback'] ?? $meeting->feedback,
            'amount' => $data['amount'] ?? $meeting->amount,
            'quantity' => $data['quantity'] ?? $meeting->quantity,
            'place' => $data['place'] ?? $meeting->place,
        ]);
    }

    public function saveImage(Blog $meeting, $file, int $userId): void
    {
        $imageName = md5($userId . time()) . '.' . $file->extension();
        $file->move(public_path('img/blog'), $imageName);
        $meeting->update(['image' => $imageName]);
    }

    public function getParticipantsCount(int $meetingId): int
    {
        return ParticipantActions::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->count();
    }

    public function getCommentsCount(int $meetingId): int
    {
        return BlogComment::where('blog_id', $meetingId)->count();
    }

    public function isUserParticipant(int $meetingId, int $userId): bool
    {
        return ParticipantActions::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->where('user_id', $userId)
            ->exists();
    }

    public function addParticipant(int $meetingId, int $userId): void
    {
        ParticipantActions::firstOrCreate([
            'object_id' => $meetingId,
            'object_name' => 'meeting',
            'user_id' => $userId,
        ]);
    }

    public function removeParticipant(int $meetingId, int $userId): void
    {
        ParticipantActions::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->where('user_id', $userId)
            ->delete();
    }

    public function incrementViews(int $meetingId, int $userId): void
    {
        $viewExists = ObjectViewControl::where('user_id', $userId)
            ->where('object_name', 'meeting')
            ->where('object_id', $meetingId)
            ->exists();

        if (!$viewExists) {
            ObjectViewControl::create([
                'user_id' => $userId,
                'object_name' => 'meeting',
                'object_id' => $meetingId,
            ]);

            Blog::where('id', $meetingId)->increment('views');
        }
    }

    public function addComment(int $meetingId, int $userId, string $comment): BlogComment
    {
        return BlogComment::create([
            'blog_id' => $meetingId,
            'user_id' => $userId,
            'comment' => $comment,
            'status' => 1,
        ]);
    }

    public function toggleLike(int $meetingId, int $userId): array
    {
        $like = Like::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $action = 'removed';
        } else {
            // Удаляем дизлайк если есть
            Dislike::where('object_id', $meetingId)
                ->where('object_name', 'meeting')
                ->where('user_id', $userId)
                ->delete();

            Like::create([
                'object_id' => $meetingId,
                'object_name' => 'meeting',
                'user_id' => $userId,
            ]);
            $action = 'added';
        }

        $likesCount = Like::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->count();

        return ['action' => $action, 'count' => $likesCount];
    }

    public function toggleDislike(int $meetingId, int $userId): array
    {
        $dislike = Dislike::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->where('user_id', $userId)
            ->first();

        if ($dislike) {
            $dislike->delete();
            $action = 'removed';
        } else {
            // Удаляем лайк если есть
            Like::where('object_id', $meetingId)
                ->where('object_name', 'meeting')
                ->where('user_id', $userId)
                ->delete();

            Dislike::create([
                'object_id' => $meetingId,
                'object_name' => 'meeting',
                'user_id' => $userId,
            ]);
            $action = 'added';
        }

        $dislikesCount = Dislike::where('object_id', $meetingId)
            ->where('object_name', 'meeting')
            ->count();

        return ['action' => $action, 'count' => $dislikesCount];
    }

    public function getAllFormats(): Collection
    {
        return MeetingFormat::all();
    }
}