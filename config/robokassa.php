<?php

return [
    'login' => env('ROBOKASSA_LOGIN'),
    'password_1' => env('ROBOKASSA_PASSWORD_1'),
    'password_2' => env('ROBOKASSA_PASSWORD_2'),
    'test_password_1' => env('ROBOKASSA_TEST_PASSWORD_1'),
    'test_password_2' => env('ROBOKASSA_TEST_PASSWORD_2'),
    'test_mode' => env('ROBOKASSA_TEST_MODE', false),
    'payment_url' => env('ROBOKASSA_PAYMENT_URL', 'https://auth.robokassa.ru/Merchant/Index.aspx'),
    'culture' => env('ROBOKASSA_CULTURE', 'ru'),
    'hash_algorithm' => env('ROBOKASSA_HASH_ALGORITHM', 'md5'),
    'sno' => env('ROBOKASSA_SNO', 'usn_income_outcome'),
]; 