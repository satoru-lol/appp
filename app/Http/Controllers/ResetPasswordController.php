<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm($token, $email)
    {
        \DB::table('password_resets')->where("email", $email)->delete();
        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {/*
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);*/

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Электронная почта не найдена.']);
        }

        // Проверка токена
        $passwordReset = \DB::table('password_resets')
            ->where('email', $request->input('email'))
            ->first();
      
        if (!$passwordReset) {
            return back()->withErrors(['token' => 'Недействительный токен сброса пароля.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Удаление использованного токена
        \DB::table('password_resets')->where('email', $request->input('email'))->delete();

        return redirect()->route('login')->with('status', 'Ваш пароль был сброшен!');
    }
}
