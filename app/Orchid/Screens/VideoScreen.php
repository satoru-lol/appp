<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\ContentVideo;
use App\Models\Videos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class VideoScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'video' => ContentVideo::with('category')->paginate(20),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Управление с видеотекой';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить видео')
            ->modal('createVideoModal')
            ->icon('plus')
            ->method('createVideo')
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
        Layout::table('video', [
            TD::make('title', 'Заголовок')->width('35%')
                ->render(fn(ContentVideo $video) => $video->title),

            TD::make('url', 'Ссылка')
                ->render(fn(ContentVideo $video) => '<a href="' . $video->url . '" target="_blank">' . $video->url . '</a>'),

            TD::make('category', 'Категория')->width('35%')
                ->render(fn(ContentVideo $video) => Str::limit($video->category->name ?? 'Без категории')),

            TD::make('Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('150px')
                ->render(function (ContentVideo $video) {
                    return
                        Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.video.edit', $video->id)
                            ->class('btn btn-warning btn-sm me-2') .

                        Button::make('Удалить')
                            ->method('deleteVideo')
                            ->icon('trash')
                            ->confirm('Вы действительно хотите удалить?')
                            ->parameters(['id' => $video->id])
                            ->class('btn btn-danger btn-sm');
                }),
        ]),

        Layout::modal('createVideoModal', [
            Layout::rows([
                Input::make('video.id')->type('hidden'),
                Input::make('video.title')
                    ->title('Название видео')
                    ->required(),

                Input::make('video.url')
                    ->title('Ссылка на видео')
                    ->required(),

                Select::make('video.category_id')
                    ->title('Категория')
                    ->fromModel(Category::class, 'name')
                    ->required(),
            ])
        ])->title('Добавить видео')->applyButton('Сохранить'),
    ];
}


    public function createVideo(Request $request){
        $data = $request->input('video');
        ContentVideo::create([
            'title' => $data['title'],
            'url' => $data['url'],
            'category_id' => $data['category_id'],
        ]);
        Toast::success('Видео добавлено!');
    }

    public function deleteVideo(Request $request){
        $video = ContentVideo::find($request->input('id'));
        $video->delete();
        Toast::success('Видео удалено!');
    }
}