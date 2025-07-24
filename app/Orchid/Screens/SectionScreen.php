<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Module;
use App\Models\Section;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SectionScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'sections' => Section::with('module')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Раздели';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить раздел')
                ->modal('createSectionModal')
                ->method('create')
                ->icon('plus')
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
            Layout::table('sections', [
                TD::make('id', '#')->width('5%'),
                TD::make('module.name', 'Название модуля')->width('40%'),
                TD::make('name','Название раздела')
                    ->width('40%'),
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

            Layout::modal('createSectionModal', [
                Layout::rows([
                    Input::make('section.name')
                        ->title('Название раздела')
                        ->required(),
                    Select::make('section.module_id')
                        ->title('Модуль')
                        ->fromModel(Module::class, 'name')
                        ->required(),
                ])
            ])->title('Добавить раздел')->applyButton('Сохранить')

            /* Layout::modal('editModal', Layout::rows([
                 Input::make('module.id')->type('hidden'),
                 TextArea::make('module.name')->title('Заголовок')->type('text')->required()

             ]))->async('asyncModule')
                 ->title('Редактировать курс')
                 ->applyButton('Сохранить'),*/
        ];
    }

    public function create(Request $request){
        $data = $request->input('section');
        Section::create([
            'name' => $data['name'],
            'module_id' => $data['module_id'],
        ]);
        Toast::info('Раздел создан!');
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
}
