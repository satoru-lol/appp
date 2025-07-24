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
        'birthday' => 'date',
        'rating' => 'decimal:2',
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с категорией специалиста
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'specialist_category_id');
    }

    /**
     * Скоупы для удобных запросов
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }
}
