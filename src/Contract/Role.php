<?php

namespace Napp\Core\Acl\Contract;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface Role
 * @package Napp\Core\Acl\Contract
 */
interface Role
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete();

    /**
     * @return bool
     */
    public function getDeleteableAttribute(): bool;

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles();

    /**
     * @return array
     */
    public function getRoleIds(): array;

    /**
     * Returns an array of merged permissions for each group the user is in.
     *
     * @return array
     */
    public function getMergedPermissions(): array;

    /**
     * @return array
     */
    public function getPermissions(): array;

    /**
     * @return bool
     */
    public function isSuperUser(): bool;

    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return int Lowest value of the users groups access levels
     */
    public function getAccessLevels(): int;
}