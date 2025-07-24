<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CourseScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'courses' => Course::all(),
            'products' => Product::all()->keyBy('id'),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Курсы';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Создать курс')
                ->icon('plus')
                ->route('platform.course.create')
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
            Layout::table('courses', [
                TD::make('id', '#')->width('5%'),
                TD::make('title', 'Название')
                    ->width('70%'),
                TD::make('feedback', 'Zoom')
                    ->render(function (Course $course) {
                        if (empty($course->feedback)) {
                            return '';
                        }
                        
                        // Сокращаем ссылку для отображения
                        $url = $course->feedback;
                        if (strlen($url) > 30) {
                            $url = substr($url, 0, 27) . '...';
                        }
                        
                        return '<a href="' . $course->feedback . '" target="_blank">' . $url . '</a>';
                    })
                    ->width('15%'),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->width('10%')
                    ->render(function (Course $course) {
                        return ModalToggle::make('Редактировать')
                                ->modal('editCourseModal')
                                ->method('saveCourse')
                                ->asyncParameters(['course' => $course->id])
                                ->modalTitle('Редактировать курс')
                            . ' ' .
                            Button::make('Удалить')
                                ->method('deleteCourse')
                                ->icon('trash')
                                ->parameters(['id' => $course->id])
                                ->confirm('Вы действительно хотите удалить?');
                    }),
            ]),

            Layout::modal('editCourseModal', Layout::rows([
                Input::make('course.id')->type('hidden'),
                TextArea::make('course.title')->title('Название курса')->type('text')->required(),
                TextArea::make('course.feedback')->title('Ссылка на Zoom')->type('text')->required(),

            ]))->async('asyncCourse')
                ->title('Редактировать курс')
                ->applyButton('Сохранить'),
        ];
    }

    public function deleteCourse($id)
    {
        $course = Course::find($id);
        $course->delete();
        Toast::info('Курс удален');
    }

    public function saveCourse(Request $request)
    {
        $validatedData = $request->validate([
            'course.id' => 'nullable|integer|exists:courses,id',
            'course.title' => 'required|string|max:255',
            'course.feedback' => 'required|string|max:255',
        ]);

        Course::where('id',$validatedData['course']['id'])->update(['title' => $validatedData['course']['title'], 'feedback' => $validatedData['course']['feedback']]);
        Toast::info('Курс сохранен');
    }

    public function asyncCourse(Course $course){
        return [
            'course' => $course->toArray(),
        ];
    }
}
