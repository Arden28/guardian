# Guardian

## Overview

The **Guardian** is a reusable, modular Laravel package designed to provide a comprehensive authentication system for modern web applications, specifically tailored for integration with a React frontend. It offers a fully API-driven authentication solution with support for social logins (via Laravel Socialite, including Telegram), two-factor authentication (2FA) via email or SMS, multi-guard role-based access control, and user impersonation. The package is built to be highly configurable, extensible, and reusable across different Laravel projects while adhering to best practices for security, scalability, and maintainability.

## Features

1. **Socialite Integration**:

   - Supports login via popular social platforms (Google, Facebook, Twitter, GitHub, Telegram, etc.) using Laravel Socialite.
   - Extensible to allow additional Socialite drivers for custom or niche providers.
   - Configurable redirect URLs and callback handling for seamless integration with React frontends.
   - Telegram-specific authentication with support for bot-based login flows (e.g., Telegram Login Widget).

2. **Two-Factor Authentication (2FA)**:

   - Supports 2FA via email or SMS, with configurable providers (e.g., Twilio, SendGrid, or custom services).
   - Time-based one-time password (TOTP) support for authenticator apps (e.g., Google Authenticator, Authy).
   - Flexible 2FA setup: users can enable/disable 2FA and choose their preferred method.
   - Rate-limiting and retry mechanisms to prevent abuse.
   - API endpoints for initiating and verifying 2FA challenges.

3. **API-Driven Endpoints**:

   - Comprehensive RESTful API endpoints for all authentication-related actions (login, logout, register, password reset, 2FA, impersonation, etc.).
   - JSON Web Token (JWT) or Laravel Sanctum for secure API authentication.
   - Response formats optimized for React frontend consumption (e.g., consistent JSON structure, error handling).
   - Middleware for protecting routes and handling token refresh.

4. **Multi-Guard Role-Based Access Control**:

   - Supports multiple authentication guards (e.g., web, api, admin) for different user types.
   - Role and permission management integrated with popular packages like Spatie's Laravel Permission.
   - Granular control over access to API endpoints based on roles (e.g., admin, user, moderator).
   - Configurable role assignment during registration or via admin actions.

5. **User Impersonation**:

   - Allows authorized users (e.g., admins) to impersonate other users for debugging or support purposes.
   - Secure implementation with session tracking and audit logging.
   - API endpoints for starting and stopping impersonation sessions.
   - Configurable permissions to restrict who can impersonate others.

6. **Reusability and Extensibility**:

   - Modular architecture with service providers, middleware, and configuration files.
   - Publishable configuration files for customizing Socialite providers, 2FA settings, and API behavior.
   - Event-driven design for easy extension (e.g., events for login, 2FA verification, impersonation).
   - Well-documented code with clear interfaces for adding custom functionality.
   - Compatibility with Laravel 8.x, 9.x, and 10.x.

7. **Security Features**:

   - CSRF protection for API endpoints (via Sanctum).
   - Rate-limiting for login attempts and 2FA requests.
   - Secure password hashing and encryption for sensitive data.
   - Audit logging for critical actions (e.g., login, impersonation, role changes).
   - Support for secure HTTP headers and CORS for React frontend integration.

8. **Frontend Integration**:

   - API responses designed for React frontend consumption (e.g., JSON with consistent structure).
   - Support for OAuth 2.0 flows for social logins.
   - Error handling with meaningful messages and status codes for frontend display.
   - Example React hooks or utilities for interacting with the package's API (provided in documentation).

## Improvements and Enhancements

To make the package more robust, reusable, and developer-friendly, theസ

System: **friendly, here are several improvements to ensure the Guardian is robust, reusable, and developer-friendly:**

 1. **Configuration Flexibility**:

    - Provide a comprehensive `config/auth-package.php` file for customizing Socialite providers, 2FA providers, API settings, and role configurations.
    - Allow environment-based configuration for sensitive data (e.g., API keys for Twilio or Telegram).

 2. **Database Migrations**:

    - Include publishable migrations for necessary tables (e.g., users, roles, permissions, 2FA settings, impersonation logs).
    - Support soft deletes for user records to allow recovery of deleted accounts.
    - Use UUIDs for user IDs to enhance security and portability.

 3. **Event-Driven Architecture**:

    - Dispatch events for key actions (e.g., `UserLoggedIn`, `TwoFactorVerified`, `ImpersonationStarted`) to allow developers to hook into custom logic.
    - Provide listeners for common tasks (e.g., sending welcome emails, logging activities).

 4. **Testing and Documentation**:

    - Include a full test suite (unit and integration tests) using PHPUnit or Pest.
    - Provide detailed documentation with installation instructions, API endpoint details, and examples for React integration.
    - Include a Postman collection or OpenAPI specification for API testing.

 5. **Error Handling**:

    - Standardize API error responses with consistent status codes and messages (e.g., 400 for validation errors, 401 for unauthorized access).
    - Include detailed error messages for debugging in development mode, with sanitized messages in production.

 6. **Performance Optimizations**:

    - Use caching (e.g., Redis, Memcached) for frequently accessed data like user roles or Socialite configurations.
    - Optimize database queries with proper indexing and eager loading.

 7. **Internationalization (i18n)**:

    - Support multi-language error messages and email/SMS templates.
    - Allow developers to override default templates for emails and SMS.

 8. **Security Enhancements**:

    - Implement secure session management for impersonation to prevent session fixation attacks.
    - Use secure random string generation for tokens and 2FA codes.
    - Support encrypted storage of sensitive user data (e.g., 2FA secrets).

 9. **Frontend Support**:

    - Provide a sample React frontend repository demonstrating integration with the package.
    - Include TypeScript types for API responses to improve React developer experience.
    - Support CORS configuration for cross-origin requests from React apps.

10. **Extensibility**:

    - Allow developers to register custom Socialite drivers via configuration.
    - Provide a plugin system for adding custom 2FA providers (e.g., custom SMS gateways).
    - Support middleware customization for API routes.

## Project Structure

The package will follow a standard Laravel package structure for easy integration:

```
laravel-auth-package/
├── src/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/ 
│   │   └── Resources/
│   ├── Events/
│   ├── Listeners/
│   ├── Models/
│   ├── Services/
│   └── Providers/
├── config/
│   └── auth-package.php
├── database/
│   └── migrations/
├── routes/
│   └── api.php
├── tests/
├── composer.json
└── README.md
```

## Installation

1. Install via Composer: `composer require vendor/laravel-auth-package`.
2. Publish configuration and migrations: `php artisan vendor:publish --provider="Vendor\AuthPackageServiceProvider"`.
3. Run migrations: `php artisan migrate`.
4. Configure `.env` with necessary keys (e.g., Socialite, Twilio, Sanctum).
5. Mount API routes in `routes/api.php` or use the package's provided routes.

## API Endpoints

- **POST /api/auth/login**: Authenticate a user (email/password or social login overstated: **POST /api/auth/social/{provider}**: Social login initiation (e.g., Google, Telegram).
- **POST /api/auth/2fa**: Initiate or verify 2FA challenges.
- **POST /api/auth/impersonate**: Start an impersonation session.
- **POST /api/auth/impersonate/stop**: Stop an impersonation session.
- **POST /api/auth/register**: Register a new user.
- **POST /api/auth/password/reset**: Initiate a password reset.
- **POST /api/auth/password/reset/confirm**: Confirm a password reset.

## Dependencies

- `laravel/socialite`: For social login integrations.
- `spatie/laravel-permission`: For role and permission management.
- `laravel/sanctum`: For API authentication.
- `twilio/sdk` or similar: For SMS-based 2FA.
- `google/authenticator`: For TOTP-based 2FA.

## Future Enhancements

- Add support for WebAuthn for passwordless authentication.
- Implement rate-limiting policies configurable via the config file.
- Support for custom user profile fields via configuration.
- Add support for multi-tenant applications.

## License

MIT License

This package aims to provide a secure, flexible, and reusable authentication solution for Laravel applications with React frontends, streamlining the development process while ensuring robust security and extensibility.