<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\ContentVideo;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;

class VideoEditScreen extends Screen
{
    public $video;

    public function query(ContentVideo $video): iterable
    {
        return [
            'video' => $video,
        ];
    }

    public function name(): ?string
    {
        return 'Редактирование видео';
    }

    public function commandBar(): array
    {
        return [];
    }

 public function layout(): iterable
{
    return [
        Layout::rows([
            Input::make('video.title')
                ->title('Название')
                ->required(),

            Input::make('video.url')
                ->title('Ссылка на видео')
                ->required(),

            Select::make('video.category_id')
                ->title('Категория')
                ->fromModel(Category::class, 'name')
                ->required(),

            Button::make('Сохранить')
                ->method('save')
                ->class('btn btn-success mt-3'),
        ]),
    ];
}


    public function save(Request $request, ContentVideo $video)
    {
        $video->update($request->get('video'));

        Toast::info('Видео успешно обновлено!');
        return redirect()->route('platform.new_video');
    }
}
