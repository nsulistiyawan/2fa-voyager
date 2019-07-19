<?php

namespace App\Providers;

use App\Http\Middleware\Voyager2FaMiddleware;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Http\Controllers\Voyager2FaController;
use TCG\Voyager\Http\Controllers\VoyagerAuthController;
use App\Http\Controllers\Login2FaController;
use TCG\Voyager\Http\Controllers\VoyagerController;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VoyagerAuthController::class, Login2FaController::class);
        $this->app->bind(VoyagerAdminMiddleware::class, Voyager2FaMiddleware::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
