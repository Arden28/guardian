<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Guardian Authentication Package Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file allows you to customize the Guardian package's
    | authentication settings, including Socialite providers, 2FA methods,
    | and API behavior.
    |
    */

    // User model configuration
    'user_model' => env('GUARDIAN_USER_MODEL', 'App\Models\User'), // The User model class
    'use_uuid' => env('GUARDIAN_USE_UUID', false), // Use UUIDs for user IDs

    // API settings
    'api' => [
        'prefix' => 'api/auth', // Prefix for API routes
        'middleware' => ['api', 'auth:sanctum'], // Middleware for protected routes
    ],

    // Socialite providers
    'socialite' => [
        'drivers' => [
            'google' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect' => env('GOOGLE_REDIRECT_URI'),
            ],
            'telegram' => [
                'client_id' => env('TELEGRAM_CLIENT_ID'),
                'client_secret' => env('TELEGRAM_CLIENT_SECRET'),
                'redirect' => env('TELEGRAM_REDIRECT_URI'),
                'bot_token' => env('TELEGRAM_BOT_TOKEN'),
            ],
            // Add more providers as needed
        ],
    ],

    // 2FA settings
    'two_factor' => [
        'enabled' => true,
        'methods' => ['email', 'sms', 'totp'], // Supported 2FA methods
        'sms_provider' => 'twilio', // Options: twilio, custom
        'twilio' => [
            'account_sid' => env('TWILIO_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'vonage' => [
            'key' => env('VONAGE_KEY'),
            'secret' => env('VONAGE_SECRET'),
            'from' => env('VONAGE_SMS_FROM'),
        ],
    ],

    // Role and permission settings
    'roles' => [
        'default_role' => 'user', // Default role for new users
        'admin_role' => 'admin', // Role with impersonation permissions
    ],

    // Impersonation settings
    'impersonation' => [
        'enabled' => true,
        'max_duration' => 3600, // Impersonation session duration in seconds
    ],

    // Password Reset
    'password_reset' => [
        'token_expiry' => 3600
    ],
];