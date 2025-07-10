# Guardian

## Overview

The **Guardian** is a reusable, modular Laravel package designed to provide a comprehensive authentication system for modern web applications, specifically tailored for integration with a React frontend. It offers an API-driven solution with social logins (via Laravel Socialite, including Telegram), two-factor authentication (2FA) via email, SMS, or TOTP, multi-guard role-based access control (RBAC), user impersonation, and password reset. The package is highly configurable, extensible, and reusable across Laravel projects, adhering to best practices for security, scalability, and maintainability.

## Features

1. **Socialite Integration**:
   - Supports Google, Telegram, and other platforms via Laravel Socialite.
   - Custom Telegram driver for Telegram Login Widget authentication.
   - Configurable redirect URLs and callback handling for React integration.

2. **Two-Factor Authentication (2FA)**:
   - Supports email, SMS (Twilio), and TOTP (e.g., Google Authenticator).
   - Configurable code length, expiry, and rate-limiting (5 attempts per minute).
   - API endpoints for enabling, verifying, and disabling 2FA.
   - QR code generation for TOTP setup.

3. **Password Reset**:
   - Secure token-based password reset with 1-hour expiry.
   - Rate-limited endpoints (5 attempts per minute).
   - Email notifications for reset tokens.

4. **Multi-Guard Role-Based Access Control**:
   - Integrates with Spatie’s Laravel Permission for role/permission management.
   - Supports multiple guards (`web`, `api`) with configurable roles.
   - API endpoints for assigning/removing roles and permissions.
   - Middleware for protecting routes (`guardian_role`, `guardian_permission`).

5. **User Impersonation**:
   - Allows admins to impersonate users for support/debugging.
   - Secure session management with audit logging in `impersonation_logs`.
   - API endpoints for starting/stopping impersonation.

6. **API-Driven Endpoints**:
   - RESTful APIs for authentication, 2FA, password reset, impersonation, and role management.
   - Uses Laravel Sanctum for token-based authentication.
   - JSON responses optimized for React with consistent error handling.

7. **Reusability and Extensibility**:
   - `GuardianUser` trait extends the host application’s `User` model.
   - Publishable configuration (`guardian.php`) for Socialite, 2FA, password reset, and roles.
   - Event-driven design with events like `UserLoggedIn`.
   - Compatible with Laravel 8.x, 9.x, and 10.x.

8. **Security Features**:
   - CSRF protection via Sanctum.
   - Rate-limiting for login, 2FA, and password reset requests.
   - Secure password hashing and TOTP secret encryption.
   - Audit logging for critical actions.

9. **Frontend Integration**:
   - JSON responses for React consumption.
   - OAuth 2.0 support for social logins.
   - QR code URI for TOTP setup (integrate with `qrcode.react`).
   - CORS support for cross-origin requests.

## Installation

1. **Install via Composer**:
   ```bash
   composer require arden28/guardian
   ```

2. **Publish configuration and migrations**:
   ```bash
   php artisan vendor:publish --provider="Arden28\Guardian\Providers\GuardianServiceProvider"
   ```

3. **Run migrations** (includes Spatie’s `roles` and `permissions` tables):
   ```bash
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   php artisan migrate
   ```

4. **Add `GuardianUser` trait to your `User` model**:
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

5. **Configure `.env`**:
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
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.example.com
   MAIL_PORT=587
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-app@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Register middleware in `app/Http/Kernel.php`**:
   ```php
   protected $routeMiddleware = [
       'guardian_2fa' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':' . config('guardian.two_factor.rate_limit.attempts', 5) . ',' . config('guardian.two_factor.rate_limit.window', 60),
       'guardian_password_reset' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':' . config('guardian.password_reset.rate_limit.attempts', 5) . ',' . config('guardian.password_reset.rate_limit.window', 60),
       'guardian_role' => \Arden28\Guardian\Http\Middleware\RoleMiddleware::class,
       'guardian_permission' => \Arden28\Guardian\Http\Middleware\PermissionMiddleware::class,
   ];
   ```

7. **Seed initial roles and permissions** (optional):
   ```php
   use Spatie\Permission\Models\Role;
   use Spatie\Permission\Models\Permission;

   Role::create(['name' => 'admin', 'guard_name' => 'api']);
   Role::create(['name' => 'user', 'guard_name' => 'api']);
   Permission::create(['name' => 'manage_roles', 'guard_name' => 'api']);
   Permission::create(['name' => 'manage_permissions', 'guard_name' => 'api']);
   Permission::create(['name' => 'impersonate', 'guard_name' => 'api']);
   ```

## Configuration

The `guardian.php` configuration file allows customization of:
- **User Model**: Set `user_model` and `use_uuid`.
- **API**: Configure `prefix` and `middleware`.
- **Socialite**: Define providers (e.g., Google, Telegram).
- **2FA**: Enable methods, configure Twilio, and set rate-limiting.
- **Password Reset**: Set token expiry and rate-limiting.
- **Roles**: Define default/admin roles and guards.
- **Impersonation**: Enable and set session duration.

Example `guardian.php`:
```php
return [
    'user_model' => env('GUARDIAN_USER_MODEL', 'App\Models\User'),
    'use_uuid' => env('GUARDIAN_USE_UUID', false),
    'api' => [
        'prefix' => 'api/auth',
        'middleware' => ['api', 'auth:sanctum'],
    ],
    'socialite' => [
        'drivers' => [
            'google' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect' => env('GOOGLE_REDIRECT_URI'),
            ],
            'telegram' => [
                'bot_token' => env('TELEGRAM_BOT_TOKEN'),
                'redirect' => env('TELEGRAM_REDIRECT_URI'),
            ],
        ],
    ],
    'two_factor' => [
        'enabled' => true,
        'methods' => ['email', 'sms', 'totp'],
        'sms_provider' => 'twilio',
        'twilio' => [
            'account_sid' => env('TWILIO_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'code_length' => 6,
        'code_expiry' => 300,
        'rate_limit' => [
            'attempts' => 5,
            'window' => 60,
        ],
        'totp' => [
            'issuer' => env('APP_NAME', 'Guardian'),
            'digits' => 6,
            'period' => 30,
        ],
    ],
    'password_reset' => [
        'token_expiry' => 3600,
        'rate_limit' => [
            'attempts' => 5,
            'window' => 60,
        ],
    ],
    'roles' => [
        'default_role' => 'user',
        'admin_role' => 'admin',
        'guards' => ['web', 'api'],
        'middleware' => [
            'role' => 'guardian_role',
            'permission' => 'guardian_permission',
        ],
    ],
    'impersonation' => [
        'enabled' => true,
        'max_duration' => 3600,
    ],
];
```

## API Reference

All endpoints are prefixed with `/api/auth` (configurable via `guardian.api.prefix`).

### Authentication
- **POST /login**
  - Request: `{ "email": "user@example.com", "password": "password" }`
  - Response (200): `{ "message": "Login successful", "user": {}, "token": "..." }`
  - Response (401): `{ "error": "Invalid credentials" }`
  - Notes: Returns `requires_2fa: true` if 2FA is enabled.

- **POST /register**
  - Request: `{ "name": "User", "email": "user@example.com", "password": "password", "password_confirmation": "password" }`
  - Response (201): `{ "message": "Registration successful", "user": {}, "token": "..." }`

- **POST /logout**
  - Middleware: `auth:sanctum`
  - Response (200): `{ "message": "Logged out successfully" }`

- **GET /user**
  - Middleware: `auth:sanctum`
  - Response (200): `{ "user": {} }`

### Social Login
- **GET /social/{provider}**
  - Redirects to the provider’s OAuth page (e.g., Google, Telegram).

- **GET /social/{provider}/callback**
  - Handles callback and issues a token.
  - Response (200): `{ "message": "Login successful", "user": {}, "token": "..." }`

### Two-Factor Authentication
- **POST /2fa/send**
  - Middleware: `throttle:guardian_2fa`
  - Request: `{ "user_id": 1 }`
  - Response (200): `{ "message": "2FA code sent" }`

- **POST /2fa/enable**
  - Middleware: `auth:sanctum`
  - Request: `{ "method": "email|sms|totp" }`
  - Response (200): `{ "message": "2FA enabled", "qr_code": "..." (for TOTP) }`

- **POST /2fa/verify**
  - Middleware: `throttle:guardian_2fa`
  - Request: `{ "user_id": 1, "code": "123456" }`
  - Response (200): `{ "message": "2FA verified", "token": "..." }`

- **POST /2fa/disable**
  - Middleware: `auth:sanctum`
  - Response (200): `{ "message": "2FA disabled" }`

### Password Reset
- **POST /password/reset**
  - Middleware: `throttle:guardian_password_reset`
  - Request: `{ "email": "user@example.com" }`
  - Response (200): `{ "message": "Password reset token sent" }`

- **POST /password/reset/confirm**
  - Middleware: `throttle:guardian_password_reset`
  - Request: `{ "email": "user@example.com", "token": "...", "password": "new_password", "password_confirmation": "new_password" }`
  - Response (200): `{ "message": "Password reset successful" }`

### Impersonation
- **POST /impersonate**
  - Middleware: `auth:sanctum`, `guardian_permission:impersonate,api`
  - Request: `{ "user_id": 2 }`
  - Response (200): `{ "message": "Impersonation started", "user": {}, "token": "...", "session_id": "..." }`

- **POST /impersonate/stop**
  - Middleware: `auth:sanctum`
  - Response (200): `{ "message": "Impersonation stopped", "user": {}, "token": "..." }`

### Role Management
- **POST /roles/assign**
  - Middleware: `auth:sanctum`, `guardian_permission:manage_roles,api`
  - Request: `{ "user_id": 2, "role": "admin", "guard": "api" }`
  - Response (200): `{ "message": "Role assigned successfully" }`

- **POST /roles/remove**
  - Middleware: `auth:sanctum`, `guardian_permission:manage_roles,api`
  - Request: `{ "user_id": 2, "role": "admin", "guard": "api" }`
  - Response (200): `{ "message": "Role removed successfully" }`

- **POST /permissions/assign**
  - Middleware: `auth:sanctum`, `guardian_permission:manage_permissions,api`
  - Request: `{ "user_id": 2, "permission": "manage_roles", "guard": "api" }`
  - Response (200): `{ "message": "Permission assigned successfully" }`

- **POST /permissions/remove**
  - Middleware: `auth:sanctum`, `guardian_permission:manage_permissions,api`
  - Request: `{ "user_id": 2, "permission": "manage_roles", "guard": "api" }`
  - Response (200): `{ "message": "Permission removed successfully" }`

- **GET /check**
  - Middleware: `auth:sanctum`
  - Request: `{ "role": "admin", "permission": "manage_roles", "guard": "api" }`
  - Response (200): `{ "message": "Check completed", "result": { "has_role": true, "has_permission": false } }`

## React Integration Example

Below is an example of integrating the Guardian package with a React frontend using Axios and `qrcode.react` for TOTP.

```jsx
import React, { useState } from 'react';
import axios from 'axios';
import QRCode from 'qrcode.react';

const api = axios.create({
  baseURL: 'https://your-app.com/api/auth',
  headers: { 'Content-Type': 'application/json' },
});

function AuthComponent() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [token, setToken] = useState('');
  const [qrCode, setQrCode] = useState('');
  const [error, setError] = useState('');

  // Login
  const handleLogin = async () => {
    try {
      const response = await api.post('/login', { email, password });
      if (response.data.requires_2fa) {
        // Handle 2FA flow
        setToken(''); // Clear token until 2FA is verified
      } else {
        setToken(response.data.token);
        localStorage.setItem('token', response.data.token);
      }
    } catch (err) {
      setError(err.response?.data?.error || 'Login failed');
    }
  };

  // Enable TOTP
  const handleEnable2FA = async () => {
    try {
      const response = await api.post('/2fa/enable', { method: 'totp' }, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setQrCode(response.data.qr_code);
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to enable 2FA');
    }
  };

  // Password Reset Request
  const handlePasswordResetRequest = async () => {
    try {
      await api.post('/password/reset', { email });
      setError('Check your email for a reset token');
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to send reset token');
    }
  };

  return (
    <div>
      <h1>Guardian Authentication</h1>
      <div>
        <input
          type="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="Email"
        />
        <input
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder="Password"
        />
        <button onClick={handleLogin}>Login</button>
        <button onClick={handlePasswordResetRequest}>Reset Password</button>
        <button onClick={handleEnable2FA}>Enable 2FA (TOTP)</button>
        {qrCode && <QRCode value={qrCode} />}
        {error && <p style={{ color: 'red' }}>{error}</p>}
      </div>
    </div>
  );
}

export default AuthComponent;
```

**Dependencies**:
```bash
npm install axios qrcode.react
```

**Notes**:
- Store tokens in `localStorage` or a secure alternative.
- Handle 2FA flow by prompting for a code if `requires_2fa` is true.
- Use `qrcode.react` to display TOTP QR codes.

## Testing

The package includes a comprehensive test suite using PHPUnit:
- **Unit Tests**: Cover services (e.g., `RoleService`, `PasswordResetService`).
- **Feature Tests**: Cover API endpoints (authentication, 2FA, password reset, impersonation, roles).
- Run tests:
  ```bash
  composer test
  ```

**Test Coverage**:
- Authentication: Login, registration, logout, user retrieval.
- Password Reset: Token request and confirmation.
- Role Management: Assigning/removing roles and permissions, checking access.
- Impersonation: Starting/stopping sessions (requires additional tests).
- 2FA: Enabling, sending, verifying, and disabling (requires additional tests).

## Dependencies

Included in `composer.json`:
- `laravel/socialite`: Social login integrations.
- `spatie/laravel-permission`: Role and permission management.
- `laravel/sanctum`: API authentication.
- `spomky-labs/otphp`: TOTP-based 2FA.
- `twilio/sdk`: SMS-based 2FA.

## Future Enhancements

- Implement WebAuthn for passwordless authentication.
- Add support for custom user profile fields.
- Support multi-tenant applications.
- Provide a Postman collection or OpenAPI spec.

## License

MIT License

This package provides a secure, flexible, and reusable authentication solution for Laravel applications with React frontends, streamlining development while ensuring robust security and extensibility.