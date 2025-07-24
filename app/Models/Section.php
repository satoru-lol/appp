<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Section extends Model
{
    use HasFactory,AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'module_id'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class,'module_id');
    }
}
