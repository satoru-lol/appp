<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Donat extends Model
{
    use HasFactory,AsSource;

    protected $table = "donat";

    protected $fillable = [
        'amount','reason','club_id','course_id','user_id'
    ];


    public function club(){
        return $this->belongsTo(Club::class,'club_id');
    }
    public function course(){
        return $this->belongsTo(Course::class,'course_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
