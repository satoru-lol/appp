<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class CourseContent extends Model
{
    use HasFactory,AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'speakers',
        'section',
        'link'
    ];

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }
}
