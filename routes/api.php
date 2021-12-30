<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CakeController;
use App\Http\Controllers\Admin\CakeController as AdminCakeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavouriteController;
use \App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use \App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;


Route::post("login", [AuthController::class, "login"]);
Route::post("check_token", [AuthController::class, "checkToken"]);
Route::post("logout", [AuthController::class, "logout"]);


Route::post("user/login", [LoginController::class, 'login'])->middleware('throttle:login');
Route::post("user/register", [LoginController::class, "register"]);
Route::post("user/logout", [LoginController::class, "logout"])
    ->middleware('auth:api');
Route::post("user/check_token", [LoginController::class, "checkToken"]);


Route::group(["middleware" => ["auth:api", "admin"], "prefix" => "admin"], function () {
    Route::post("cakes", [AdminCakeController::class, 'index'])->middleware("permission:cakes view");
//    Route::post("cakes/paginate", [AdminCakeController::class, 'paginate']);
    Route::post("cakes/show", [AdminCakeController::class, 'show'])->middleware("permission:cake view");
    Route::post("cakes/delete", [AdminCakeController::class, 'delete'])->middleware("permission:cake delete");
    Route::post("add-edit-cake", [AdminCakeController::class, "addEdit"]);
    Route::post("categories/show", [AdminCategoryController::class, 'show'])->middleware('permission:category view');
    Route::post("categories", [AdminCategoryController::class, 'index'])->middleware('permission:categories view');
    Route::post("categories/delete", [AdminCategoryController::class, 'delete'])->middleware('permission:category delete');
    Route::post("add-edit-category", [AdminCategoryController::class, "addEdit"]);
//    Route::post("menus",[AdminMenuController::class,'index']);
//    Route::post("menus/add-edit",[AdminMenuController::class,"add_edit"]);
//    Route::post("menus/delete",[AdminMenuController::class,"delete"]);
    Route::post('operation', [PermissionController::class, 'index'])->middleware('permission:operations view');
    Route::post('operation/store', [PermissionController::class, 'store'])->middleware('permission:operation create');
    Route::post('operation/update', [PermissionController::class, 'update'])->middleware('permission:operation edit');
    Route::post('operation/remove', [PermissionController::class, 'remove'])->middleware('permission:operation delete');
    Route::post('users', [UserController::class, 'index'])->middleware(["permission:users view"]);
    Route::post('users/store', [UserController::class, 'store'])->middleware(["permission:user store"]);
    Route::post('users/delete', [UserController::class, 'delete'])->middleware(["permission:user delete"]);
    Route::post('users/show', [UserController::class, 'show'])->middleware(["permission:user view"]);
    Route::post('users/update', [UserController::class, 'update'])->middleware(["permission:user update"]);
    Route::post("orders",[OrderController::class,'index'])->middleware(["permission:orders view"]);
    Route::post("contacts", [ContactController::class, 'contacts'])->middleware("permission:contacts view");
    Route::post("contacts/delete", [ContactController::class, 'delete'])->middleware("permission:contact delete");
    Route::post("contacts/read/all", [ContactController::class, 'readAll']);
    Route::post("contacts/read", [ContactController::class, 'read']);
    Route::post("contacts/read/counts", [ContactController::class, 'contactCounts']);
});


Route::post("cakes", [CakeController::class, 'index']);
Route::post("lastCakes", [CakeController::class, 'lastCake']);
Route::post("cake", [CakeController::class, 'cake']);
Route::post("category", [CategoryController::class, 'category']);
Route::post("categories", [CategoryController::class, 'index']);



Route::post("cake/favourite/add", [FavouriteController::class, 'addFavourite']);
Route::post("cake/favourites", [FavouriteController::class, 'favorites']);


Route::post("cake/cart/add", [CartController::class, 'addCart']);
Route::post("cake/carts", [CartController::class, 'carts']);
Route::post("cake/cart/remove", [CartController::class, 'removeCart']);
Route::post("cake/cart/removeAll", [CartController::class, 'removeAll']);
Route::post("cake/cart/increase", [CartController::class, 'increase']);
Route::post("cake/cart/decrease", [CartController::class, 'decrease']);
Route::post("cake/contact", [ContactController::class, 'contact']);


Route::post("checkout", [CheckoutController::class, 'checkout']);




