<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Club extends Model
{
    use HasFactory,AsSource;

    protected $table = 'club'; 

    protected $fillable = [
        'image', 'title', 'times', 'date', 'speakers', 'theory', 'feedback', 'text', 'pay_method', 'video', 'product_level', 'is_hidden'
    ];

    public function clubDates(){
        return $this->hasOne(ClubDate::class, 'club_id', 'id')
        ->where('date', '>=', Carbon::today())
        ->orderBy('date');
    }

    public function allClubDates()
    {
        return $this->hasMany(ClubDate::class, 'club_id', 'id')->orderBy('date', 'desc');
    }
}
