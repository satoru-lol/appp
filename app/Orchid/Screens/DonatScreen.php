<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\Donat;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DonatScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $donat = Donat::with(['club', 'course', 'user'])->paginate();
        return [
            'donat' => $donat,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Донаты';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('donat', [
                TD::make('id', '#')->width('5%'),
                TD::make('amount', 'Сумма')
                    ->width('10%'),
                TD::make('reason', 'За что донат?')
                    ->width('20%'),
                TD::make('club','Название клуба')
                    ->width('40%')
                    ->render(fn($donat) => $donat->club?->title ?? '--'),
                TD::make('course','Название курса')
                    ->width('40%')
                    ->render(fn($donat) => $donat->course?->title ?? '--'),
                TD::make('user','Имя отправителья')
                    ->width('40%')
                    ->render(fn($donat) => $donat->user?->firstname.' '.$donat->user?->lastname ?? '--'),
            ])
        ];
    }

}
