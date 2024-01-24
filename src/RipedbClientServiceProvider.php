<?php

namespace Bakkerit\LaravelRipedbClient;

use Illuminate\Support\ServiceProvider;

class RipedbClientServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->extendSocialite();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/ripe.php');

        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('ripe.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('ripe');
        }

        $this->mergeConfigFrom($source, 'ripe');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerApiClient();
        $this->registerApiAdapter();
        $this->registerManager();
    }

}
