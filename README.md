# Napp ACL Core

[![Build Status](https://travis-ci.org/Napp/dbalcore.svg?branch=master)](https://travis-ci.org/Napp/aclcore)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Napp/aclcore/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Napp/aclcore/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Napp/aclcore/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Napp/aclcore/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


Roles and Permissions for Laravel optimized for performance. 
Every permission is registered through code instead of pivot tables. 
This results in great performance.


## Install

```bash
composer require napp/aclcore
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Napp\Core\Acl\AclServiceProvider" --tag="config"
```

When published - then review it and change accordingly to your applications. The config files `config/acl.php` contains:

```php
return [
    /**
     * Define which Eloquent models used by the package
     */
    'models' => [
        'role' => Napp\Core\Acl\Model\Role::class,
        'user' => Illuminate\Foundation\Auth\User::class,
    ],

    /**
     * Table names for the package
     */
    'table_names' => [
        'roles' => 'roles',
        'users_roles' => 'users_roles',
    ],

    /**
     * The default guard used to authorize users
     */
    'guard' => 'web'
];
```


## Usage

Add `HasRole` trait to your User model:

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Napp\Core\Acl\Contract\Role as RoleContract;
use Napp\Core\Acl\Role\HasRole;

class User extends Authenticatable implements RoleContract
{
    use HasRole;
}
```

### Register Permissions

Register simple permissions in your app.

```php
Napp\Core\Acl\PermissionRegistrar::register([
    'users.create', 
    'users.view'
]);
```

Register permissions with Closure.

```php
Napp\Core\Acl\PermissionRegistrar::register([
    'users.create' => 'My\App\Users\Permissions@create',
    'users.update' => 'My\App\Users\Permissions@edit',
    'users.view'
]);
```

### Middleware

Add the middleware to `App/Http/Kernal.php`

```php
protected $routeMiddleware = [
    'may' => \Napp\Core\Acl\Middleware\Authorize::class,
```

use it like this:

```php
Route::get('users', ['uses' => 'UsersController@index'])->middleware('may:users.view');
```


### Usage in php code

```php

// authorize a single permission
if (may('users.view')) {
    // do something
}

// authorize if **any** of the permissions are valid
if (may(['users.view', 'users.create'])) {
    // do something
}

// authorize if **all** of the permissions are valid
if (mayall(['users.view', 'users.create'])) {
    // do something
}

// reverse - not logic
if (maynot('users.view')) {
    return abort();
}

// check for user role
if (has_role($user, 'manager')) {
    // do something
}

// check if user has many roles
if (has_role($user, ['support', 'hr'])) {
    // do something
}

```


### Usage in Blade

`may` is equivalent to default `can` from Laravel.

```php
@may('users.create')
    <a href="my-link">Create</a>
@endmay
```

Check if user has **any** of the permissions

```php
@may(['users.create', 'users.update'])
    <a href="my-link">Create</a>
@endmay
```

Check if user have **all** of the permissions

```php
@mayall(['users.create', 'users.update'])
    <a href="my-link">Create</a>
@endmayall
```

Use `maynot` for reverse logic

```php
@maynot('users.create')
    <a href="my-link">Create</a>
@endmaynot
```

Check if user has a specific role

```php
@hasrole('admin')
    <a href="my-link">Create</a>
@endhasrole
```


See PHPUnit tests for more examples and usage.