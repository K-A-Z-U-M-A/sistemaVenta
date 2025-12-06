<?php

namespace App\Providers;

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
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Registrar Observers - Se ejecutarán cuando haya acciones en los modelos
        \App\Models\Producto::observe(\App\Observers\ProductoObserver::class);
        \App\Models\Venta::observe(\App\Observers\VentaObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Categoria::observe(\App\Observers\CategoriaObserver::class);
        \App\Models\Caja::observe(\App\Observers\CajaObserver::class);
    }
}
