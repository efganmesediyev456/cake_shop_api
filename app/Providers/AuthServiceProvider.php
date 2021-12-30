<?php

namespace App\Providers;

use App\Models\Access_operation;
use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }


        $user=request()->user();

        if(Schema::hasTable('access_operations')){
            Access_operation::get()->map(function ($permission) {

                Gate::define($permission->name, function ($user) use ($permission) {

                    $bool= (bool)$user->operations->where("name",$permission->name)->count();

                    return $bool;


                });
            });

        }


//        Gate::define('', function ($user, $post) {
//            return $user->id == $post->user_id;
//        });
    }
}
