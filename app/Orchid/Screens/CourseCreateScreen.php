<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CourseCreateScreen extends Screen
{
    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создать курс';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'products' => Product::all(),
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
            Layout::rows([
                TextArea::make('course.title')
                    ->title('Название курса')
                    ->rows(3)
                    ->required(),
                
                TextArea::make('course.feedback')
                    ->title('Ссылка на Zoom')
                    ->rows(2)
                    ->placeholder('Например: https://us06web.zoom.us/j/82514095169')
                    ->required(),

                Button::make('Сохранить')
                    ->method('saveCourse')
                    ->icon('check')
                    ->class('btn btn-success')
            ]),
        ];
    }

    /**
     * Handle saving the course.
     *
     * @param Request $request
     */
    public function saveCourse(Request $request)
    {
        $data = $request->input('course');
        
        // Получаем ID пробной подписки
        $trialProduct = Product::where('level', -1)->first();
        $trialProductId = $trialProduct ? $trialProduct->id : 1;
        
        // Получаем первую категорию из базы данных
        $firstCategory = CourseCategory::first();
        $categoryId = $firstCategory ? $firstCategory->id : 1;
        
        try {
            // Начинаем транзакцию
            DB::beginTransaction();
            
            // Создаем новый курс с данными по умолчанию для всех необходимых полей
            $course = Course::create([
                // Обязательные поля (NOT NULL)
                'title' => $data['title'],
                'course_category_id' => $categoryId,
                'image' => '', // NOT NULL поле без значения по умолчанию
                'speakers' => '', // NOT NULL поле без значения по умолчанию
                'theory' => '', // NOT NULL поле без значения по умолчанию
                'practice' => '', // NOT NULL поле без значения по умолчанию
                'status' => 0, // По умолчанию 0
                'views' => 0, // По умолчанию 0
                
                // Необязательные поля (NULL допустим)
                'times' => json_encode([]), // Может быть NULL, но установим пустой JSON
                'product_level' => $trialProductId,
                'is_hidden' => 0,
                'is_polygon' => 0,
            ]);
            
            // Отдельно обновляем поле feedback, так как с ним могут быть проблемы
            $course->feedback = $data['feedback'];
            $course->save();
            
            // Завершаем транзакцию
            DB::commit();
            
            // Показываем сообщение об успехе и перенаправляем пользователя
            Toast::info('Курс успешно создан');
            return redirect()->route('platform.courses');
            
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            DB::rollBack();
            
            // Выводим сообщение об ошибке
            Toast::error('Ошибка при создании курса: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
} 