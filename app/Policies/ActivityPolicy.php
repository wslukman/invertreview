<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Determine whether the user can view any model.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view activities list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        return true; // Everyone can view published activities
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_activity') && $user->church_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        // Owner bisa edit, atau church admin, atau super admin
        return $user->id === $activity->user_id 
            || $user->hasRole('super_admin')
            || ($user->hasRole('church_admin') && $user->church_id === $activity->church_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        // Owner bisa hapus, atau church admin (own church), atau super admin
        return $user->id === $activity->user_id 
            || $user->hasRole('super_admin')
            || ($user->hasRole('church_admin') && $user->church_id === $activity->church_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Activity $activity): bool
    {
        return $user->hasRole('super_admin') 
            || ($user->hasRole('church_admin') && $user->church_id === $activity->church_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        return $user->hasRole('super_admin');
    }
}
