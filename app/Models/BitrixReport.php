<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class BitrixReport extends Model
{
    use AsSource;
    protected $table = 'bitrix_report';
    protected $fillable = [
        'name',
        'bitrix_id',
        'clock',
        'coef',
        'all_amount',
        'otkaz',
    ];
}
