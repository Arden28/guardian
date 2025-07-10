# Guardian

## Overview

The **Guardian** is a reusable, modular Laravel package designed to provide a comprehensive authentication system for modern web applications, specifically tailored for integration with a React frontend. It offers a fully API-driven authentication solution with support for social logins (via Laravel Socialite, including Telegram), two-factor authentication (2FA) via email, SMS, or TOTP, multi-guard role-based access control, user impersonation, and password reset. The package is built to be highly configurable, extensible, and reusable across different Laravel projects while adhering to best practices for security, scalability, and maintainability.

## Features

1. **Socialite Integration**:
   - Supports login via Google, Telegram, and other platforms using Laravel Socialite.
   - Custom Telegram driver for Telegram Login Widget authentication.
   - Configurable redirect URLs and callback handling for React integration.
   - Extensible for additional Socialite drivers via configuration.

2. **Two-Factor Authentication (2FA)**:
   - Supports email, SMS (via Twilio), and TOTP (authenticator apps like Google Authenticator).
   - Configurable code length, expiry, and rate-limiting (e.g., 5 attempts per minute).
   - API endpoints for enabling, verifying, and disabling 2FA.
   - QR code generation for TOTP setup.

3. **Password Reset**:
   - Allows users to request a password reset token via email.
   - Secure token-based password reset with configurable expiry (default: 1 hour).
   - Rate-limited endpoints to prevent abuse (e.g., 5 attempts per minute).
   - API-driven for React frontend integration.

4. **Multi-Guard Role-Based Access Control**:
   - Integrates with Spatie’s Laravel Permission for role and permission management.
   - Supports multiple guards (e.g., `web`, `api`) with configurable default/admin roles.
   - API endpoints for assigning/removing roles and permissions, and checking user access.
   - Middleware for protecting routes based on roles (`guardian_role`) or permissions (`guardian_permission`).

5. **User Impersonation**:
   - Allows authorized users (e.g., admins) to impersonate others for support or debugging.
   - Secure session management with audit logging in the `impersonation_logs` table.
   - API endpoints for starting and stopping impersonation sessions with token issuance.

6. **API-Driven Endpoints**:
   - RESTful API endpoints for login, registration, social login, 2FA, impersonation, password reset, and role management.
   - Uses Laravel Sanctum for secure token-based authentication.
   - JSON responses optimized for React frontends with consistent structure and error handling.
   - Configurable middleware for protecting routes.

7. **Reusability and Extensibility**:
   - Uses a `GuardianUser` trait to extend the host application’s `User` model, avoiding conflicts.
   - Publishable configuration (`guardian.php`) for customizing Socialite, 2FA, password reset, and role settings.
   - Event-driven design with events like `UserLoggedIn` for custom logic.
   - Compatible with Laravel 8.x, 9.x, and 10.x.

8. **Security Features**:
   - CSRF protection via Sanctum.
   - Rate-limiting for login, 2FA, and password reset requests (configurable via `guardian.two_factor.rate_limit` and `guardian.password_reset.rate_limit`).
   - Secure password hashing and encryption for 2FA secrets.
   - Audit logging for critical actions (e.g., login, impersonation, role changes).

9. **Frontend Integration**:
   - JSON responses designed for React consumption.
   - Supports OAuth 2.0 flows for social logins and Telegram Login Widget.
   - QR code URI for TOTP setup (integrate with `qrcode.react`).
   - Error handling with meaningful messages and status codes.
   - CORS support for cross-origin requests.

## Improvements and Enhancements

1. **Configuration Flexibility**:
   - Comprehensive `guardian.php` config file for Socialite, 2FA, password reset, and role settings.
   - Environment-based configuration for sensitive data (e.g., Twilio, Telegram bot token).

2. **Database Migrations**:
   - Publishable migrations for adding Guardian fields to `users` table (`add_guardian_user`), `two_factor_settings`, `impersonation_logs`, and Spatie’s `roles` and `permissions` tables.
   - Conditional migrations to avoid conflicts with existing `users` tables.
   - Optional UUID support for user IDs via `guardian.use_uuid`.

3. **Event-Driven Architecture**:
   - Dispatches events like `UserLoggedIn` for custom logic (e.g., logging, notifications).
   - Extensible listeners for common tasks.

4. **Rate-Limiting**:
   - 2FA: Configurable via `guardian.two_factor.rate_limit` (default: 5 attempts per 60 seconds).
   - Password Reset: Configurable via `guardian.password_reset.rate_limit` (default: 5 attempts per 60 seconds).
   - Requires host application to register `guardian_2fa` and `guardian_password_reset` middleware in `app/Http/Kernel.php`.

5. **Role Management**:
   - Multi-guard support for roles and permissions (e.g., `web`, `api`).
   - Middleware (`guardian_role`, `guardian_permission`) for protecting routes based on roles/permissions.
   - Requires host application to register middleware in `app/Http/Kernel.php`.

6. **Error Handling**:
   - Standardized JSON error responses (e.g., 400 for validation, 401 for unauthorized, 403 for forbidden).
   - Detailed error messages in development, sanitized in production.

7. **Performance Optimizations**:
   - Caches 2FA codes and password reset tokens for efficient verification.
   - Optimized queries with proper indexing.

8. **Security Enhancements**:
   - Secure random code/token generation for 2FA and password resets.
   - Encrypted storage of TOTP secrets.
   - Session management for impersonation to prevent fixation attacks.

9. **Frontend Support**:
   - JSON responses compatible with React.
   - QR code URI for TOTP setup.
   - CORS support for cross-origin requests.

## Project Structure

```
guardian/
├── src/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   ├── Notifications/
│   ├── Events/
│   ├── Listeners/
│   ├── Models/
│   ├── Services/
│   ├── Socialite/
│   ├── Traits/
│   └── Providers/
├── config/
│   └── guardian.php
├── database/
│   └── migrations/
├── routes/
│   └── api.php
├── tests/
├── composer.json
└── README.md
```

## Installation

1. Install via Composer:
   ```bash
   composer require arden28/guardian
   ```
2. Publish configuration and migrations:
   ```bash
   php artisan vendor:publish --provider="Arden28\Guardian\Providers\GuardianServiceProvider"
   ```
3. Run migrations (includes Spatie’s `roles` and `permissions` tables):
   ```bash
   php artisan migrate
   ```
4. Add the `GuardianUser` trait to your `User` model:
   ```php
   namespace App\Models;

   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Illuminate\Notifications\Notifiable;
   use Arden28\Guardian\Traits\GuardianUser;

   class User extends Authenticatable
   {
       use Notifiable, GuardianUser;
   }
   ```
5. Configure `.env` with necessary keys:
   ```env
   GUARDIAN_USER_MODEL=App\Models\User
   GUARDIAN_USE_UUID=false
   GOOGLE_CLIENT_ID=your-google-client-id
   GOOGLE_CLIENT_SECRET=your-google-client-secret
   GOOGLE_REDIRECT_URI=https://your-app.com/api/auth/social/google/callback
   TELEGRAM_BOT_TOKEN=your-telegram-bot-token
   TELEGRAM_REDIRECT_URI=https://your-app.com/api/auth/social/telegram/callback
   TWILIO_SID=your-twilio-sid
   TWILIO_AUTH_TOKEN=your-twilio-auth-token
   TWILIO_FROM=+1234567890
   ```
6. Register throttle and role/permission middleware in `app/Http/Kernel.php`:
   ```php
   protected $routeMiddleware = [
       'guardian_2fa' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':' . config('guardian.two_factor.rate_limit.attempts', 5) . ',' . config('guardian.two_factor.rate_limit.window', 60),
       'guardian_password_reset' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':' . config('guardian.password_reset.rate_limit.attempts', 5) . ',' . config('guardian.password_reset.rate_limit.window', 60),
       'guardian_role' => \Arden28\Guardian\Http\Middleware\RoleMiddleware::class,
       'guardian_permission' => \Arden28\Guardian\Http\Middleware\PermissionMiddleware::class,
   ];
   ```
7. Seed initial roles and permissions (optional):
   ```php
   use Spatie\Permission\Models\Role;
   use Spatie\Permission\Models\Permission;

   Role::create(['name' => 'admin', 'guard_name' => 'api']);
   Role::create(['name' => 'user', 'guard_name' => 'api']);
   Permission::create(['name' => 'manage_roles', 'guard_name' => 'api']);
   Permission::create(['name' => 'manage_permissions', 'guard_name' => 'api']);
   Permission::create(['name' => 'impersonate', 'guard_name' => 'api']);
   ```

## API Endpoints

- **POST /api/auth/login**: Authenticate a user (email/password).
- **POST /api/auth/register**: Register a new user.
- **POST /api/auth/social/{provider}**: Initiate social login (e.g., Google, Telegram).
- **GET /api/auth/social/{provider}/callback**: Handle social login callback.
- **POST /api/auth/2fa/send**: Send a 2FA code (rate-limited).
- **POST /api/auth/2fa/enable**: Enable 2FA (email, SMS, or TOTP).
- **POST /api/auth/2fa/verify**: Verify a 2FA code (rate-limited).
- **POST /api/auth/2fa/disable**: Disable 2FA.
- **POST /api/auth/impersonate**: Start an impersonation session (requires `impersonate` permission).
- **POST /api/auth/impersonate/stop**: Stop an impersonation session.
- **POST /api/auth/password/reset**: Request a password reset token (rate-limited).
- **POST /api/auth/password/reset/confirm**: Reset password using a token (rate-limited).
- **GET /api/auth/user**: Get authenticated user details.
- **POST /api/auth/roles/assign**: Assign a role to a user (requires `manage_roles` permission).
- **POST /api/auth/roles/remove**: Remove a role from a user (requires `manage_roles` permission).
- **POST /api/auth/permissions/assign**: Assign a permission to a user (requires `manage_permissions` permission).
- **POST /api/auth/permissions/remove**: Remove a permission from a user (requires `manage_permissions` permission).
- **GET /api/auth/check**: Check if a user has a specific role or permission.

## Dependencies

The package requires the following dependencies (included in `composer.json`):
- `laravel/socialite`: For social login integrations.
- `spatie/laravel-permission`: For role and permission management.
- `laravel/sanctum`: For API authentication.
- `spomky-labs/otphp`: For TOTP-based 2FA.
- `twilio/sdk`: For SMS-based 2FA.

## Future Enhancements

- Implement WebAuthn for passwordless authentication.
- Add support for custom user profile fields.
- Support multi-tenant applications.
- Include a Postman collection or OpenAPI spec for API testing.

## License

MIT License

This package provides a secure, flexible, and reusable authentication solution for Laravel applications with React frontends, streamlining development while ensuring robust security and extensibility.