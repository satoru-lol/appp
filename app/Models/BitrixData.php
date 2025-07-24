<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class BitrixData extends Model
{
    use AsSource;
    protected $table = 'bitrix_data';
    protected $fillable = [
        'fullName',
        'course_name',
        'stage',
        'amount',
        'fact',
        'ostatok',
        'deal_id'
    ];
}
