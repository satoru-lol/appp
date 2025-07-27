<?php

namespace App\Orchid\Screens\Video;

use App\Models\Videos;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class VideoEditScreen extends Screen
{
    public $video;

    public function query(Videos $video): array
    {
        return [
            'video' => $video,
        ];
    }

    public function name(): ?string
    {
        return 'Редактирование видео';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('video.title')->title('Название')->required(),
                Input::make('video.yandex_url')->title('Ссылка')->required(),
                Select::make('video.video_library_id')
                    ->title('Категория')
                    ->options([
                        4 => 'Клубы',
                        5 => 'Курсы',
                    ])
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request, Videos $video)
    {
        $video->update($request->get('video'));
        Toast::success('Видео обновлено');
        return redirect()->route('platform.video.list');
    }
}
