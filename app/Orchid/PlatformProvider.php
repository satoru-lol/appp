<?php

declare(strict_types=1);

namespace App\Orchid;

use Illuminate\Support\Facades\Auth;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Подписки')
                ->icon('bs.basket2')
                ->title('Меню')
                ->route('platform.products'),

            Menu::make('Папки видеотеки')
                ->icon('bs.basket2')
                ->route('platform.folders'),

            Menu::make('Видео')
                ->icon('bs.basket2')
                ->route('platform.new_video'),

            /*Menu::make('Видеотека')
                ->icon('bs.basket2')
                ->route('platform.videos'),*/

            Menu::make('Клубы')
                ->icon('bs.basket2')
                ->route('platform.clubs'),

            Menu::make('Курсы')
                ->icon('bs.basket2')
                ->route('platform.courses'),

            Menu::make('Контенты курсов')
                ->icon('bs.basket2')
                ->route('platform.courseContent'),

            Menu::make('Донаты')
                ->icon('bs.basket2')
                ->route('platform.donat'),

            Menu::make('Отчет по модулям')
                ->icon('bs.basket2')
                ->route('platform.bitrix'),

            Menu::make('Отчет по эффективности')
                ->icon('bs.basket2')
                ->route('platform.bitrix_report'),

            // Menu::make('Модули')
            //     ->icon('bs.collection-fill')
            //     ->title('Расписание')
            //     ->canSee(Auth::user()->id == 1269)
            //     ->route('platform.modules'),

            // Menu::make('Раздели')
            //     ->icon('bs.collection-fill')
            //     ->canSee(Auth::user()->id == 1269)
            //     ->route('platform.sections'),

            // Menu::make('Уроки')
            //     ->icon('bs.collection-fill')
            //     ->canSee(Auth::user()->id == 1269)
            //     ->route('platform.lessons'),

            // Menu::make('Доступ к контенту')
            //     ->icon('bs.collection-fill')
            //     ->title('Управление доступом к контенту')
            //     ->route('platform.roles.contents'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
            Menu::make(__('Редактировать главный экран'))
                ->icon('bs.cursor-move')
                ->route('platform.addPart.route')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
            ItemPermission::group(__('Подписки на сайте'))
                ->addPermission('platform.products', __('Подписки'))
        ];
    }
}
