<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
    use HasFactory;
    public $guarded = [];



//    public function getImageAttribute($value)
//    {
//       $path = storage_path('app/' .'images'.'/'.$value);
//       return $path;
//    }

//    public function gallery(){
//        return $this->hasMany(Gallery::class);
//    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
