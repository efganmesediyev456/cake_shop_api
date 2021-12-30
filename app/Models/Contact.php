<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    public $guarded=[];

    public $hidden=["updated_at"];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}
