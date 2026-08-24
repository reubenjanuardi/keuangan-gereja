<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('portal.role.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('portal.role.view');
    }

    public function create(User $user): bool
    {
        return $user->can('portal.role.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('portal.role.manage');
    }

    public function delete(User $user, Role $role): bool
    {
        // Don't allow deleting Super Admin role
        if ($role->name === 'Super Admin') {
            return false;
        }

        return $user->can('portal.role.manage');
    }
}
