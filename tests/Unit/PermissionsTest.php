<?php

namespace Napp\Core\Acl\Tests\Unit;

use Napp\Core\Acl\AclService;
use Napp\Core\Acl\PermissionRegistrar;
use Napp\Core\Acl\Tests\TestCase;

class PermissionsTest extends TestCase
{
    /**
     * @var AclService
     */
    protected $acl;

    public function setUp(): void
    {
        parent::setUp();

        PermissionRegistrar::register([
            'users.create',
            'users.view',
            'users.update',
            'users.destroy',
            'users.foo' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@foo',
            'users.bar' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@bar',
            'users.exception' => 'Napp\Core\Acl\Tests\Stubs\UserPermissions@exception',
        ]);
        $this->acl = new AclService();
    }

    public function test_superuser_has_all_permissions()
    {
        $this->assertEquals(array_keys($this->acl->allPermissions()), $this->acl->getUserPermissions($this->superUser));
    }

    public function test_registered_user_has_no_permissions()
    {
        $this->assertEquals([], $this->acl->getUserPermissions($this->registeredUser));
    }

    public function test_superuser_has_full_access()
    {
        $this->actingAs($this->superUser);
        $this->assertTrue($this->acl->may('users.create'));
    }

    public function test_admin_has_full_access()
    {
        $this->actingAs($this->adminUser);
        $this->assertTrue($this->acl->may('users.create'));
    }

    public function test_manager_has_no_access_to_delete()
    {
        $this->actingAs($this->managerUser);
        $this->assertFalse($this->acl->may('users.destroy'));
    }

    public function test_registered_has_no_access()
    {
        $this->actingAs($this->registeredUser);
        $this->assertFalse($this->acl->may('users.view'));
    }

    public function test_manager_has_access_through_any()
    {
        $this->actingAs($this->managerUser);
        $this->assertTrue($this->acl->may(['users.destroy', 'users.create']));
    }

    public function test_manager_no_access_incorrect_permission()
    {
        $this->actingAs($this->managerUser);
        $this->assertFalse($this->acl->may('random.stuff'));
    }

    public function test_manager_no_access_incorrect_permission_any()
    {
        $this->actingAs($this->managerUser);
        $this->assertFalse($this->acl->may(['random.stuff', 'random.second']));
    }

    public function test_manager_no_access_using_maynot()
    {
        $this->actingAs($this->managerUser);
        $this->assertTrue($this->acl->maynot('users.destroy'));
    }

    public function test_manager_no_access_using_maynot_any()
    {
        $this->actingAs($this->managerUser);
        $this->assertTrue($this->acl->maynot(['random.stuff', 'random.second']));
    }

    public function test_manager_has_all_permissions()
    {
        $this->assertTrue($this->acl->hasAllPermissions(['users.view', 'users.create'], $this->managerUser));
    }

    public function test_manager_does_not_have_all_permissions()
    {
        $this->assertFalse($this->acl->hasAllPermissions(['users.view', 'users.destroy'], $this->managerUser));
    }

    public function test_null_user_has_no_permission_of_all()
    {
        $this->assertFalse($this->acl->hasAllPermissions(['users.view', 'users.destroy'], null));
    }

    public function test_null_user_has_no_permission()
    {
        $this->assertFalse($this->acl->hasPermission('users.view', null));
    }

    public function test_exec_closure_permissions()
    {
        $this->actingAs($this->managerUser);
        $this->assertTrue($this->acl->may('users.foo'));
    }

    public function test_exec_falsify_closure_permissions()
    {
        $this->actingAs($this->managerUser);
        $this->assertFalse($this->acl->may('users.bar'));
    }

    public function test_exec_throw_exception_in_closure_permissions()
    {
        $this->expectException(\Exception::class);
        $this->actingAs($this->managerUser);
        $this->assertFalse($this->acl->may('users.exception'));
    }

    public function test_get_permissions_from_null_user()
    {
        $this->assertEmpty($this->acl->getUserPermissions(null));
    }

    public function test_get_permissions_from_registered_user()
    {
        $this->actingAs($this->registeredUser);
        $this->assertEmpty($this->acl->getUserPermissions(null));
    }

    public function test_context_mock()
    {
        $mock = $this->getMockBuilder(\Napp\Common\Context\Context::class)
            ->setMethods(['getCMSUser'])->getMock();
        app()->instance(\Napp\Common\Context\Context::class, $mock);

        $this->actingAs($this->registeredUser);
        $this->assertEmpty($this->acl->getUserPermissions(null));
    }
}
