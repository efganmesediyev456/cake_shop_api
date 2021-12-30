<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('except_exists', function($attribute, $value, $parameters)
        {
            if($value==$parameters[2]){
                return true;
            }
            return DB::table($parameters[0])
                    ->where($parameters[1], '=', $value)
                    ->count()>0;
        });
    }
}
