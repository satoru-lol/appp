<?php

namespace app\Http\Controllers\Controllers\Admin;

use app\Http\Controllers\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {   
	    
        $settings = Setting::all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = Setting::all();

        $data = $request->all();
        foreach($data as $key => $value) {
            if(in_array($key,  ['_method', '_token']))
                continue;

            $setting = Setting::where('key', $key)->first();
            if($setting) {
                $setting->key = $key;
                $setting->value = $value;
                $setting->save();
            }
        }

        return redirect()->route('admin.settings.index');
    }

    public function destroy(int $setting_id): RedirectResponse
    {
        $setting = Setting::find($setting_id);
        $setting->delete();

        return redirect()->route('admin.settings.index');
    }

    public function showAdd(Request $request): View
    {
        return view('admin.settings.add');
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'key' => 'required|max:255|unique:settings',
            'desc' => 'max:255',
            'value' => 'max:255'
        ]);

        $setting = new Setting;

        $setting->name = $request->name;
        $setting->key = $request->key;

        if($request->desc !== '')
            $setting->desc = $request->desc;

        if($request->value !== '')
            $setting->value = $request->value;

        $setting->save();

        return back()->with('success', __('Поле добавлено'));
    }
}
