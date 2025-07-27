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
}
