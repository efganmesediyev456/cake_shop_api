<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;
    public $guarded=[];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function cake(){
        return $this->belongsTo(Cake::class);
    }

    protected $hidden = [
        'user_id','cake_id'
    ];
}
