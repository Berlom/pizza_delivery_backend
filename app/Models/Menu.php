<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'ingredients'
    ];

    public function ingredient(){
        return $this->belongsToMany(Ingredient::class,'ingredient_menu');
    }
}
