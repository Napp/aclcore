<?php

namespace Napp\Core\Acl;

class PermissionRegistrar
{
    /** @var array */
    protected static $permissions = [];

    /** @var PermissionRegistrar */
    private static $instance;

    /**
     * @codeCoverageIgnore
     * PermissionRegistrar constructor.
     */
    private function __construct()
    {
    }

    /**
     * @codeCoverageIgnore
     * Singleton class instance.
     * @return PermissionRegistrar
     */
    public static function getInstance(): PermissionRegistrar
    {
        if (!null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registers permissions
     *
     * @param array $permissions
     * @return void
     */
    public static function register(array $permissions): void
    {
        $permissions = self::format($permissions);

        self::$permissions = array_merge(self::$permissions, $permissions);
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return array
     */
    public static function getPermissions(): array
    {
        return self::$permissions;
    }

    /**
     * @param array $permissions
     * @return array
     */
    protected static function format(array $permissions): array
    {
        foreach ($permissions as $key => $permission) {
            if (\is_numeric($key)) {
                $permissions[$permission] = $permission;
                unset($permissions[$key]);
            }
        }

        return $permissions;
    }

    /**
     * Replace user permission with PermissionRegistrar
     * @param array $userPermissions
     * @return array
     */
    public static function formatPermissions(array $userPermissions): array
    {
        $userPermissions = self::format($userPermissions);

        foreach ($userPermissions as $key => $permission) {
            $userPermissions[$key] = self::$permissions[$key];
        }

        return $userPermissions;
    }
}