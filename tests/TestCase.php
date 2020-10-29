<?php

namespace Napp\Core\Acl\Tests;

use Napp\Core\Acl\AclServiceProvider;
use Napp\Core\Acl\Model\Role;
use Napp\Core\Acl\PermissionRegistrar;
use Napp\Core\Acl\Tests\Stubs\User;

class TestCase extends \Orchestra\Testbench\TestCase
{
    // User Types
    protected $superUser;
    protected $adminUser;
    protected $managerUser;
    protected $registeredUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestDatabases();
    }

    protected function getPackageProviders($app)
    {
        return [
            AclServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('acl', [
            'models' => [
                'role' => \Napp\Core\Acl\Model\Role::class,
                'user' => \Illuminate\Foundation\Auth\User::class,
            ],
            'table_names' => [
                'roles' => 'roles',
                'users_roles' => 'users_roles',
            ],
            'guard' => 'web',
        ]);
    }

    public function setUpTestDatabases()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/database');

        $this->seedData();

        $this->superUser = User::with('roles')->where('email', 'superuser@example.com')->first();
        $this->adminUser = User::with('roles')->where('email', 'admin@example.com')->first();
        $this->managerUser = User::with('roles')->where('email', 'manager@example.com')->first();
        $this->registeredUser = User::with('roles')->where('email', 'registered@example.com')->first();
    }

    private function seedData()
    {
        // create test roles
        Role::create(['name' => 'superuser', 'slug' => 'superuser', 'permissions' => null, 'access_level' => 1, 'is_default' => null]);
        Role::create(['name' => 'admin', 'slug' => 'admin', 'permissions' => null, 'access_level' => 2, 'is_default' => null]);
        Role::create(['name' => 'manager', 'slug' => 'manager', 'permissions' => [
            'users.view',
            'users.create',
            'users.update',
            'users.foo',
            'users.bar',
            'users.exception',
        ], 'access_level' => 3, 'is_default' => null]);
        Role::create(['name' => 'registered', 'slug' => 'registered', 'permissions' => [], 'access_level' => 3, 'is_default' => 1]);

        // create test users and assign roles
        User::create(['name' => 'Superman', 'email' => 'superuser@example.com'])->roles()->attach(1);
        User::create(['name' => 'Admin', 'email' => 'admin@example.com'])->roles()->attach(2);
        User::create(['name' => 'Manager', 'email' => 'manager@example.com'])->roles()->attach(3);
        User::create(['name' => 'Registered User', 'email' => 'registered@example.com'])->roles()->attach(4);

        // register default permissions for testing
        PermissionRegistrar::register([
            'users.create',
            'users.view',
            'users.update',
            'users.destroy',
            'users.foo' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@foo',
            'users.bar' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@bar',
            'users.exception' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@exception',
        ]);
    }
}
