<?php

return [
    'models' => [
        'role' => Napp\Core\Acl\Model\Role::class,
        'user' => Illuminate\Foundation\Auth\User::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'users_roles' => 'users_roles',
    ],

    'guard' => 'web'
];