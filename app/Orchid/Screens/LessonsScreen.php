<?php

namespace App\Orchid\Screens;

use App\Exports\RandomData;
use App\Exports\UsersExport;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Maatwebsite\Excel\Facades\Excel;

class LessonsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'lessons' => Lesson::with('section')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Уроки';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить урок')
                ->modal('createLessonModal')
                ->method('create')
                ->icon('plus'),
            ModalToggle::make('Export')->modal('exportCourse')->method('export')->icon('download'),
            Link::make('Download Excel')
                ->href(route('users.export'))
                ->target('_blank')
                ->icon('cloud-download')
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
            Layout::table('lessons', [
                TD::make('id', '#')->width('10%'),
                TD::make('section.name','Название раздела')->width('30%'),
                TD::make('name', 'Тема')->width('40%'),
                TD::make('teacher','Преподаватель')->width('5%'),
                TD::make('minute','Длителность')->width('2%'),
                TD::make('schedule', 'Время проведения')->width('40%')
                    ->render(fn($model) =>
                        "<div class='p-2 bg-gray-100 rounded-lg text-sm font-medium'>
            <strong>Дата:</strong> " . Carbon::parse($model->date)->format('d.m.Y') . "<br>
            <strong>Время:</strong> " . Carbon::parse($model->start_time)->format('H:i') . " - " . Carbon::parse($model->end_time)->format('H:i') . "
        </div>"
                    ),
                /*TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(function (Module $module) {
                        return ModalToggle::make('Редактировать')
                                ->modal('editModal')
                                ->method('saveModal')
                                ->asyncParameters(['course' => $module->id])
                                ->modalTitle('Редактировать курс')
                            . ' ' .
                            Button::make('Удалить')
                                ->method('deleteModal')
                                ->icon('trash')
                                ->parameters(['id' => $module->id])
                                ->confirm('Вы действително хотите удалить?');
                    }),*/
            ]),

            Layout::modal('createLessonModal', [
                Layout::rows([
                    Input::make('lesson.name')
                        ->title('Тема')
                        ->required(),
                    Input::make('lesson.teacher')
                        ->title('Преподаватель')
                        ->required(),
                    Input::make('lesson.minute')
                        ->title('Длителность')
                        ->type('number')
                        ->required(),
                    Input::make('lesson.date')
                        ->title('Дата')
                        ->type('date')
                        ->required(),
                    DateTimer::make('lesson.start_time')
                        ->title('Время начала')
                        ->format('H:i')
                        ->enableTime()
                        ->disableDate()
                        ->placeholder('Введите время начала')
                        ->required(),
                    DateTimer::make('lesson.end_time')
                        ->title('Время окончание')
                        ->format('H:i')
                        ->enableTime()
                        ->disableDate()
                        ->placeholder('Введите время окончание')
                        ->required(),
                    Select::make('lesson.section_id')
                        ->title('Раздел')
                        ->fromModel(Section::class, 'name')
                        ->required(),
                ])
            ])->title('Добавить урок')->applyButton('Сохранить'),

            Layout::modal('exportCourse', [
               Layout::rows([
                  Input::make('name')->title('Название курса')->required(),
                  Input::make('duration')->title('Длителность укажите в днях')->type('number')->required(),
                  Input::make('start')->title('Укажите дата начало курса')->type('date')->required(),
                   Link::make('Download')
                       ->href(route('users.export'))
                       ->icon('cloud-download')
                       ->class('btn btn-success'),
                   ]),
            ])->title('Экспорт курса')->applyButton('Сохранить'),
        ];
    }

    public function create(Request $request){
        $data = $request->input('lesson');
        Lesson::create([
            'name' => $data['name'],
            'section_id' => $data['section_id'],
            'teacher' => $data['teacher'],
            'minute' => $data['minute'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);
        Toast::info('Урок создан!');
    }

    /*public function deleteModal($id)
    {
        $course = Module::find($id);
        $course->delete();
        Toast::info('Модуль удалено');
    }

    public function saveModal(Request $request)
    {
        $validatedData = $request->validate([
            'module.id' => 'nullable|integer|exists:module,id',
            'module.title' => 'required|string|max:255',
        ]);

        Course::where('id',$validatedData['module']['id'])->update(['name' => $validatedData['module']['name']]);
        Toast::info('Модуль сохранен');
    }

    public function asyncModule(Module $modal){
        return [
            'modal' => $modal->toArray(),
        ];
    }*/
    public function export()
    {
        Toast::info('Download starting...');
//        return redirect()->to(route('users.export'));
        return Http::get(route('users.export'));
    }
}
