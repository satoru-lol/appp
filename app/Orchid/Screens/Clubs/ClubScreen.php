<?php

namespace App\Orchid\Screens\Clubs;

use App\Models\Club;
use App\Models\ClubDate;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ClubScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'club' => Club::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ClubScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить дату')
                ->icon('plus')
                ->modal('dateModal')
                ->method('saveDate'),

            Button::make('Создать новый клуб')
                ->icon('plus')
                ->method('openCreateClubScreen'),
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
            // Модальное окно для добавления новой даты
            Layout::modal('dateModal', Layout::rows([
                Select::make('club.club_id')
                    ->title('Тип')
                    ->fromModel(Club::class, 'title', 'id')
                    ->empty('Выберите тип')
                    ->required(),

                Input::make('club.date')
                    ->title('Дата')
                    ->type('date')
                    ->required(),

                // DateTimer::make('club.start_time')
                //     ->title('Время начала')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->required(),
                    
                    Input::make('club.start_time')
                    ->title('Время начала')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                // DateTimer::make('club.end_time')
                //     ->title('Время окончания')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->required(),
                    
                       Input::make('club.end_time')
                    ->title('Время окончания')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                Input::make('club.speakers')
                    ->title('Спикеры')
                    ->required(),
            ]))
                ->method('saveDate')
                ->applyButton('Сохранить')
                ->closeButton('Отмена'),

            // Модальное окно для редактирования клуба
            Layout::modal('editClubDataModal', Layout::rows([
                Input::make('club.id')->type('hidden'),

                Input::make('club.feedback')->title('Ссылка')->type('text')->required(),

                Input::make('club.title')->title('Название клуба')->type('text')->required(),

                TextArea::make('club.text')->title('Описание клуба')->required(),
            ]))
                ->title('Редактировать клуб')
                ->applyButton('Сохранить')
                ->async('asyncClub'),

            // Таблица с клубами
            Layout::table('club', [
                TD::make('id', 'ID')->width('10%'),
                TD::make('title', 'Название')->width('20%'),
                TD::make('text', 'Описание')->width('30%')
                    ->render(fn($model) => "<div class='p-2 bg-gray-100 rounded-lg text-sm font-medium'>{$model->text}</div>"),
                TD::make('feedback', 'Ссылка на Zoom')->width('20%'),
                TD::make('Действие')->render(function (Club $club) {
                    return ModalToggle::make('Показать расписание')
                            ->icon('eye')
                            ->modal('rowModal')
                            ->class('btn btn-primary btn-sm')
                            ->asyncParameters(['id' => $club->id])
                        . "\n" .
                        Link::make('Редактировать')
                            ->icon('pencil')
                            ->href(route('platform.club.edit', $club->id))
                            ->class('btn btn-warning btn-sm')
                        . "\n" .
                        Button::make('Удалить')
                            ->icon('trash')
                            ->class('btn btn-danger btn-sm')
                            ->method('deleteClub')
                            ->parameters(['id' => $club->id]);
                }),
            ]),

            // Модальное окно с расписанием клуба + кнопки редактирования и удаления даты
            Layout::modal('rowModal', [
                Layout::table('clubs', [
                    TD::make('id', 'ID')->width('10%'),
                    TD::make('date', 'Дата')->width('20%'),
                    TD::make('start_time', 'Время начала')->width('20%'),
                    TD::make('end_time', 'Время окончания')->width('20%'),
                    TD::make('speakers', 'Спикеры')->width('25%'),
                    TD::make('Действия')->render(function (ClubDate $clubDate) {
                        return Button::make('Удалить')
                                ->icon('trash')
                                ->method('deleteClubDate', ['id' => $clubDate->id])
                            . "\n" .
                            ModalToggle::make('Редактировать')
                                ->icon('pencil')
                                ->modal('editDateModal')
                                ->method('saveEditedDate')
                                ->asyncParameters(['id' => $clubDate->id]);
                             
                    }),
                ]),
            ])
                ->title('Данные клуба')
                ->async('asyncFetchModal'),

            // Модальное окно редактирования даты расписания
            Layout::modal('editDateModal', Layout::rows([
                Input::make('clubDate.id')->type('hidden'),

                Select::make('clubDate.club_id')
                    ->title('Тип')
                    ->fromModel(Club::class, 'title', 'id')
                    ->empty('Выберите тип')
                    ->required(),

                Input::make('clubDate.date')
                    ->title('Дата')
                    ->type('date')
                    ->required(),

                // DateTimer::make('clubDate.start_time')
                //     ->title('Время начала')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->required(),
                    
                        Input::make('clubDate.start_time')
                    ->title('Время начала')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                // DateTimer::make('clubDate.end_time')
                //     ->title('Время окончания')
                //     ->format('H:i')
                //     ->enableTime()
                //     ->disableDate()
                //     ->required(),
                    
                         Input::make('clubDate.end_time')
                    ->title('Время окончания')
                    ->type('time')
                    ->format('H:i')
                    ->required(),

                Input::make('clubDate.speakers')
                    ->title('Спикеры')
                    ->required(),
            ]))
                ->title('Редактировать дату')
                ->applyButton('Сохранить')
                ->async('asyncEditDate'),
        ];
    }

    /**
     * Async загрузка расписания для клуба
     */
    public function asyncFetchModal(int $id): array
    {
        $clubDates = ClubDate::where('club_id', $id)->get();
        return ['clubs' => $clubDates];
    }

    /**
     * Сохранение новой даты
     */
    public function saveDate(Request $request)
    {
        $validated = $request->validate([
            'club.club_id' => 'required|exists:clubs,id',
            'club.date' => 'required|date',
            'club.start_time' => 'required',
            'club.end_time' => 'required',
            'club.speakers' => 'required|string',
        ]);

        ClubDate::create([
            'club_id' => $validated['club']['club_id'],
            'date' => $validated['club']['date'],
            'start_time' => $validated['club']['start_time'],
            'end_time' => $validated['club']['end_time'],
            'speakers' => $validated['club']['speakers'],
        ]);

        Toast::info('Дата добавлена');
    }

    /**
     * Удаление даты расписания
     */
    public function deleteClubDate(int $id)
    {
        ClubDate::findOrFail($id)->delete();
        Toast::info('Дата удалена');
    }

    /**
     * Async загрузка данных для редактирования клуба
     */
    public function asyncClub(int $id): array
    {
        $club = Club::select('id', 'title', 'feedback', 'text')->findOrFail($id);

        return ['club' => $club];
    }

    /**
     * Сохранение изменений клуба
     */
    public function editClubDate(Request $request)
    {
        $validated = $request->validate([
            'club.id' => 'nullable|integer|exists:club,id',
            'club.feedback' => 'required|string|max:255',
            'club.title' => 'required|string|max:255',
            'club.text' => 'required|string',
        ]);

        Club::where('id', $validated['club']['id'])->update([
            'feedback' => $validated['club']['feedback'],
            'title' => $validated['club']['title'],
            'text' => $validated['club']['text'],
        ]);

        Toast::info('Клуб сохранен');
    }

    /**
     * Метод async загрузки данных для редактирования даты расписания
     */
    public function asyncEditDate(int $id): array
    {
        $clubDate = ClubDate::findOrFail($id);
        return ['clubDate' => $clubDate];
    }

    /**
     * Сохранение изменений даты расписания
     */
    public function saveEditedDate(Request $request)
    {
        $validated = $request->validate([
            'clubDate.id' => 'required|integer|exists:club_dates,id',
            'clubDate.club_id' => 'required|integer|exists:club,id',
            'clubDate.date' => 'required|date',
            'clubDate.start_time' => 'required',
            'clubDate.end_time' => 'required',
            'clubDate.speakers' => 'required|string',
        ]);

        $clubDate = ClubDate::findOrFail($validated['clubDate']['id']);
        $clubDate->update([
            'club_id' => $validated['clubDate']['club_id'],
            'date' => $validated['clubDate']['date'],
            'start_time' => $validated['clubDate']['start_time'],
            'end_time' => $validated['clubDate']['end_time'],
            'speakers' => $validated['clubDate']['speakers'],
        ]);

        Toast::info('Расписание обновлено');
    }

    /**
     * Переход на экран создания клуба
     */
    public function openCreateClubScreen()
    {
        return redirect()->route('platform.create.club');
    }

    /**
     * Удаление клуба
     */
    public function deleteClub(int $id)
    {
        $club = Club::find($id);

        if (!$club) {
            Toast::error('Клуб не найден.');
            return;
        }

        $club->delete();
        Toast::info('Клуб удален');
    }
}
