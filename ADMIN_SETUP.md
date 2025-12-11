# Admin Dashboard Setup

This document explains how to set up and use the admin dashboard functionality.

## Features

- Role-based access control (RBAC)
- Web-based admin dashboard with system metrics
- API endpoints for admin functionality
- Secure authentication with both web sessions and API tokens
- Dashboard shows:
  - Total registered users
  - Total files uploaded
  - Recent users
  - Recent documents

## Admin Account

A default admin account is created through the database seeder:

- **Email:** mahekarim@gmail.com
- **Password:** 123456
- **Role:** admin

> **Security Note:** These credentials are for development only. In production, use a strong password and consider changing the email address.

## Access Methods

### Web Interface

1. **Admin Login Page:**
   - URL: `http://localhost:8000/admin/login`
   - Use the admin credentials to log in
   - After successful login, you'll be redirected to the dashboard

2. **Admin Dashboard:**
   - URL: `http://localhost:8000/admin/dashboard`
   - Accessible only after authentication
   - Shows system metrics and recent activity

### API Endpoints

#### Authentication
- `POST /api/login` - Login with email and password
- `POST /api/logout` - Logout (requires authentication)
- `GET /api/user` - Get current authenticated user

#### Admin Dashboard
- `GET /api/admin/dashboard` - Get dashboard metrics (requires admin role)

## Usage Examples

### Web Interface
Simply navigate to `http://localhost:8000/admin/login` in your browser and log in with the admin credentials.

### API Usage

1. **Login as Admin:**
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email": "mahekarim@gmail.com", "password": "123456"}'
   ```

2. **Access Dashboard API:**
   ```bash
   curl -X GET http://localhost:8000/api/admin/dashboard \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
   ```

## Setup Instructions

1. Run the migration to add the role field:
   ```bash
   php artisan migrate
   ```

2. Run the seeder to create the admin account:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

3. Run tests to verify functionality:
   ```bash
   php artisan test tests/Feature/AdminWebTest.php
   php artisan test tests/Feature/AdminApiTest.php
   ```

## Security Implementation

- Passwords are hashed using Laravel's default bcrypt hashing
- Web authentication uses Laravel's session-based authentication
- API authentication uses Laravel Sanctum tokens
- Admin middleware ensures only users with 'admin' role can access admin routes
- All admin endpoints require both authentication and admin role
- Custom authentication middleware redirects to the admin login page for web routes

## Testing

The test suite includes:
- Web authentication tests (login, logout, access control)
- API authentication tests (valid/invalid credentials)
- Authorization tests (admin vs regular user access)
- Dashboard metrics verification
- UI rendering tests

Run all tests with:
```bash
php artisan test
```

Run specific test suites:
```bash
php artisan test tests/Feature/AdminWebTest.php  # Web interface tests
php artisan test tests/Feature/AdminApiTest.php  # API tests