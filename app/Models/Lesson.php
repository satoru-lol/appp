<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Lesson extends Model
{
    use HasFactory,AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'section_id',
        'teacher',
        'minute',
        'date',
        'start_time',
        'end_time',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }
}
