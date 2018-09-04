<?php

namespace Napp\Core\Acl\Tests\Unit;

use Illuminate\Support\Collection;
use Napp\Core\Acl\AclService;
use Napp\Core\Acl\Model\Role;
use Napp\Core\Acl\Tests\Stubs\User;
use Napp\Core\Acl\Tests\TestCase;

class RolesTest extends TestCase
{
    /**
     * @var AclService
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new AclService();
    }

    public function test_user_has_super_user_role()
    {
        $this->assertTrue($this->service->userHasRole($this->superUser, 'superuser'));
        $this->assertFalse($this->service->userHasRole($this->superUser, 'admin'));
        $this->assertFalse($this->service->userHasRole($this->superUser, 'manager'));
    }


    public function test_user_has_admin_role()
    {
        $this->assertTrue($this->service->userHasRole($this->adminUser, 'admin'));
        $this->assertFalse($this->service->userHasRole($this->adminUser, 'superuser'));
        $this->assertFalse($this->service->userHasRole($this->adminUser, 'manager'));
    }

    public function test_user_has_manager_role()
    {
        $this->assertTrue($this->service->userHasRole($this->managerUser, 'manager'));
        $this->assertFalse($this->service->userHasRole($this->managerUser, 'superuser'));
        $this->assertFalse($this->service->userHasRole($this->managerUser, 'admin'));
    }

    public function test_user_has_many_roles()
    {
        $user = User::create(['name' => 'User with many roles', 'email' => 'many@example.com']);
        $user->roles()->attach([2,3,4]);

        $this->assertEquals([2,3,4], $user->getRoleIds());
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isSuperUser());
        $this->assertEquals(2, $user->getAccessLevels());

        $this->assertFalse($this->service->userHasRole($user, 'superuser'));
        $this->assertTrue($this->service->userHasRole($user, 'admin'));
        $this->assertTrue($this->service->userHasRole($user, 'manager'));
        $this->assertTrue($this->service->userHasRole($user, 'registered'));
        $this->assertTrue($this->service->userHasRole($user, ['manager','registered']));
    }

    public function test_user_has_no_roles()
    {
        $this->assertFalse($this->service->userHasRole($this->managerUser, ['foo','bar']));
    }

    public function test_null_user_has_no_roles()
    {
        $this->assertFalse($this->service->userHasRole(null, 'registered'));
    }

    public function test_delete_user_and_detach_roles()
    {
        $this->actingAs($this->adminUser);

        $user = User::create(['name' => 'new User', 'email' => 'newuser@example.com']);
        $user->roles()->attach([3,4]);
        $userId = $user->getKey();
        $this->assertDatabaseHas('users_roles', ['user_id' => $userId]);

        $this->assertTrue($user->getAttribute('deleteable'));

        $user->delete();
        $this->assertDatabaseMissing('users_roles', ['user_id' => $userId]);
    }

    public function test_delete_role_and_detach_users()
    {
        $role = Role::find(4);
        $this->assertDatabaseHas('users_roles', ['user_id' => $this->registeredUser->getKey()]);

        $role->delete();
        $this->assertDatabaseMissing('users_roles', ['user_id' => $this->registeredUser->getKey()]);
    }
}
