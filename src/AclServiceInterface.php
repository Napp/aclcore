<?php

namespace Napp\Core\Acl;

use \Napp\Core\Acl\Contract\Role;

/**
 * Interface AclServiceInterface
 * @package Napp\Core\Acl
 */
interface AclServiceInterface
{
    /**
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @param array|string $roles
     * @return bool
     */
    public function userHasRole(?Role $user, $roles): bool;

    /**
     * @param string $permission
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return bool
     */
    public function hasPermission(string $permission, ?Role $user = null): bool;

    /**
     * @param array $permissions
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return bool
     */
    public function hasAnyPermission(array $permissions, ?Role $user = null): bool;

    /**
     * @param array $permissions
     * @param \Illuminate\Foundation\Auth\User|null $user
     * @return bool
     */
    public function hasAllPermissions(array $permissions, ?Role $user = null): bool;

    /**
     * @return array
     */
    public function allPermissions(): array;

    /**
     * @param string|array $permission
     * @return bool
     */
    public function may($permission): bool;

    /**
     * @param string|array $permission
     * @return bool
     */
    public function maynot($permission): bool;

    /**
     * @param array $permissions
     * @return bool
     */
    public function mayall(array $permissions): bool;
}