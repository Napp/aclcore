<?php

namespace Napp\Core\Acl\Tests\Unit;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Napp\Core\Acl\AclService;
use Napp\Core\Acl\Middleware\Authorize;
use Napp\Core\Acl\Model\Role;
use Napp\Core\Acl\PermissionRegistrar;
use Napp\Core\Acl\Tests\Stubs\User;
use Napp\Core\Acl\Tests\TestCase;
use Napp\Core\Api\Exceptions\Exceptions\AuthorizationException;

class HelpersTest extends TestCase
{
    /**
     * @var AclService
     */
    protected $acl;

    public function setUp()
    {
        parent::setUp();

        $this->acl = new AclService();
    }

    public function test_acl_helper()
    {
        $this->assertEquals($this->acl, acl());
    }

    public function test_acl_helper_with_permission()
    {
        $this->actingAs($this->superUser);
        $this->assertTrue(acl('users.create'));
    }

    public function test_may_helper()
    {
        $this->assertEquals($this->acl, may());
    }

    public function test_may_helper_with_permission()
    {
        $this->actingAs($this->superUser);
        $this->assertTrue(may('users.create'));
    }




}