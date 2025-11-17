<?php

namespace App\Providers;

use App\Utilities\Formatter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('formatter', function () {
            return new Formatter();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        if (config('app.env') === 'production') {
            // \URL::forceScheme('https');
        }


        Blade::if('hasGroup', function (string $groups) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            if ($user->isSuperUser()) {
                return true;
            }

            foreach (explode('|', $groups) as $grupo) {
                if ($user->hasRole(trim($grupo))) {
                    return true;
                }
            }

            return false;
        });

        Blade::if('hasPermission', function (string $permissions) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            foreach (explode('|', $permissions) as $permission) {
                if ($user->hasPermission(trim($permission))) {
                    return true;
                }
            }

            return false;
        });
    }
}
