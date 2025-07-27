<?php

namespace App\Orchid\Screens;

use App\Models\RoleContent;
use App\Models\ViewParts;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Layouts\Rows;

class AddPartScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Редактировать главный экран';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Выбрать какой раздел и какая категория будет видна на главной странице';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
    }

    /**
     * Button commands.
     *
     * @return array
     */
    public function commandBar(): array
    {
        return [
            Button::make('Save')
                ->method('save')
        ];
    }

    /**
     * Views.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            Layout::view('platform.example-screen')
        ];
//        return [
//            Layout::rows([
//                Select::make('option1')
//                    ->options([
//                        1 => 'Option 1',
//                        2 => 'Option 2',
//                        3 => 'Option 3',
//                    ])
//                    ->title('Select Option 1')
//                    ->required(),
//
//                Select::make('option2')
//                    ->options([])
//                    ->title('Select Option 2')
//                    ->required()
//                    ->canSee(false), // Hide by default
//            ]),
//            /*
//
//            Layout::view('platform::layouts.example', [
//                'script' => $this->script(),
//            ]),*/
//        ];
    }

    /**
     * Save the form data.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function save(\Illuminate\Http\Request $request)
    {
        $data = $request->all();

        $roleCategory = RoleContent::where("id", $request->option1)->first();

        $viewParts = ViewParts::where("role_content_id", $roleCategory->id)->first();
        if (!empty($viewParts)) {
            $viewParts->category_id = $request->option2;
            $viewParts->save();
        } else {
            ViewParts::create([
                "role_content_id" => $request->option1,
                "category_id" => $request->option2,
                "role_content" => $roleCategory->value
            ]);
        }

        return redirect("/admin");
        // Handle the form submission logic here
    }

    /**
     * Get the view to render the screen.
     *
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\View\View
    {
        /*return view('app')
            ->with('script', $this->script());
    */}

    /**
     * Custom script.
     *
     * @return \Orchid\Screen\TD[]
     */
    public function script()
    {
        return <<<JS
            document.addEventListener('DOMContentLoaded', function () {
                const select1 = document.querySelector('select[name="option1"]');
                const select2 = document.querySelector('select[name="option2"]');
                const select2Row = select2.closest('.row');

                console.log('Script loaded, waiting for select1 changes');

                select1.addEventListener('change', function() {
                    console.log('Option 1 changed:', this.value);
                    const selectedValue = this.value;

                    if (selectedValue) {
                        fetch(`/api/select-options?option1=1`)
                            .then(response => response.json())
                            .then(data => {
                                console.log('Fetched options:', data);
                                select2.innerHTML = ''; // Clear existing options
                                for (const [value, text] of Object.entries(data)) {
                                    const option = document.createElement('option');
                                    option.value = value;
                                    option.textContent = text;
                                    select2.appendChild(option);
                                }

                                select2Row.style.display = 'block';
                            })
                            .catch(error => console.error('Error fetching options:', error));
                    } else {
                        select2Row.style.display = 'none';
                    }
                });
            });
        JS;
    }
}
