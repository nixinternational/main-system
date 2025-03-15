<?php

namespace App\Providers;

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
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
          if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }


        Blade::if('hasGroup', function (string $groups) {
            $grupos = explode('|', $groups);
            $bool   = false;
            $user = Auth::user();
            if($user->grupoPermissao == null ){
                return true;
            }
            if($user->grupoPermissao == null && $user->obtemTodosGrupos() == 'administrador' || $user->obtemTodosGrupos() == 'root'){
                return true;
            }
            foreach ($grupos as $grupo) {
                $bool = $user->pertenceAoGrupo($grupo);
            }

            return $bool;
        });
        Blade::if('hasPermission', function (string $groups) {
            $grupos = explode('|', $groups);
            $bool   = false;
            $user = Auth::user();
            if($user->obtemTodosGrupos() == 'administrador' || $user->obtemTodosGrupos() == 'root'){
                return true;
            }
            foreach ($grupos as $grupo) {
                if($user->pertenceAPermissao($grupo)){
                    return true;
                }
             
            }

            return $bool;
        });
    }
}
