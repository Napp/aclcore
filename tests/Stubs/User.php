<?php

namespace Napp\Core\Acl\Tests\Stubs;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Napp\Core\Acl\Contract\Role as RoleContract;
use Napp\Core\Acl\Role\HasRole;

class User extends Authenticatable implements RoleContract
{
    use HasRole;

    public $timestamps = false;

    protected $guarded = [];
}
