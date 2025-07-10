<?php

namespace Arden28\Guardian\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class SocialiteProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('telegram', function ($app) {
            $config = config('guardian.socialite.drivers.telegram');
            return Socialite::buildProvider(
                \Arden28\Guardian\Socialite\TelegramProvider::class,
                $config
            );
        });
    }
}