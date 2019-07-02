<?php

if (!function_exists('acl')) {
    /**
     * @return \Napp\Core\Acl\AclService
     */
    function acl()
    {
        $arguments = func_get_args();
        if (empty($arguments)) {
            return app(\Napp\Core\Acl\AclService::class);
        }

        return app(\Napp\Core\Acl\AclService::class)->may($arguments[0]);
    }
}

if (!function_exists('may')) {
    /**
     * @return \Napp\Core\Acl\AclService
     */
    function may()
    {
        $arguments = func_get_args();
        if (empty($arguments)) {
            return app(\Napp\Core\Acl\AclService::class);
        }

        return app(\Napp\Core\Acl\AclService::class)->may($arguments[0]);
    }
}

if (!function_exists('maynot')) {
    /**
     * @param string|array $permission
     *
     * @return \Napp\Core\Acl\AclService
     */
    function maynot($permission)
    {
        return app(\Napp\Core\Acl\AclService::class)->maynot($permission);
    }
}

if (!function_exists('mayall')) {
    /**
     * @param array $permissions
     *
     * @return \Napp\Core\Acl\AclService
     */
    function mayall(array $permissions)
    {
        return app(\Napp\Core\Acl\AclService::class)->mayall($permissions);
    }
}

if (!function_exists('has_role')) {
    /**
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @param array|string                      $roles
     *
     * @return \Napp\Core\Acl\AclService
     */
    function has_role($user, $roles)
    {
        return app(\Napp\Core\Acl\AclService::class)->userHasRole($user, $roles);
    }
}
