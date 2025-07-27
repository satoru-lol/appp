<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use App\Models\CourseContent;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;

class CourseContentEditScreen extends Screen
{
    public $content;

    public function query(CourseContent $content): array
    {
        return [
            'content' => $content,
        ];
    }

    public function name(): ?string
    {
        return 'Редактирование контента';
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                
                     Select::make('content.course_id')
                    ->title('Курс')
                    ->fromModel(Course::class, 'title')
                    ->empty('Выберите курс')
                    ->required(),
                Input::make('content.id')->type('hidden'),

                TextArea::make('content.section')
                    ->title('Раздел'),

                TextArea::make('content.title')
                    ->title('Тема курса')
                    ->rows(3),

                TextArea::make('content.description')
                    ->title('Описание курса')
                    ->rows(5),

                Input::make('content.speakers')
                    ->title('Спикеры'),

                Input::make('content.date')
                    ->title('Дата')
                    ->type('date')
                    ->required(),

                // DateTimer::make('content.start_time')
                //     ->title('Время начала')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->config(['time_24hr' => true])
                //     ->required(),
                          Input::make('content.start_time')
                    ->title('Время начала')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                // DateTimer::make('content.end_time')
                //     ->title('Время окончания')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->config(['time_24hr' => true])
                //     ->required(),
                    
                             Input::make('content.end_time')
                    ->title('Время окончания')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                // Input::make('content.link')
                //     ->title('Ссылка на занятие')
                //     ->type('url'),

                      Button::make('Сохранить')
                ->method('updateContent')
                ->class('btn btn-success mt-3'),
            ])
        ];
    }

    public function updateContent(Request $request)
    {
        $data = $request->validate([
            'content.id' => 'required|exists:course_contents,id',
            'content.course_id' => 'required|exists:courses,id',
            'content.title' => 'nullable|string',
            'content.description' => 'nullable|string',
            'content.date' => 'required|date',
            'content.start_time' => 'required',
            'content.end_time' => 'required',
            'content.speakers' => 'nullable|string',
            'content.section' => 'nullable|string',
        ]);

        CourseContent::findOrFail($data['content']['id'])->update($data['content']);

        Toast::info('Контент обновлён');

        return redirect()->route('platform.courseContent');
    }
}
