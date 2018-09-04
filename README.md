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

When published - then review it and change accordingly to your applications.


## Usage

Add trait to your User model:

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


### In php code

```php
if (!may('users.view')) {
    return abort();
}
```


### Blade Extensions

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