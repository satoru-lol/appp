<?php

namespace App\Orchid\Screens\Video;

use App\Models\Product;
use App\Models\Videos;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class VideoListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'videos' => Videos::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Видеотека';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать видеотеку')->modal('addVideo')->method('createVideo'),
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
            Layout::table('videos',[
                TD::make('title', 'Заголовок')->width('200px'),
                TD::make('yandex_url', 'Ссылка')->width('200px'),
                TD::make('video_library_id', 'Уровень подписки')->width('200px'),
                TD::make('Действие')->render(function (Videos $videos) {
                    return ModalToggle::make('Редактировать')
                        ->modal('editVideo')
                        ->method('updateVideo')
                        ->modalTitle('Редактирование видеотеку')
                        ->asyncParameters(['id' => $videos->id]);
                }),
                TD::make('Удалить')->render(function (Videos $videos){
                    return Button::make('Удалить')->icon('trash')->class('btn btn-danger')
                        ->confirm('Вы уверены, что хотите удалить эту видеотеку?')
                        ->method('deleteVideo')
                        ->parameters(['id' => $videos->id]);
                }),
            ]),

            Layout::modal('addVideo', Layout::rows([
                Group::make([
                    Input::make('title')->required()->title('Название заголовка'),
                    Input::make('yandex_url')->required()->title('Ссылка на видео'),
                    Select::make('video_library_id')->required()->title('Категория видео')->options([
                        4=> 'Клубы',
                        5 => 'Курсы',
                    ])
                ]),
            ]))->title('Создание видео')->applyButton('Создать'),

            Layout::modal('editVideo', Layout::rows([
                Group::make([
                    Input::make('video.title')->required()->title('Название заголовка'),
                    Input::make('video.yandex_url')->required()->title('Ссылка к видео'),
                    Input::make('video.id')->hidden()
                ]),
                Select::make('video.video_library_id')->required()->options([
                    4=> 'Клубы',
                    5 => 'Курсы',
                ]),
            ]))->title('Создание видео')->applyButton('Отредактировать')->async('asyncGetVideo')
        ];
    }

    public function asyncGetVideo(int $id) {
        return [
            'video' => Videos::find($id),
        ];
    }

    public function createVideo(Request $request) {
        Videos::create($request->all());
        Toast::success('Видео создано');
//        return redirect()->route('platform.video.list');
    }

    public function updateVideo(Request $request)
    {
        Videos::where('id', $request->input('video.id'))->update([
            'title' => $request->input('video.title'),
            'yandex_url' => $request->input('video.yandex_url'),
            'video_library_id' => $request->input('video.video_library_id'),
        ]);
        Toast::success('Видео изменено');
    }

    public function deleteVideo(Request $request){
        Videos::destroy($request->input('id'));
        Toast::success('Видео удалено');
    }
}
