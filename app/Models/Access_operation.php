<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access_operation extends Model
{
    use HasFactory;

    public $guarded=[];

    public $hidden=["pivot"];
}
