<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;


    public $guarded=[];



    public function cake(){
       return  $this->belongsTo(Cake::class)->select("id","name","image");
    }
}
