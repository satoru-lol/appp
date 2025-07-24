<?php

namespace App\Orchid\Screens\Clubs;

use App\Models\Club;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;  // Add the Button import
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CreateClubScreen extends Screen
{
    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создать клуб';
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
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];  // No data to fetch
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('club.title')
                    ->title('Название клуба')
                    ->required(),
                    
                    \Orchid\Screen\Fields\Quill::make('club.text')
    ->title('Описание клуба')
    ->required(),

                Input::make('club.feedback')
                    ->title('Ссылка на Zoom или другой сервис')
                    ->required(),

                Button::make('Сохранить')
                    ->method('saveClub')  
                    ->icon('check')     
                    ->class('btn btn-success') 
            ]),
        ];
    }

    /**
     * Handle saving the club.
     *
     * @param Request $request
     */
    public function saveClub(Request $request)
    {
        $data = $request->input('club');
        
        // Validate and save the club data
        $club = Club::create([
            'title' => $data['title'],
            'text' => $data['text'],
            'feedback' => $data['feedback'],
        ]);

        // Show a success message and redirect the user
        Toast::info('Клуб добавлен');
        return redirect()->route('platform.club.edit', $club);
    }
}
