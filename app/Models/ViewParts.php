<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewParts extends Model
{
    use HasFactory;

    protected $fillable = ["role_content_id", "category_id", "role_content"];
}
