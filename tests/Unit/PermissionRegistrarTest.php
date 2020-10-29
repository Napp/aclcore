<?php

namespace Napp\Core\Acl\Tests\Unit;

use Napp\Core\Acl\PermissionRegistrar;
use Napp\Core\Acl\Tests\TestCase;

class PermissionRegistrarTest extends TestCase
{
    public function test_it_can_register_permissions()
    {
        $expected = [
            'users.create' => 'users.create',
            'users.view' => 'users.view',
            'users.update' => 'users.update',
            'users.destroy' => 'users.destroy',
            'users.foo' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@foo',
            'users.bar' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@bar',
            'users.exception' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@exception',
            'foo.bar' => 'foo.bar',
            'woo.hoo' => 'woo.hoo',
        ];

        PermissionRegistrar::register([
            'foo.bar',
            'woo.hoo',
        ]);

        $this->assertEquals($expected, PermissionRegistrar::getPermissions());
    }

    public function test_it_can_register_permissions_multiple_times()
    {
        $expected = [
            'users.create' => 'users.create',
            'users.view' => 'users.view',
            'users.update' => 'users.update',
            'users.destroy' => 'users.destroy',
            'users.foo' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@foo',
            'users.bar' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@bar',
            'users.exception' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@exception',
            'foo.bar' => 'foo.bar',
            'woo.hoo' => 'woo.hoo',
            'foo.bar2' => 'foo.bar2',
            'wow.awesome' => 'wow.awesome',
        ];

        PermissionRegistrar::register([
            'foo.bar',
            'woo.hoo',
        ]);

        PermissionRegistrar::register([
            'foo.bar2',
            'foo.bar', //duplicate
            'wow.awesome',
        ]);

        $this->assertEquals($expected, PermissionRegistrar::getPermissions());
    }

    public function test_it_can_format_user_permissions()
    {
        $userPermissions = [
            'foo.bar',
        ];

        $expected = [
            'foo.bar' => 'SomeClassClosure@create',
        ];

        PermissionRegistrar::register([
            'foo.bar' => 'SomeClassClosure@create',
            'woo.hoo',
            'extra.perm',
        ]);

        $this->assertEquals($expected, PermissionRegistrar::formatPermissions($userPermissions));
    }

    public function test_it_can_format_user_permissions_2()
    {
        $userPermissions = [
            'extra.perm',
        ];

        $expected = [
            'extra.perm' => 'extra.perm',
        ];

        PermissionRegistrar::register([
            'foo.bar' => 'SomeClassClosure@create',
            'woo.hoo',
            'extra.perm',
        ]);

        $this->assertEquals($expected, PermissionRegistrar::formatPermissions($userPermissions));
    }
}
