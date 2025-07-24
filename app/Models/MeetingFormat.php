<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingFormat extends Model
{
    use HasFactory;

    protected $table = "meeting_format";

    protected $fillable = ["format", "label"];
}
