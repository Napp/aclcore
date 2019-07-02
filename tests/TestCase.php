<?php

namespace Napp\Core\Acl\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Napp\Core\Acl\AclServiceProvider;
use Napp\Core\Acl\Model\Role;
use Napp\Core\Acl\PermissionRegistrar;
use Napp\Core\Acl\Tests\Stubs\User;
use Orchestra\Testbench\Database\MigrateProcessor;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public static $migrated = false;

    // User Types
    protected $superUser;
    protected $adminUser;
    protected $managerUser;
    protected $registeredUser;

    /**
     * Set up the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestDatabases();

        $this->superUser = User::with('roles')->where('email', 'superuser@example.com')->first();
        $this->adminUser = User::with('roles')->where('email', 'admin@example.com')->first();
        $this->managerUser = User::with('roles')->where('email', 'manager@example.com')->first();
        $this->registeredUser = User::with('roles')->where('email', 'registered@example.com')->first();
    }

    public function setUpTestDatabases()
    {
        if (false === static::$migrated) {
            $this->dropAllTables();

            $this->migrateTables(__DIR__.'/../database/migrations');

            $this->seedData();

            static::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AclServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('database.default', 'test');

        $app['config']->set('acl', [
            'models' => [
                'role' => \Napp\Core\Acl\Model\Role::class,
                'user' => \Illuminate\Foundation\Auth\User::class,
            ],
            'table_names' => [
                'roles'       => 'roles',
                'users_roles' => 'users_roles',
            ],
            'guard' => 'web',
        ]);

        // MySQL
        $app['config']->set('database.connections.test', [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', '127.0.0.1'),
            'port'        => env('DB_PORT', '3306'),
            'database'    => env('DB_DATABASE', 'db_testing'),
            'username'    => env('DB_USERNAME', 'root'),
            'password'    => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ]);
    }

    /**
     * Drop all tables to start the test with fresh data.
     */
    public function dropAllTables()
    {
        Schema::disableForeignKeyConstraints();
        collect(DB::select('SHOW TABLES'))
            ->map(function (\stdClass $tableProperties) {
                return get_object_vars($tableProperties)[key($tableProperties)];
            })
            ->each(function (string $tableName) {
                Schema::drop($tableName);
            });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Migrate the database.
     *
     * @param $paths
     */
    public function migrateTables($paths)
    {
        $options = is_array($paths) ? $paths : ['--path' => $paths];

        if (isset($options['--realpath']) && is_string($options['--realpath'])) {
            $options['--path'] = [$options['--realpath']];
        }

        $options['--realpath'] = true;

        $this->createTestUserTable();

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();
    }

    private function createTestUserTable()
    {
        // hard code a test user database sense the migrate command can only take one migration path at the same time.
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
        });
    }

    private function seedData()
    {
        // create test roles
        Role::create(['name' => 'superuser', 'slug'=> 'superuser', 'permissions' => null, 'access_level' => 1, 'is_default' => null]);
        Role::create(['name' => 'admin', 'slug'=> 'admin', 'permissions' => null, 'access_level' => 2, 'is_default' => null]);
        Role::create(['name' => 'manager', 'slug'=> 'manager', 'permissions' => [
            'users.view',
            'users.create',
            'users.update',
            'users.foo',
            'users.bar',
            'users.exception',
        ], 'access_level' => 3, 'is_default' => null]);
        Role::create(['name' => 'registered', 'slug'=> 'registered', 'permissions' => [], 'access_level' => 3, 'is_default' => 1]);

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
            'users.foo'       => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@foo',
            'users.bar'       => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@bar',
            'users.exception' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@exception',
        ]);
    }

    /**
     * Begin a database transaction on the testing database.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        $connection = $database->connection(null);
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $connection = $database->connection(null);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollback();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
    }
}
