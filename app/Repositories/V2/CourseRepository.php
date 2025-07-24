<?php

namespace App\Repositories\V2;

use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseCategory;
use App\Models\ParticipantActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository
{
    public function getUpcomingCourses(?int $categoryId = null): Collection
    {
        $query = CourseContent::select([
                'id', 'course_id', 'date', 'start_time', 'end_time', 'description'
            ])
            ->with(['course:id,title,image,text,product_level'])
            ->whereHas('course')
            ->where('date', '>=', Carbon::today())
            ->orderBy('date');

        if ($categoryId) {
            $query->whereHas('course', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }

        return $query->get();
    }

    public function findCourseWithContent(int $id): CourseContent
    {
        return CourseContent::with([
            'course:id,title,image,text,product_level,feedback',
        ])->findOrFail($id);
    }

    public function getUserParticipantCourses(int $userId): array
    {
        return ParticipantActions::where('user_id', $userId)
            ->where('object_name', 'courses')
            ->pluck('object_id')
            ->toArray();
    }

    public function isUserParticipant(int $courseId, int $userId): bool
    {
        return ParticipantActions::where('user_id', $userId)
            ->where('object_name', 'courses')
            ->where('object_id', $courseId)
            ->exists();
    }

    public function addParticipant(int $courseId, int $userId): void
    {
        ParticipantActions::firstOrCreate([
            'user_id' => $userId,
            'object_name' => 'courses',
            'object_id' => $courseId,
        ]);
    }

    public function removeParticipant(int $courseId, int $userId): void
    {
        ParticipantActions::where('user_id', $userId)
            ->where('object_name', 'courses')
            ->where('object_id', $courseId)
            ->delete();
    }

    public function getCategories(): Collection
    {
        return CourseCategory::where('status', 1)->get();
    }

    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(Course $course, array $data): bool
    {
        return $course->update($data);
    }

    public function createCourseContent(int $courseId, array $data): CourseContent
    {
        return CourseContent::create(array_merge($data, ['course_id' => $courseId]));
    }

    public function updateCourseContent(CourseContent $content, array $data): bool
    {
        return $content->update($data);
    }

    public function getCoursesWithUpcomingContent(): Collection
    {
        return Course::whereHas('contents', function ($query) {
            $query->where('date', '>=', Carbon::today());
        })->with(['contents' => function ($query) {
            $query->where('date', '>=', Carbon::today())
                  ->orderBy('date', 'asc')
                  ->limit(1);
        }])->get();
    }

    public function searchCourses(string $search): Collection
    {
        return CourseContent::whereHas('course', function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('text', 'like', "%{$search}%");
        })->with('course')
          ->where('date', '>=', Carbon::today())
          ->orderBy('date')
          ->get();
    }
}