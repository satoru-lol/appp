<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Platform\Models\User;
use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'users';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        $subscriptionOptions = [
            'no_subscription' => 'Без подписки',
            '-1'              => 'Пробная',
            '7'               => 'Переходная',
            '6'               => 'Базовая',
            '59'              => 'Премиум',
        ];

        return [
            TD::make('firstname', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (User $user) => new Persona($user->presenter())),
                
                         TD::make('lastname', __('Фамилия'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('email', __('Email'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (User $user) => ModalToggle::make($user->email)
                    ->modal('asyncEditUserModal')
                    ->modalTitle($user->presenter()->title())
                    ->method('saveUser')
                    ->asyncParameters([
                        'user' => $user->id,
                    ])),

            TD::make('phone', 'Телефон')
              ->sort()
    ->cantHide()
    ->filter(Input::make()),

TD::make('subscription_level', 'Подписка')
    ->sort()
    ->filter(Select::make('subscription_level')->options($subscriptionOptions)->empty('Все', 'all'))
    ->render(function ($user) {
        if (empty($user->subscription_level) || $user->subscription_level === '0') {
            return 'Без подписки';
        }

        $product = \App\Models\Product::where('level', $user->subscription_level)->first();
        if (!$product) {
            // Если подписки не существует, возвращаем информативное сообщение
            return '<span class="text-warning">Неизвестная подписка (Уровень: ' . $user->subscription_level . ')</span>';
        }
        
        // Маппинг цветов для разных типов подписок
        $badgeColors = [
            '-1' => 'primary', // Пробная - синий
            '6' => 'success',  // Базовая - зеленый
            '59' => 'warning', // Премиум - желтый
            '7' => 'info',     // Переходная - голубой
        ];
        
        $color = isset($badgeColors[$user->subscription_level]) ? $badgeColors[$user->subscription_level] : 'secondary';
        
        return '<span class="badge bg-' . $color . '">' . $product->name . '</span>';
    }),

            TD::make('balance', 'Баланс')
            ->render(fn($user) => $user->balance ?? 0),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),


            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (User $user) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.systems.users.edit', $user->id)
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $user->id,
                            ]),
                    ])),
        ];
    }
}
