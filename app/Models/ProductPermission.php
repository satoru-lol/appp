<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ProductPermission extends Model
{
    use AsSource,HasFactory;

    protected $table = 'product_permissions';
    protected $fillable = [
        'product_id',
        'video',
        'course',
        'club'
    ];
}
