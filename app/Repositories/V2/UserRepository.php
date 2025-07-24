<?php

namespace App\Repositories\V2;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Transactions;
use App\Models\SubscriptionPays;
use App\Models\ParticipantActions;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function findWithRelations(int $userId): User
    {
        return User::with([
            'subscription',
            'introduction'
        ])->findOrFail($userId);
    }

    public function getUserTransactions(int $userId, int $limit = 10): Collection
    {
        return Transactions::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getUserSubscriptionPays(int $userId, int $limit = 10): Collection
    {
        return SubscriptionPays::with('product')
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getUserMeetings(int $userId, int $perPage = 5): LengthAwarePaginator
    {
        $participantActions = ParticipantActions::where('user_id', $userId)
            ->where('object_name', 'meeting')
            ->pluck('object_id');

        return Blog::whereIn('id', $participantActions)
            ->select(['id', 'name', 'date', 'image', 'fio'])
            ->paginate($perPage, ['*'], 'meetings_page');
    }

    public function updateProfile(User $user, array $data): bool
    {
        return $user->update([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'phone' => $data['phone'] ?? $user->phone,
            'email' => $data['email'],
        ]);
    }

    public function getAvatarPath(User $user): ?string
    {
        $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
        $files = glob($pattern);
        
        return !empty($files) ? basename($files[0]) : null;
    }

    public function removeAvatar(User $user): bool
    {
        $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
        $deleted = false;
        
        foreach (glob($pattern) as $file) {
            if (@unlink($file)) {
                $deleted = true;
            }
        }
        
        return $deleted;
    }

    public function saveAvatar(User $user, $file): string
    {
        // Удаляем старый аватар
        $this->removeAvatar($user);
        
        $ext = $file->getClientOriginalExtension();
        $filename = md5($user->id . $user->phone) . '.' . $ext;
        $file->move(public_path('img/avatars/'), $filename);
        
        return $filename;
    }
}