<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    public $guarded=[];
    public $timestamps=false;


    public function imageable()
    {
        return $this->morphTo();
    }


    public function menus()
    {
        return $this->morphedByMany(Menu::class, 'imageable');
    }

    /**
     * Get all of the videos that are assigned this tag.
     */
    public function cakes()
    {
        return $this->morphedByMany(Cake::class, 'imageable');
    }
}
