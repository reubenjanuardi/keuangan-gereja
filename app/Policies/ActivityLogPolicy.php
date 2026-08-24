<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('portal.log.view');
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->can('portal.log.view');
    }

    public function create(User $user): bool
    {
        return false; // Activity logs are system generated only
    }

    public function update(User $user, ActivityLog $activityLog): bool
    {
        return false; // Activity logs are immutable
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return false; // Activity logs cannot be deleted manually
    }
}
