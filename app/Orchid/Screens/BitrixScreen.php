<?php

namespace App\Orchid\Screens;

use App\Models\BitrixData;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;


class BitrixScreen extends Screen
{
    public function query(): iterable
    {
        $totalAmount = BitrixData::where('stage','!=','C8:8')->sum('amount');
        $totalOtkaz = BitrixData::where('stage','C8:8')->sum('amount');
        $totalFact = BitrixData::where('stage','!=','C8:8')->sum('fact');
        $totalOstatok = BitrixData::where('stage','!=','C8:8')->sum('ostatok');
        return [
            'data' => BitrixData::all(),
            'totalAmount' => $totalAmount,
            'totalOtkaz' => $totalOtkaz,
            'totalFact' => $totalFact,
            'totalOstatok' => $totalOstatok,

        ];
    }

    public function name(): ?string
    {
        return 'Отчет по программе';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('data', [
               TD::make('fullName','ФИО')->width('40%'),
                TD::make('course_name','Название программы')->width('45%'),
                TD::make('amount','Сумма сделки')->width('45%'),
                TD::make('stage','Стадия сделки')
                    ->render(function ($model) {
                        $stages = [
                            'C8:1' => 'Предоплата',
                            'C8:2' => 'Оплата',
                            'C8:8' => 'Отказ от оплаты'
                        ];
                        return $stages[$model->stage] ?? $model->stage;
                    }),
                TD::make('fact','Оплачено по факту'),
                TD::make('ostatok','Остаток'),
            ]),

            Layout::rows([
                Input::make('totalAmount')
                    ->title('Итого суммы сделок:')
                    ->value(number_format(request()->get('totalAmount'), 2, '.', ' '))
                    ->disabled(),
            ]),

            Layout::rows([
                Input::make('totalOtkaz')
                    ->title('Итого суммы сделок отказанных:')
                    ->value(number_format(request()->get('totalOtkaz'), 2, '.', ' '))
                    ->disabled(),
            ]),

            Layout::rows([
                Input::make('totalFact')
                    ->title('Итого суммы сделок фактических оплат:')
                    ->value(number_format(request()->get('totalFact'), 2, '.', ' '))
                    ->disabled(),
            ]),

            Layout::rows([
                Input::make('totalOstatok')
                    ->title('Итого суммы сделок остаток:')
                    ->value(number_format(request()->get('totalOstatok'), 2, '.', ' '))
                    ->disabled(),
            ]),
        ];
    }
}
