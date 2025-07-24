<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ClubDate extends Model
{
    use HasFactory,AsSource;

    protected $table = "club_dates";
    protected $fillable = ['date', 'club_id','start_time', 'end_time','speakers'];
}
