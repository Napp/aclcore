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