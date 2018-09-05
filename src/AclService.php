<?php

namespace Napp\Core\Acl;

use Illuminate\Container\Container;
use Napp\Core\Acl\Contract\Role;

/**
 * Class AclService
 * @package Napp\Core\Acl
 */
class AclService implements AclServiceInterface
{
    /**
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @param array|string $roles
     * @return bool
     */
    public function userHasRole(?Role $user, $roles): bool
    {
        if (null === $user) {
            return false;
        }

        if (\is_array($roles)) {
            foreach ($roles as $role) {
                if (true === $this->userHasRole($user, $role)) {
                    return true;
                }
            }
        } else {
            return \in_array($roles, $user->getRoles()->pluck('slug')->all());
        }

        return false;
    }

    /**
     * @param string $permission
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return bool
     */
    public function hasPermission(string $permission, ?Role $user = null): bool
    {
        // no user - no permission
        if (null === $user) {
            return false;
        }

        // admins and superuser have full access
        if ($user->isAdmin() || $user->isSuperUser()) {
            return true;
        }

        $allPermissions = PermissionRegistrar::formatPermissions($user->getMergedPermissions());

        // check if user has permission
        if (false === array_key_exists($permission, $allPermissions)) {
            return false;
        }

        $permissionAction = $allPermissions[$permission];
        //dd($permission, $permissionAction);
        // simple permission check - no closure
        if ($permissionAction === $permission) {
            return true;
        }

        // Calling Instance Methods: ClassName@methodName
        return Container::getInstance()->call($permissionAction);
    }

    /**
     * @param array $permissions
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return bool
     */
    public function hasAnyPermission(array $permissions, ?Role $user = null): bool
    {
        foreach ($permissions as $permission) {
            $result = $this->hasPermission($permission, $user);
            if (true === $result) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $permissions
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return bool
     */
    public function hasAllPermissions(array $permissions, ?Role $user = null): bool
    {
        foreach ($permissions as $permission) {
            $result = $this->hasPermission($permission, $user);
            if (false === $result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function allPermissions(): array
    {
        return PermissionRegistrar::getPermissions();
    }

    /**
     * @param \Napp\Core\Acl\Contract\Role|null $user
     * @return array
     */
    public function getUserPermissions(?Role $user = null): array
    {
        if (null === $user && false === auth()->check()) {
            return [];
        }

        if (null === $user && true === \class_exists(\Napp\Common\Context\Context::class)) {
            $user = app(\Napp\Common\Context\Context::class)->getCMSUser();
        }

        if (null === $user) {
            return [];
        }

        // admin and superuser have full access
        if ($user->isAdmin() || $user->isSuperUser()) {
            return array_keys($this->allPermissions());
        }

        return array_keys(PermissionRegistrar::formatPermissions($user->getMergedPermissions()));
    }

    /**
     * @param string|array $permission
     * @return bool
     */
    public function may($permission): bool
    {
        $user = auth(config('acl.guard'))->user();

        if (\is_array($permission)) {
            return $this->hasAnyPermission($permission, $user);
        }

        return $this->hasPermission($permission, $user);
    }

    /**
     * @param string|array $permission
     * @return bool
     */
    public function maynot($permission): bool
    {
        return !$this->may($permission);
    }

    /**
     * @param array $permissions
     * @return bool
     */
    public function mayall(array $permissions): bool
    {
        return $this->hasAllPermissions($permissions, auth(config('acl.guard'))->user());
    }
}
