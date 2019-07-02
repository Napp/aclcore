<?php

namespace Seeds;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $roleClass = app(config('acl.models.role'));
        $roleClass::create(['name' => 'superadmin', 'slug'=> 'superadmin', 'permissions' => null, 'access_level' => 1, 'is_default' => null]);
        $roleClass::create(['name' => 'admin', 'slug'=> 'admin', 'permissions' => null, 'access_level' => 2, 'is_default' => null]);
        $roleClass::create(['name' => 'manager', 'slug'=> 'manager', 'permissions' => ['users.view', 'users.create', 'users.update'], 'access_level' => 3, 'is_default' => null]);
        $roleClass::create(['name' => 'registered', 'slug'=> 'registered', 'permissions' => [], 'access_level' => 3, 'is_default' => 1]);
    }
}
