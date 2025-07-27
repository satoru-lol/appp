<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserPsyLanceLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [

           Select::make('user.psy_lance')
                ->options([
                    null => __('Категории нет'),  
                    'practitioner' => __('Практикант-психолог'), 
                    'client' => __('Клиент'), 
                    'supervisor' => __('Супервизор')  
                ])
                 ->placeholder(__('Выберите категорию'))
                ->title(__('Категория'))
        ];
    }
}
