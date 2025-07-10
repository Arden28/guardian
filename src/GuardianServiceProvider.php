<?php

namespace Arden28\Guardian;

use Illuminate\Support\ServiceProvider;

class GuardianServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'guardian');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'guardian');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('guardian.php'),
            ], 'config');

            // Register events and listeners
            $this->registerEvents();

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/guardian'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/guardian'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/guardian'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'guardian');

        // Register the main class to use with the facade
        $this->app->singleton('guardian', function () {
            return new Guardian;
        });
    }

    /**
     * Register package events and listeners.
     *
     * @return void
     */
    protected function registerEvents()
    {
        // Example: Register events for login, 2FA, and impersonation
        Event::listen('Vendor\Guardian\Events\UserLoggedIn', 'Vendor\Guardian\Listeners\LogUserLogin');
    }
}
