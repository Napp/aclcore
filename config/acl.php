<?php

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