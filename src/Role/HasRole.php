<?php

namespace Napp\Core\Acl\Role;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait HasRole
 * @package Napp\Core\Acl\Role
 */
trait HasRole
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('acl.models.role'), config('acl.table_names.users_roles'));
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $this->roles()->detach();

        return parent::delete();
    }

    /**
     * @return bool
     */
    public function getDeleteableAttribute(): bool
    {
        $user = auth()->user();

        return $user->getAuthIdentifier() != $this->attributes['id']
            && $user->roles[0]->access_level <= $this->roles[0]->access_level;
    }

    /**
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getRoleIds(): array
    {
        $ids = [];

        foreach ($this->getRoles() as $role) {
            $ids[] = $role->id;
        }

        return $ids;
    }

    /**
     * Returns an array of merged permissions for each group the user is in.
     *
     * @return array
     */
    public function getMergedPermissions(): array
    {
        $permissions = [];
        foreach ($this->getRoles() as $role) {
            $permissions = \array_merge($permissions, $role->getPermissions() ?? []);
        }

        // merge with users own permissions
        return \array_merge($permissions, $this->getPermissions());
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isSuperUser(): bool
    {
        return \in_array(1, $this->getRoleIds(), true);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return \in_array(2, $this->getRoleIds(), true);
    }

    /**
     * @return int Lowest value of the users groups access levels
     */
    public function getAccessLevels(): int
    {
        $accessLevel = PHP_INT_MAX;

        foreach ($this->getRoles() as $role) {
            $role->access_level_parent = (null === $role->access_level_parent) ? PHP_INT_MAX : $role->access_level_parent;
            $accessLevel = $this->getLowestAccessLevel($role->access_level, $role->access_level_parent, $accessLevel);
        }

        return $accessLevel;
    }

    /**
     * @param int $level
     * @param int $parent
     * @param int $current
     * @return int
     */
    protected function getLowestAccessLevel($level, $parent, $current): int
    {
        return min([$level, $parent, $current]);
    }
}