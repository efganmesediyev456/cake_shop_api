<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public $guarded = [];

    public function cake()
    {
        return $this->belongsTo(Cake::class);
    }

    public $hidden = [
        'user_id','cake_id'
    ];
}
