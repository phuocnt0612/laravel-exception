<?php

namespace Phuocnt\LaravelException\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadConfig();
        $this->registerAssets();
    }

    private function loadConfig()
    {
        if ($this->app['config']->get('exception') === null) {
            $this->app['config']->set('exception', require __DIR__.'/../config/exception.php');
        }
    }


    private function registerAssets()
    {
        $this->publishes([
            __DIR__.'/../config/exception.php' => config_path('exception.php')
        ]);
    }
}
