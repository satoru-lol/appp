<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialist_category_id',
        'birthday',
        'degree',
        'experience',
        'location',
        'about',
        'prices',
        'time',
        'gender',
        'free_time',
        'rating',
        'status',
        'views'
    ];

    protected $casts = [
        'prices' => 'array',
        'time' => 'array',
    ];
}
