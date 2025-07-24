<?php

namespace App\Orchid\Screens\Product;

use App\Models\Product;
use App\Models\ProductPermission;
use App\Orchid\Layouts\Product\ProductListTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Validation\Rule;

class ProductListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'products' => Product::with('getPermissions')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Подписки';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать подписку')->modal('addProduct')->method('createProduct'),
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
            ProductListTable::class,
            Layout::modal('addProduct', Layout::rows([
                Group::make([
                    Input::make('name')->required()->title('Название подписки'),
                    Input::make('price')->required()->title('Цена'),
                ]),
                CheckBox::make('video')->title('Доступ к видеотекам')->sendTrueOrFalse(),
                CheckBox::make('club')->title('Доступ к онлайн клубам')->sendTrueOrFalse(),
                CheckBox::make('course')->title('Доступ к онлайн курсам')->sendTrueOrFalse(),
                TextArea::make('description')->required()->title('Описание подписки')->help('Будет видно на главной странице'),
            ]))->title('Создание подписки')->applyButton('Создать'),

            Layout::modal('editProduct', Layout::rows([
                Group::make([
                    Input::make('product.name')->required()->title('Название подписки'),
                    Input::make('product.price')->required()->title('Цена'),
                    Input::make('product.id')->hidden()
                ]),
                CheckBox::make('product.getPermissions.video')->title('Доступ к видеотекам')->sendTrueOrFalse(),
                CheckBox::make('product.getPermissions.club')->title('Доступ к онлайн клубам')->sendTrueOrFalse(),
                CheckBox::make('product.getPermissions.course')->title('Доступ к онлайн курсам')->sendTrueOrFalse(),
                TextArea::make('product.description')->required()->title('Описание подписки')->help('Будет видно на главной странице'),
            ]))->title('Создание подписки')->applyButton('Отредактировать')->async('asyncGetProduct')
        ];
    }

    public function asyncGetProduct(Product $product)
    {
        return [
            'product' => $product
        ];
    }

    public function createProduct(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:10|min:2',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255|min:2',
            'video' => 'nullable|boolean',
            'club' => 'nullable|boolean',
            'course' => 'nullable|boolean',
        ]);

        // Генерируем уникальный уровень автоматически
        $maxLevel = Product::max('level') + 1;

        $productID = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'level' => $maxLevel, // Автоматически присваиваем уникальный уровень
        ]);

        ProductPermission::create([
            'product_id' => $productID->id,
            'video' => $data['video'],
            'club' => $data['club'],
            'course' => $data['course'],
        ]);
        Toast::success('Подписка создана');
    }

    public function updateProduct(Request $request)
    {
        $productId = $request->input('product.id');
        $data = $request->product;
        unset($data['level']);
        Product::find($productId)->update($data);
        Toast::success('Подписка успешно отредактирована');
    }
}
