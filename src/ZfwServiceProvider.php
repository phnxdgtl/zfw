<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Support\ServiceProvider;

class ZfwServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'zfw');
        $this->publishes([
            __DIR__.'/config/zfw.php' => config_path('zfw.php'),
        ]);
        $this->publishes([
            __DIR__.'/views/notification-md.blade.php' => resource_path('views/zfw/notification-email.blade.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Sevenpointsix\Zfw\ZfwController');

        /* Not working
        $this->app->bind('zwf', function () {
            return new Zfw;
        });
        */
    }

}
