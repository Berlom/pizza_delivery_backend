<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'menu_id',
        'ingredients',
        'quantity'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
