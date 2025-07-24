<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Product IDs & Levels
    |--------------------------------------------------------------------------
    |
    | This file stores the master product IDs and levels for different 
    | subscription types. This avoids hard-coding magic numbers in the code,
    | making access checks more reliable and readable.
    |
    */

    'products' => [
        // Название => [id, level]
        'premium'      => ['id' => 59, 'level' => 5],
        'basic'        => ['id' => 62, 'level' => 6],
        'trial'        => ['id' => 1,  'level' => 1],
        
        // Системные/старые подписки
        'transitional' => ['id' => 66, 'level' => 8],
        'trial_old'    => ['id' => 68, 'level' => 0], // Старая пробная с level 0
    ],

]; 