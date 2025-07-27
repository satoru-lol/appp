<?php

namespace App\Orchid\Screens;

use App\Models\BitrixReport;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;


class BitrixReportScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'data' => BitrixReport::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Отчет по эффективности';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('data', [
               TD::make('name','Список модулей')->width('40%'),
                TD::make('clock','Часы')->width('45%'),
                TD::make('coef','Коэффициент')->width('45%'),
                TD::make('stage','Стоимость')
                    ->render(function ($model) {
                        return ($model->all_amount - $model->otkaz) * $model->coef;
                    }),
            ]),
        ];
    }
}
