<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Videos extends Model
{
    use HasFactory,AsSource;

    protected $table = 'content_video';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'google_url ',
        'yandex_url',
        'path',
        'title',
        'video_library_id',
    ];
}
