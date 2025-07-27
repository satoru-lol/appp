<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    protected $fillable = [
        'id',
        'name',
        'price',
        'description',
        'first_week_price',
        'visible'
    ];

    public function getPermissions(){
        return $this->hasOne(ProductPermission::class,'product_id');
    }

    public function setLevelAttribute($value)
    {
        if (isset($this->attributes['level']) && $this->attributes['level'] != $value) {
            $user = auth()->check() ? auth()->user()->id : 'guest';
            $old = $this->attributes['level'];
            $new = $value;
            $trace = json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10));
            $log = "[".now()."] Product ID: {$this->id}, LEVEL changed: {$old} => {$new}, by user: {$user}, trace: {$trace}\n";
            file_put_contents(storage_path('logs/level_changes.log'), $log, FILE_APPEND);
        }
        $this->attributes['level'] = $value;
    }
}
