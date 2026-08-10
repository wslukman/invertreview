<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // Relations
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getAuthorNameAttribute(): string
    {
        return $this->user_id ? $this->user->name : $this->guest_name;
    }

    public function getAuthorEmailAttribute(): string
    {
        return $this->user_id ? $this->user->email : $this->guest_email;
    }

    public function getIsGuestAttribute(): bool
    {
        return $this->user_id === null;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
