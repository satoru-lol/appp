<?php

namespace App\Service;
use App\Mail\Introduction;
use App\Models\Introduction as ModelsIntroduction;
use App\Models\User;
use App\Models\Subscription;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;


class IntroductionService
{

    public function storeIntroduction($data)
    {
        $phone = str_replace(" ", "", (new PhoneNumber($data['phone']))->formatE164());
        $phoneIntroduction = ModelsIntroduction::where('phone', $phone)->exists();
        // Проверка на существование номера телефона
        if ($phoneIntroduction && User::where('phone',$phone)->whereNotNull('phone_verified_at')->first()) {
         //   throw new \Exception(__('Указанный номер телефона уже используется2 <a href="https://appp-psy.ru/auth"> Попробуйте войти </a>'));
        }

        // Проверка на существование электронной почты
        /*if (ModelsIntroduction::where('email', $data['email'])->exists()) {
            throw new \Exception(__('Указанная электронная почта уже используется <a href="https://appp-psy.ru/auth"> Попробуйте войти </a>'));
        }*/

        // Синтаксическая проверка электронной почты
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception(__('Указанный электронный адрес некорректен.'));
        }

        // Проверка существования домена электронной почты
        $emailDomain = substr(strrchr($data['email'], "@"), 1);
        if (!checkdnsrr($emailDomain, 'MX')) {
            throw new \Exception(__('Указанный электронный адрес некорректен.'));
        }

        $hash = md5(Str::random(15));
        if (empty($phoneIntroduction)){
            $introduction = ModelsIntroduction::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $phone,
                'hash' => $hash
            ]);
        }else{
            $introduction = ModelsIntroduction::where('phone',$phone)->update(['hash' => $hash]);
        }
        /*

        $user = User::create([
            'email' => $data['email'],
            "phone" => $phone,
            "password" => "null"
        ]);*/


        try {
            Mail::to($data['email'])->send(new Introduction(route('introductionConfirm', $hash)));
        } catch (Exception $e) {
            if (isset($introduction) && $introduction){
                $introduction->delete();
            }
            throw new \Exception(__($e->getMessage()));
        }

        return $introduction;
    }

    public function storeIntroductionTest($data)
    {
        $phone = str_replace(" ", "", (new PhoneNumber($data['phone']))->formatE164());
        $phoneIntroduction = ModelsIntroduction::where('phone', $phone)->exists();
        // Проверка на существование номера телефона
        if ($phoneIntroduction) {
           // throw new \Exception(__('Указанный номер телефона уже используется2 <a href="https://appp-psy.ru/auth"> Попробуйте войти </a>'));
        }

        // Проверка на существование электронной почты
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception(__('Указанная электронная почта уже используется <a href="https://appp-psy.ru/auth"> Попробуйте войти </a>'));
        }

        // Синтаксическая проверка электронной почты
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception(__('Указанный электронный адрес некорректен.'));
        }

        // Проверка существования домена электронной почты
        $emailDomain = substr(strrchr($data['email'], "@"), 1);
        if (!checkdnsrr($emailDomain, 'MX')) {
            throw new \Exception(__('Указанный электронный адрес некорректен.'));
        }

        $hash = md5(Str::random(15));
//        if (empty($phoneIntroduction)){
            $introduction = ModelsIntroduction::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $phone,
                'hash' => $hash
            ]);
        /*}else{
            $introduction = ModelsIntroduction::where('phone',$phone)->update(['hash' => $hash]);
        }*/
        /*

        $user = User::create([
            'email' => $data['email'],
            "phone" => $phone,
            "password" => "null"
        ]);*/


        try {
            Mail::to($data['email'])->send(new Introduction(route('introductionConfirmTest', $hash)));
        } catch (Exception $e) {
            if (isset($introduction) && $introduction){
                $introduction->delete();
            }
            throw new \Exception(__($e->getMessage()));
        }

        return $introduction;
    }

}
