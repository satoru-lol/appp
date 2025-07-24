<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'blog_category_id',
        'blog_content_id',
        'name',
        'time_read',
        'image',
        'views',
        'status',
		'reg',
        'video',
        'amount',
        'product_level',
        'feedback',
        'date',
        'fio',
        'format_id',
        'is_meeting',
        'explanation',
        'quantity'
    ];

    public function blogContent()
    {
        return $this->hasOne(BlogContent::class, 'id', 'blog_content_id');
    }

    public function format()
    {
        return $this->belongsTo(\App\Models\MeetingFormat::class, 'format_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\BlogComment::class, 'blog_id');
    }

    public function likes()
    {
        return $this->morphMany(\App\Models\Like::class, 'object', 'object_name', 'object_id')
                    ->where('object_name', 'meeting');
    }

    public function dislikes()
    {
        return $this->morphMany(\App\Models\Dislike::class, 'object', 'object_name', 'object_id')
                    ->where('object_name', 'meeting');
    }

    public function participants()
    {
        return $this->morphMany(\App\Models\ParticipantActions::class, 'object', 'object_name', 'object_id')
                    ->where('object_name', 'meeting');
    }
}
