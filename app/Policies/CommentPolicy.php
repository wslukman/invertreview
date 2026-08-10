<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_comment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Owner bisa hapus, atau church admin (own church), atau super admin
        return $user->id === $comment->user_id 
            || $user->hasRole('super_admin')
            || ($user->hasRole('church_admin') 
                && $user->church_id === $comment->activity->church_id);
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Comment $comment): bool
    {
        // Church admin atau super admin bisa approve
        return $user->hasRole('super_admin') 
            || ($user->hasRole('church_admin') 
                && $user->church_id === $comment->activity->church_id);
    }
}
