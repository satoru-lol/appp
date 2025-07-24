<?php

namespace App\Repositories\V2;

use App\Models\Club;
use App\Models\ClubDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ClubRepository
{
    public function getUpcomingClubs(): Collection
    {
        return Club::select([
                'id', 'title', 'speakers', 'theory', 'feedback', 
                'text', 'image', 'video', 'product_level', 'times'
            ])
            ->with(['clubDates' => function($query) {
                $query->select(['club_id', 'date', 'start_time', 'end_time'])
                      ->where('date', '>=', Carbon::now())
                      ->orderBy('date', 'asc')
                      ->limit(1);
            }])
            ->where('is_hidden', '!=', 1)
            ->whereHas('clubDates', function ($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->orderBy('id')
            ->get();
    }

    public function getClubsByLevel(int $level): Collection
    {
        return $this->getUpcomingClubs()
            ->where('product_level', $level)
            ->values();
    }

    public function findClubWithDetails(int $id): Club
    {
        return Club::with([
            'allClubDates' => function($query) {
                $query->orderBy('date', 'desc');
            }
        ])->findOrFail($id);
    }

    public function createClub(array $data): Club
    {
        return Club::create($data);
    }

    public function updateClub(Club $club, array $data): bool
    {
        return $club->update($data);
    }

    public function saveClubImage(Club $club, $file): void
    {
        $imageName = time() . '.' . $file->extension();
        $file->move(public_path('images'), $imageName);
        $club->update(['image' => $imageName]);
    }

    public function saveClubVideo(Club $club, $file): void
    {
        $videoName = time() . date("y-m-d") . '.' . $file->extension();
        $file->move(public_path('images'), $videoName);
        $club->update(['video' => $videoName]);
    }

    public function addClubDate(int $clubId, array $dateData): ClubDate
    {
        return ClubDate::create(array_merge($dateData, ['club_id' => $clubId]));
    }

    public function updateClubDate(int $dateId, array $dateData): bool
    {
        $clubDate = ClubDate::findOrFail($dateId);
        return $clubDate->update($dateData);
    }

    public function deleteClubDate(int $dateId): bool
    {
        return ClubDate::destroy($dateId);
    }

    public function getClubDates(int $clubId): Collection
    {
        return ClubDate::where('club_id', $clubId)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getUpcomingClubDates(int $clubId): Collection
    {
        return ClubDate::where('club_id', $clubId)
            ->where('date', '>=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get();
    }

    public function hideClub(int $clubId): bool
    {
        return Club::where('id', $clubId)->update(['is_hidden' => 1]);
    }

    public function showClub(int $clubId): bool
    {
        return Club::where('id', $clubId)->update(['is_hidden' => 0]);
    }
}