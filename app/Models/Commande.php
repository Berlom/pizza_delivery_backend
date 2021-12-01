<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable=[
        'address_id'
    ];

    public function Addresses(){
        return $this->belongsTo(Address::class,'address_id');
    }

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
