<?php

namespace App\Policies;

use App\Models\ProgramRegistration;
use App\Models\User;

class ProgramRegistrationPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProgramRegistration $registration): bool
    {
        // Owner bisa cancel, atau church admin (own church), atau super admin
        return $user->id === $registration->user_id 
            || $user->hasRole('super_admin')
            || ($user->hasRole('church_admin') 
                && $user->church_id === $registration->program->church_id);
    }
}
