<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Course extends Model
{
    use HasFactory,AsSource;

    protected $casts = [
        "times" => "array"
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_category_id',
        'title',
        'image',
        'price',
        'expect',
        'time',
		'text',
        'pay_method',
        'video',
        'product_level',
        'is_hidden',
        'is_polygon'
    ];
}
