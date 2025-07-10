<?php

use Illuminate\Support\Facades\Route;
use Arden28\Guardian\Http\Controllers\AuthController;
use Arden28\Guardian\Http\Controllers\SocialAuthController;
use Arden28\Guardian\Http\Controllers\TwoFactorController;
use Arden28\Guardian\Http\Controllers\ImpersonationController;

/*
|--------------------------------------------------------------------------
| Guardian API Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication, social login, 2FA, and impersonation
| for the Guardian package. They are prefixed with 'api/auth' and use
| middleware defined in the guardian.php config.
|
*/

// Public routes (no authentication required)
Route::group(['prefix' => config('guardian.api.prefix', 'api/auth')], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('guardian.login');
    Route::post('/register', [AuthController::class, 'register'])->name('guardian.register');
    Route::post('/password/reset', [AuthController::class, 'requestPasswordReset'])->name('guardian.password.request');
    Route::post('/password/reset/confirm', [AuthController::class, 'resetPassword'])->name('guardian.password.reset');

    // Social login routes
    Route::get('/social/{provider}', [SocialAuthController::class, 'redirect'])->name('guardian.social.redirect');
    Route::get('/social/{provider}/callback', [SocialAuthController::class, 'callback'])->name('guardian.social.callback');
});

// Protected routes (require Sanctum authentication)
Route::group(['prefix' => config('guardian.api.prefix', 'api/auth'), 'middleware' => config('guardian.api.middleware', ['api', 'auth:sanctum'])], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('guardian.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('guardian.user');

    // 2FA routes
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('guardian.2fa.enable');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('guardian.2fa.verify');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('guardian.2fa.disable');

    // Impersonation routes
    Route::post('/impersonate', [ImpersonationController::class, 'start'])->name('guardian.impersonate.start');
    Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('guardian.impersonate.stop');
});