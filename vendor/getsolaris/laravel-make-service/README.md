# Laravel Make Service

[![Latest Stable Version](http://poser.pugx.org/getsolaris/laravel-make-service/v)](https://packagist.org/packages/getsolaris/laravel-make-service)
[![Total Downloads](http://poser.pugx.org/getsolaris/laravel-make-service/downloads)](https://packagist.org/packages/getsolaris/laravel-make-service)
[![Monthly Downloads](http://poser.pugx.org/getsolaris/laravel-make-service/d/monthly)](https://packagist.org/packages/getsolaris/laravel-make-service)
[![License](http://poser.pugx.org/getsolaris/laravel-make-service/license)](https://packagist.org/packages/getsolaris/laravel-make-service)
[![PHP Version Require](http://poser.pugx.org/getsolaris/laravel-make-service/require/php)](https://packagist.org/packages/getsolaris/laravel-make-service)

A Laravel package that provides an Artisan command to generate service classes, implementing the MVCS (Model-View-Controller-Service) pattern in your Laravel applications.

## Overview

This package simplifies the creation of service layer classes in Laravel applications. It helps you maintain clean architecture by separating business logic from controllers, making your code more maintainable, testable, and reusable.

## Requirements

- PHP 7.1 or higher
- Laravel 5.6.34 or higher (supports up to Laravel 12)

## Installation

Install the package via Composer:

```bash
composer require getsolaris/laravel-make-service --dev
```

The package will automatically register itself using Laravel's package discovery.

## Usage

### Basic Command Syntax

```bash
php artisan make:service {name} {--i : Create a service interface}
```

### Creating a Service Class

To create a simple service class:

```bash
php artisan make:service UserService
```

This will create a service class at `app/Services/UserService.php`:

```php
<?php

namespace App\Services;

class UserService
{
    //
}
```

### Creating a Service with Interface

To create a service class with its corresponding interface:

```bash
php artisan make:service UserService --i
```

This will create two files:

1. **Service class** at `app/Services/UserService.php`:
```php
<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    //
}
```

2. **Interface** at `app/Services/Interfaces/UserServiceInterface.php`:
```php
<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    //
}
```

## Practical Examples

### Example Service Implementation

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        });
    }

    /**
     * Update user information
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function updateUser(User $user, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $user->update($data);
    }

    /**
     * Get user statistics
     *
     * @param User $user
     * @return array
     */
    public function getUserStatistics(User $user): array
    {
        return [
            'posts_count' => $user->posts()->count(),
            'comments_count' => $user->comments()->count(),
            'last_login' => $user->last_login_at,
        ];
    }
}
```

### Using Services in Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()
        ]);
    }

    public function statistics(User $user): JsonResponse
    {
        $stats = $this->userService->getUserStatistics($user);

        return response()->json($stats);
    }
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you discover any issues or have questions, please [create an issue](https://github.com/getsolaris/laravel-make-service/issues).

## Author

- **Solaris** - [getsolaris](https://github.com/getsolaris)
- Email: getsolaris.kr@gmail.com

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).