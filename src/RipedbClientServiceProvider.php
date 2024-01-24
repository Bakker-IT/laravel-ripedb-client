<?php

namespace Bakkerit\LaravelRipedbClient;

use Bakkerit\LaravelRipedbClient\Models\AsBlock;
use Bakkerit\LaravelRipedbClient\Models\AsSet;
use Bakkerit\LaravelRipedbClient\Models\AutNum;
use Bakkerit\LaravelRipedbClient\Models\Domain;
use Bakkerit\LaravelRipedbClient\Models\FilterSet;
use Bakkerit\LaravelRipedbClient\Models\Inet6num;
use Bakkerit\LaravelRipedbClient\Models\Inetnum;
use Bakkerit\LaravelRipedbClient\Models\InetRtr;
use Bakkerit\LaravelRipedbClient\Models\Irt;
use Bakkerit\LaravelRipedbClient\Models\KeyCert;
use Bakkerit\LaravelRipedbClient\Models\Mntner;
use Bakkerit\LaravelRipedbClient\Models\Organisation;
use Bakkerit\LaravelRipedbClient\Models\PeeringSet;
use Bakkerit\LaravelRipedbClient\Models\Person;
use Bakkerit\LaravelRipedbClient\Models\Poem;
use Bakkerit\LaravelRipedbClient\Models\PoeticForm;
use Bakkerit\LaravelRipedbClient\Models\Role;
use Bakkerit\LaravelRipedbClient\Models\Route;
use Bakkerit\LaravelRipedbClient\Models\Route6;
use Bakkerit\LaravelRipedbClient\Models\RouteSet;
use Bakkerit\LaravelRipedbClient\Models\RtrSet;

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
        $this->registerModels();
    }

    public function registerModels() {
        $this->app->bind('ripedb.asBlock', function () {
            return new AsBlock();
        });

        $this->app->bind('ripedb.asSet', function () {
            return new AsSet();
        });

        $this->app->bind('ripedb.autNum', function () {
            return new AutNum();
        });

        $this->app->bind('ripedb.domain', function () {
            return new Domain();
        });

        $this->app->bind('ripedb.filterSet', function () {
            return new FilterSet();
        });

        $this->app->bind('ripedb.inet6num', function () {
            return new Inet6num();
        });

        $this->app->bind('ripedb.inetnum', function () {
            return new Inetnum();
        });

        $this->app->bind('ripedb.inetRtr', function () {
            return new InetRtr();
        });

        $this->app->bind('ripedb.irt', function () {
            return new Irt();
        });

        $this->app->bind('ripedb.keyCert', function () {
            return new KeyCert();
        });

        $this->app->bind('ripedb.mntner', function () {
            return new Mntner();
        });

        $this->app->bind('ripedb.organisation', function () {
            return new Organisation();
        });

        $this->app->bind('ripedb.peeringSet', function () {
            return new PeeringSet();
        });

        $this->app->bind('ripedb.person', function () {
            return new Person();
        });

        $this->app->bind('ripedb.poem', function () {
            return new Poem();
        });

        $this->app->bind('ripedb.poeticForm', function () {
            return new PoeticForm();
        });

        $this->app->bind('ripedb.role', function () {
            return new Role();
        });

        $this->app->bind('ripedb.route', function () {
            return new Route();
        });

        $this->app->bind('ripedb.route6', function () {
            return new Route6();
        });

        $this->app->bind('ripedb.routeSet', function () {
            return new RouteSet();
        });

        $this->app->bind('ripedb.rtrSet', function () {
            return new RtrSet();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'ripedb.asBlock',
            'ripedb.asSet',
            'ripedb.autNum',
            'ripedb.domain',
            'ripedb.filterSet',
            'ripedb.inet6num',
            'ripedb.inetnum',
            'ripedb.inetRtr',
            'ripedb.irt',
            'ripedb.keyCert',
            'ripedb.mntner',
            'ripedb.organisation',
            'ripedb.peeringSet',
            'ripedb.person',
            'ripedb.poem',
            'ripedb.poeticForm',
            'ripedb.role',
            'ripedb.route',
            'ripedb.route6',
            'ripedb.routeSet',
            'ripedb.rtrSet',
        ];
    }

}
