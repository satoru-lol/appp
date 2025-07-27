<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseContent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;


class CourseContentScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'content' => CourseContent::with('course')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Контент курсов';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить контент')
                ->icon('plus')
                ->modal('createContentModal')
                ->method('createContent'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('content', [
                TD::make('id', 'ID')->width('10%'),
                TD::make('course.title', 'Родительский курс')->width('15%'),
                TD::make('section','Раздел')->width('15%'),
                TD::make('title', 'Заголовок')->width('20%'),
                TD::make('description', 'Описание')->width('10%'),
                TD::make('schedule', 'Время проведения')->width('40%')
                    ->render(fn($model) =>
                        "<div class='p-2 bg-gray-100 rounded-lg text-sm font-medium'>
            <strong>Дата:</strong> " . Carbon::parse($model->date)->format('d.m.Y') . "<br>
            <strong>Время:</strong> " . Carbon::parse($model->start_time)->format('H:i') . " - " . Carbon::parse($model->end_time)->format('H:i') . "
        </div>"
                    ),
                TD::make('speakers', 'Спикеры')->width('2%'),
       TD::make('Действия')
    ->align(TD::ALIGN_CENTER)
    ->width('1%')
    ->render(function (CourseContent $content) {
                return
            Link::make('Редактировать')
                ->route('platform.course-content.edit', $content->id)
                ->icon('pencil')
                ->class('btn btn-warning btn-sm me-2') .
            Button::make('Удалить')
                ->method('deleteCategory')
                ->icon('trash')
                ->confirm('Вы действительно хотите удалить?')
                ->parameters(['id' => $content->id]);
    }),

            ]),
            Layout::modal('createContentModal', [
                Layout::rows([
                    Input::make('content.id')->hidden(),
                    TextArea::make('content.section')->title('Раздел')->rows(3),
                    TextArea::make('content.title')->title('Тема курса')->rows(5),
                    TextArea::make('content.description')->title('Описание курса')->rows(10),
                    Input::make('content.speakers')->title('Спикеры')->required(),
                    Input::make('content.date')
                        ->title('Дата')
                        ->type('date')
                        ->placeholder('Введите дату')
                        ->required(),
                    DateTimer::make('content.start_time')
                        ->title('Время начала')
                        ->format('H:i')
                        ->enableTime()
                        ->disableDate()
                        ->placeholder('Введите время начала')
                        ->required(),
                    DateTimer::make('content.end_time')
                        ->title('Время окончания')
                        ->format('H:i')
                        ->enableTime()
                        ->disableDate()
                        ->placeholder('Введите время окончания')
                        ->required(),
                    Select::make('content.course_id')
                        ->title('Курс')
                        ->fromModel(Course::class, 'title')
                        ->empty('Выбирайте курс')
                        ->required(),
                ])
            ])->title('Добавить контент')->applyButton('Сохранить'),
        ];
    }

    public function createContent(Request $request): void
    {
        $content = Course::where('id', $request['content']['course_id'])->first();
        if (!$content) {
            throw new \Exception('Курс не найден.');
        }
        CourseContent::create([
            'course_id' => $request['content']['course_id'],
            'title' => $request['content']['title'],
            'description' => $request['content']['description'],
            'date' => $request['content']['date'],
            'start_time' => $request['content']['start_time'],
            'end_time' => $request['content']['end_time'],
            'speakers' => $request['content']['speakers'],
            'section' => $request['content']['section'],
        ]);
        Toast::info('Контент успешно добавлен');
    }
    public function deleteCategory(Request $request){
        $category = CourseContent::find($request->get('id'))->delete();
        Toast::info('Контент удален');
    }

}
