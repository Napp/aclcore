<?php

namespace Napp\Core\Acl\Tests\Unit;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Napp\Core\Acl\AclService;
use Napp\Core\Acl\Middleware\Authorize;
use Napp\Core\Acl\Tests\TestCase;
use Napp\Core\Api\Exceptions\Exceptions\AuthorizationException;

class AuthorizeTest extends TestCase
{
    /**
     * @var AclService
     */
    protected $acl;
    protected $authMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->authMiddleware = new Authorize();

        $this->acl = new AclService();
    }

    public function test_guest_user_cannot_access()
    {
        $this->assertEquals($this->runMiddleware($this->authMiddleware, 'users.create'), 401);
    }

    public function test_manager_user_can_access()
    {
        $this->actingAs($this->managerUser);
        $this->assertEquals($this->runMiddleware($this->authMiddleware, 'users.create'), 200);
    }

    public function test_manager_user_cannot_delete_user_access()
    {
        $this->actingAs($this->managerUser);
        $this->assertEquals($this->runMiddleware($this->authMiddleware, 'users.destroy'), 403);
    }

    protected function runMiddleware($middleware, $parameters)
    {
        try {
            return $middleware->handle(new Request(), function () {
                return (new Response())->setContent('<html></html>');
            }, $parameters)->status();
        } catch (AuthenticationException $e) {
            return 401;
        } catch (AuthorizationException $e) {
            return $e->getResponseCode();
        }
    }


}