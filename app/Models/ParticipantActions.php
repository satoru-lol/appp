<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantActions extends Model
{
    use HasFactory;

    protected $table = "participant_actions";

    protected $fillable = ["user_id", "object_id", "object_name"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function courseContent()
    {
        return $this->belongsTo(CourseContent::class, 'object_id')
            ->where('object_name', 'courses');
    }
}
