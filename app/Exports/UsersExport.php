<?php

namespace App\Exports;

use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    protected $data;
    public function __construct(array $data = []){
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $teachers = [
            'Булах Л.С.' => ['days' => [Carbon::SATURDAY], 'time_start' => '09:00', 'time_end' => '17:00'],
            'Царева Т.О.' => ['days' => [Carbon::MONDAY, Carbon::WEDNESDAY, Carbon::FRIDAY], 'time_start' => '14:00', 'time_end' => '19:00'],
            'Скалаухова Т.С.' => ['days' => [Carbon::TUESDAY, Carbon::THURSDAY], 'time_start' => '12:00', 'time_end' => '16:00'],
            'Шептура А.В.' => ['days' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY], 'time_start' => '12:00', 'time_end' => '19:00'],
            'Кузьмина А.В.' => ['days' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY], 'time_start' => '10:00', 'time_end' => '15:00'],
            'Рат Д.А.' => ['days' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY], 'time_start' => '10:00', 'time_end' => '19:00'],
            'Абязова Ю.А.' => ['days' => [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY], 'time_start' => '10:00', 'time_end' => '17:00'],
        ];

        $startDate = Carbon::parse($this->data['courseStart']);
        $endDate = $startDate->copy()->addDays(intval($this->data['courseDuration'])); // Ограничение по курсу
        $currentDate = $startDate; // Первая лекция в день начала курса
        $data = [];

        $sections = Section::where('module_id',$this->data['courseName'])->select('id')->get()->toArray();
        $lessons = Lesson::whereIn('section_id',$sections)->get();

        for ($i = 0; $i < count($lessons); $i++) {
            // Выбираем случайного преподавателя
            $teacher = Arr::random(array_keys($teachers));
            $conditions = $teachers[$teacher];

            // Для первой лекции оставляем дату без изменений
            if ($i > 0) {
                $currentDate = $this->getAvailableDate($currentDate, $conditions['days'], $endDate);
            }

            // Если дата вышла за пределы курса — выходим
            if ($currentDate->greaterThan($endDate)) {
                break;
            }

            // Генерируем доступное время лекции
            $lectureTime = $this->getAvailableTime($teacher, $currentDate, $conditions['time_start'], $conditions['time_end']);

            // Формируем лекцию
            $lecture = [
                'section' => Section::where('id',$lessons[$i]['section_id'])->first()->name,
                'title' => $lessons[$i]['name'],
                'teacher' => $teacher,
                'duration' => Arr::random([60, 75]),
                'date' => $currentDate->toDateString(),
                'time' => $lectureTime,
            ];

            // Сохраняем в базу
            DB::table('lectures')->insert([
                'title' => $lecture['title'],
                'teacher' => $lecture['teacher'],
                'duration' => $lecture['duration'],
                'date' => $lecture['date'],
                'time' => $lecture['time'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $data[] = $lecture;

            // Двигаем дату вперед (но не за границы курса)
            $currentDate->addDays(rand(1, 2));
        }

        return new Collection([
            ['Раздел','Тема', 'Преподаватель', 'Длительность', 'Дата', 'Время'],
            ...$data,
        ]);
    }

    private function getAvailableDate(Carbon $date, array $allowedDays, Carbon $endDate)
    {
        do {
            $date->addDay();
        } while (!in_array($date->dayOfWeek, $allowedDays) && $date->lessThanOrEqualTo($endDate));

        return $date;
    }

    /**
     * Получает доступное время для лекции, учитывая ограничения преподавателя
     */
    private function getAvailableTime($teacher, $date, $start, $end)
    {
        do {
            $hour = rand(Carbon::parse($start)->hour, Carbon::parse($end)->hour);
            $minute = rand(0, 11) * 5;
            $time = Carbon::createFromTime($hour, $minute)->format('H:i');

            // Проверяем, занято ли это время у преподавателя
            $exists = DB::table('lectures')
                ->where('teacher', $teacher)
                ->where('date', $date->toDateString())
                ->where('time', $time)
                ->exists();
        } while ($exists); // Повторяем, если время уже занято

        return $time;
    }
}
