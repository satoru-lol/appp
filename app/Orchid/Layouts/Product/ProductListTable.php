<?php

namespace App\Orchid\Layouts\Product;

use App\Models\Product;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'products';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Название')->width('200px'),
            TD::make('price', 'Цена')->width('200px'),
            // TD::make('level', 'Уровень подписки')->width('200px'), // Скрыто для предотвращения случайного изменения уровня
            TD::make('permissions', 'Permissions')
                ->render(function ($model) {
                    if (!$model->getPermissions) {
                        return 'Нету доступов';
                    }
                    return implode('<br>',[
                        CheckBox::make("permissions.video[{$model->id}]")
                            ->value($model->getPermissions->video)
                            ->sendTrueOrFalse()
                            ->title('Доступ к видеотекам'),

                        CheckBox::make("permissions.club[{$model->id}]")
                            ->value($model->getPermissions->club)
                            ->sendTrueOrFalse()
                            ->title('Доступ к онлайн клубам'),

                        CheckBox::make("permissions.course[{$model->id}]")
                            ->value($model->getPermissions->course)
                            ->sendTrueOrFalse()
                            ->title('Доступ к онлайн курсам'),
                    ]);
                }),
            TD::make('action')->render(function (Product $product) {
                return ModalToggle::make('Редактировать')
                    ->modal('editProduct')
                    ->method('updateProduct')
                    ->modalTitle('Редактирование подписки')
                    ->asyncParameters([
                        'product' => $product->id
                    ]);
            })
        ];
    }
}
