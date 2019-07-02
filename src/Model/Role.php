<?php

namespace Napp\Core\Acl\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role.
 */
class Role extends Model
{
    /**
     * @var array
     */
    protected $casts = ['permissions' => 'json'];

    /**
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'permissions',
        'slug',
        'access_level',
        'access_level_parent',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('acl.models.user'), config('acl.table_names.users_roles'));
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    public function delete()
    {
        $this->users()->detach();

        return parent::delete();
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param null|array $permissions
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setPermissionsAttribute(?array $permissions)
    {
        if (\is_array($permissions)) {
            $this->attributes['permissions'] = json_encode($permissions);

            return;
        }

        $this->attributes['permissions'] = null;
    }
}
