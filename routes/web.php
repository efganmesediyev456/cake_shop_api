<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




//Route::get("cake/favourite/add",[CartController::class,'addFavourite']);
//Route::get("cake/favourites",[CartController::class,'favorites']);
//Route::get("cake/favourite/remove",[CartController::class,'removeFavourite']);

Route::get('/', function() {

    return "Laravel Cake Api";

});

