<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class CategoriesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'categories' => Category::with('parent', 'videos')->get(), // Assuming Category model exists'
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Папки видеотеки';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить папку')
                ->icon('plus')
                ->modal('createCategoryModal')
                ->method('createCategory'),
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
            Layout::table('categories', [
                TD::make('name', 'Name')
                    ->width('35%')
                    ->render(function (Category $category) {
                        return  $category->name;
                    }),

                TD::make('parent', 'Parent Category')
                    ->render(function (Category $category) {
                        return Str::markdown($category->parent?->name ?? 'No Parent');
                    }),

                TD::make('videos', 'Videos')
                    ->render(function (Category $category) {
                        return $category->videos->count();
                    }),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(function (Category $category) {
                        return Button::make('Удалить')
                                ->method('deleteCategory')
                                ->icon('trash')
                                ->confirm('Вы действително хотите удалить?')
                                ->parameters(['id' => $category->id]);
                    }),
            ]),
            Layout::modal('createCategoryModal', [
                Layout::rows([
                    Input::make('category.id')->hidden(),
                    Input::make('category.name')->title('Category Name')->required(),
                    Select::make('category.parent_id')
                        ->title('Родительская папка')
                        ->fromModel(Category::class, 'name')
                        ->empty('Нету родительского папки'),
                ])
            ])->title('Добавить папку')->applyButton('Сохранить'),
        ];
    }

    public function createCategory(Request $request)
    {
        $data = $request->all();

        Category::create([
            'name' => $data['category']['name'],
            'parent_id' => $data['category']['parent_id'] ?? null,
        ]);
        Toast::success('Папка добавлен');
    }

    public function deleteCategory(Request $request){
        $category = Category::find($request->get('id'));
        $category->videos()->delete();
        $category->children()->delete();
        if($category->delete()){
            Toast::success('Папка удалена');
        } else {
            Toast::error('Ошибка при удалении папки');
        }
    }
}
