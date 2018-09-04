<?php

namespace Napp\Core\Acl\Tests\Stubs;

class UserPermissions
{
    public function foo()
    {
        return true;
    }

    public function bar()
    {
        return false;
    }

    public function exception()
    {
        throw new \Exception();
    }
}