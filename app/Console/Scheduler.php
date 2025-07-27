<?php

namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Scheduler
{
    public function schedule()
    {
        // Запланируйте задачи здесь
        Artisan::call('task:daily');
    }
}
