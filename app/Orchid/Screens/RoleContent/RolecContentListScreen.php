<?php

namespace App\Orchid\Screens\RoleContent;

use App\Models\Product;
use App\Models\RoleContent;
use App\Orchid\Layouts\Product\ProductListTable;
use App\Orchid\Layouts\RoleContent\RoleContentListTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RolecContentListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'rolesContents' => RoleContent::all()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Управление доступом к контенту';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать доступ к контенту')->modal('addContent')->method('createPrivateContent'),
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
            RoleContentListTable::class,
            Layout::modal('addContent', Layout::rows([
                Group::make([
                    Select::make('value')->required()->title('Название контента')->help('Контент который нужно ограничить')->options([
                        'Курсы' => 'Курсы',
                        'Полигон' => 'Полигон',
                        'Рег. мероприятия' => 'Рег. мероприятия',
                        'Наши встречи' => 'Наши встречи',
                        'Онлайн клубы' => 'Онлайн клубы',
                        'Видеотека' => 'Видеотека'
                    ]),
                    Select::make('level')->required()->title('Уровень подписки')->help('Каким пользователям он будет доступен')->options([
                        0 => '0 уровень',
                        1 => '1 уровень',
                        2 => '2 уровень',
                        3 => '3 уровень'
                    ])->help('Обычный пользватель без подписки - 0'),
                ]),
            ]))->title('Создание ограниченного контента')->applyButton('Создать'),
            Layout::modal('editContent', Layout::rows([
                Group::make([
                    Input::make('roleContent.id')->hidden(),
                    Select::make('roleContent.value')->required()->title('Название контента')->help('Контент который нужно ограничить')->options([
                        'Курсы' => 'Курсы',
                        'Полигон' => 'Полигон',
                        'Рег. мероприятия' => 'Рег. мероприятия',
                        'Наши встречи' => 'Наши встречи',
                        'Онлайн клубы' => 'Онлайн клубы',
                        'Видеотека' => 'Видеотека'
                    ]),
                    Select::make('roleContent.level')->required()->title('Уровень подписки')->help('Каким пользователям он будет доступен')->options([
                        0 => '0 уровень',
                        1 => '1 уровень',
                        2 => '2 уровень',
                        3 => '3 уровень'
                    ]),
                ]),
            ]))->title('Редактирование ограниченного контента')->applyButton('Редактировать')->async('asyncGetRoleContent'),
        ];
    }

    public function asyncGetRoleContent(RoleContent $roleContent)
    {
        return [
            'roleContent' => $roleContent
        ];
    }

    public function updateContent(Request $request)
    {
        RoleContent::find($request->input('roleContent.id'))->update($request->roleContent);
        Toast::success('Подписка успешно отредактирована');
    }

    public function createPrivateContent(Request $request)
    {
        $data = $request->validate([
            'value' => 'required|string',
            'level' => 'required|integer',
        ]);
        RoleContent::create($data);
        Toast::success('Ограниченный контент успешно создан');
    }
}
