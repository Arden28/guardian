<?php

namespace Arden28\Guardian;

use Arden28\Guardian\Providers\SocialiteProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

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
        $this->publishConfig();
        $this->publishMigrations();
        
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('guardian.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'guardian');


        // Bind the User model dynamically
        $this->app->bind('guardian.user', function () {
            $model = config('guardian.user_model', 'App\Models\User');
            return new $model;
        });

        // Register Socialite provider
        $this->app->register(SocialiteProvider::class);

        // Register Spatie's PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);
    }

    /**
     * Publish package config.
     *
     * @return void
     */
    protected function publishConfig(){
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('guardian.php'),
        ], 'guardian.config');
    }


    /**
     * Publish package migrations.
     *
     * @return void
     */
    protected function publishMigrations()
    {
        $timestamp = now()->format('Y_m_d_His');

        $migrations = [
            'create_two_factor_settings_table.php',
            'create_impersonation_logs_table.php',
        ];

        $migrationFiles = [];

        foreach ($migrations as $index => $migration) {
            $migrationFiles[__DIR__ . "/../database/migrations/{$migration}"] =
                database_path("migrations/{$timestamp}_{$migration}");

            // Increment timestamp safely
            $timestamp = now()->addSeconds(1)->format('Y_m_d_His');
        }

        $this->publishes($migrationFiles, 'guardian.migrations');
    }

    /**
     * Register package events and listeners.
     *
     * @return void
     */
    protected function registerEvents()
    {
        // Example: Register events for login, 2FA, and impersonation
        Event::listen('Arden28\Guardian\Events\UserLoggedIn', 'Arden28\Guardian\Listeners\LogUserLogin');
    }
}
