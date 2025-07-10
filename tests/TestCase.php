<?php

namespace Arden28\Guardian\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Arden28\Guardian\Providers\GuardianServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            GuardianServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set up in-memory SQLite database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up Guardian configuration
        $app['config']->set('guardian.user_model', \Arden28\Guardian\Models\User::class);
        $app['config']->set('guardian.use_uuid', false);
        $app['config']->set('guardian.api.middleware', ['api']);
        $app['config']->set('guardian.two_factor.enabled', true);
        $app['config']->set('guardian.two_factor.methods', ['email', 'sms', 'totp']);
        $app['config']->set('guardian.password_reset.token_expiry', 3600);
        $app['config']->set('guardian.roles.guards', ['api']);
        $app['config']->set('guardian.roles.default_role', 'user');
        $app['config']->set('guardian.roles.admin_role', 'admin');
        $app['config']->set('guardian.impersonation.enabled', true);

        // Set up Sanctum
        $app['config']->set('auth.providers.users.model', \Arden28\Guardian\Models\User::class);
        $app['config']->set('auth.guards.api.driver', 'sanctum');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish Spatie migrations
        $this->artisan('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);
        $this->artisan('migrate', ['--database' => 'testing']);
    }
}