<?php

namespace App\Orchid\Layouts\RoleContent;

use App\Models\Product;
use App\Models\RoleContent;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class RoleContentListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'rolesContents';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('value', 'Контент')->width('200px'),
            TD::make('level', 'Уровень доступа')->width('200px'),
            TD::make('action')->render(function (RoleContent $roleContent) {
                return ModalToggle::make('Редактировать')
                    ->modal('editContent')
                    ->method('updateContent')
                    ->modalTitle('Редактирование приватного контента')
                    ->asyncParameters([
                        'roleContent' => $roleContent->id
                    ]);
            })
        ];
    }
}
