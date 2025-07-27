<?php

namespace App\Orchid\Screens\Clubs;

use App\Models\Club;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;

class ClubEditScreen extends Screen
{
    public $club;

    public function query(Club $club): iterable
    {
        $this->club = $club;

        return [
            'club' => $club,
        ];
    }

    public function name(): ?string
    {
        return 'Редактировать клуб';
    }

    public function commandBar(): iterable
    {
       return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('club.id')->type('hidden'),
                Input::make('club.title')->title('Название клуба')->required(),
                Input::make('club.feedback')->title('Ссылка')->required(),
    
                \Orchid\Screen\Fields\Quill::make('club.text')
    ->title('Описание клуба')
    ->required(),
                       Button::make('Сохранить')
            
                ->method('save')    
                ->icon('check')
         ->class('btn btn-success')
            ]),
        ];
    }
    
    

    public function save(Request $request)
    {
 
        $validated = $request->validate([
            'club.id' => 'required|integer|exists:club,id',
            'club.title' => 'required|string|max:255',
            'club.feedback' => 'required|string|max:255',
            'club.text' => 'required|string',
        ]);

        $club = Club::findOrFail($validated['club']['id']);
        $club->update([
            'title' => $validated['club']['title'],
            'feedback' => $validated['club']['feedback'],
            'text' => $validated['club']['text'],
        ]);

        Toast::info('Клуб успешно обновлен');

return redirect()->route('platform.clubs');
    }
}
