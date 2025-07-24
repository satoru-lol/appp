<?php

namespace App\Services\V2;

use App\Models\Course;
use App\Models\CourseContent;
use App\Repositories\V2\CourseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    public function getUpcomingCourses(?int $categoryId = null, ?int $userId = null): array
    {
        $courses = $this->courseRepository->getUpcomingCourses($categoryId);
        
        // Получаем курсы пользователя если авторизован
        $participantCourseIds = [];
        if ($userId) {
            $participantCourseIds = $this->courseRepository->getUserParticipantCourses($userId);
        }

        // Обогащаем данные
        $courses->each(function($course) {
            $this->formatCourseData($course);
        });

        return [
            'courses' => $courses,
            'participantCourseIds' => $participantCourseIds
        ];
    }

    public function getCourseDetails(int $id, ?int $userId = null): array
    {
        $course = $this->courseRepository->findCourseWithContent($id);
        
        // Форматируем данные
        $this->formatCourseData($course);
        
        // Проверяем участие пользователя
        $isParticipant = $userId ? 
            $this->courseRepository->isUserParticipant($id, $userId) : false;

        // SEO данные
        $title = $course->course->title . ' - Курс АЧПП';
        $description = 'Подробная информация о курсе "' . $course->course->title . '". ';
        if ($course->course->text) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($course->course->text), 
                120
            );
        }
        $keywords = $course->course->title . ', курс, обучение, психология, АЧПП';

        return compact(
            'course', 'isParticipant', 'title', 'description', 'keywords'
        );
    }

    public function subscribeToCourse(int $courseId, int $userId): array
    {
        $isParticipant = $this->courseRepository->isUserParticipant($courseId, $userId);
        
        if ($isParticipant) {
            $this->courseRepository->removeParticipant($courseId, $userId);
            $action = 'unsubscribed';
            $message = 'Вы отписались от курса';
        } else {
            $this->courseRepository->addParticipant($courseId, $userId);
            $action = 'subscribed';
            $message = 'Вы записались на курс';
        }

        return [
            'action' => $action,
            'message' => $message
        ];
    }

    public function searchCourses(string $search): Collection
    {
        $courses = $this->courseRepository->searchCourses($search);
        
        $courses->each(function($course) {
            $this->formatCourseData($course);
        });

        return $courses;
    }

    public function getCategories(): Collection
    {
        return $this->courseRepository->getCategories();
    }

    public function createCourse(array $data, ?int $userId = null): Course
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }

        return $this->courseRepository->createCourse($data);
    }

    public function updateCourse(int $id, array $data, ?int $userId = null): Course
    {
        $course = Course::findOrFail($id);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditCourse($course, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для редактирования этого курса'
            );
        }

        $this->courseRepository->updateCourse($course, $data);

        return $course->fresh();
    }

    public function createCourseContent(int $courseId, array $data, ?int $userId = null): CourseContent
    {
        $course = Course::findOrFail($courseId);
        
        // Проверяем права доступа
        if ($userId && !$this->canUserEditCourse($course, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'У вас нет прав для добавления контента к этому курсу'
            );
        }

        return $this->courseRepository->createCourseContent($courseId, $data);
    }

    public function getCourseMetaData(CourseContent $course): array
    {
        return [
            'title' => $course->course->title . ' - Курс АЧПП',
            'description' => $this->generateDescription($course),
            'keywords' => $this->generateKeywords($course),
            'og_image' => $course->course->image ? asset('images/' . $course->course->image) : null,
            'canonical' => route('courses-v2.show', $course->id)
        ];
    }

    private function formatCourseData($course): void
    {
        if ($course->date) {
            $date = Carbon::parse($course->date);
            $course->formatted_date = $date->translatedFormat('d F Y');
            $course->formatted_time = $date->format('H:i');
        }

        if ($course->start_time) {
            $course->formatted_start_time = Carbon::createFromFormat('H:i:s', $course->start_time)->format('H:i');
        }

        if ($course->end_time) {
            $course->formatted_end_time = Carbon::createFromFormat('H:i:s', $course->end_time)->format('H:i');
        }
    }

    private function canUserEditCourse(Course $course, int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        return $user?->isAdmin() || $course->user_id === $userId;
    }

    private function generateDescription(CourseContent $course): string
    {
        $description = 'Подробная информация о курсе "' . $course->course->title . '". ';
        
        if ($course->course->text) {
            $description .= \Illuminate\Support\Str::limit(
                strip_tags($course->course->text), 
                120
            );
        }

        if ($course->description) {
            $description .= ' ' . \Illuminate\Support\Str::limit(
                strip_tags($course->description), 
                50
            );
        }

        return $description;
    }

    private function generateKeywords(CourseContent $course): string
    {
        $keywords = [$course->course->title, 'курс', 'обучение', 'АЧПП', 'психология'];

        return implode(', ', $keywords);
    }
}