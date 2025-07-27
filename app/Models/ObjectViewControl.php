<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectViewControl extends Model
{
    use HasFactory;

    protected $table = "object_view_control";

    protected $fillable = ["user_id", "object_id", "object_name"];
}
