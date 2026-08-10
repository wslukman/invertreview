<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_program_id',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'status',
    ];

    // Relations
    public function program(): BelongsTo
    {
        return $this->belongsTo(SocialProgram::class, 'social_program_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getParticipantNameAttribute(): string
    {
        return $this->user_id ? $this->user->name : $this->guest_name;
    }

    public function getParticipantEmailAttribute(): string
    {
        return $this->user_id ? $this->user->email : $this->guest_email;
    }

    public function getParticipantPhoneAttribute(): string
    {
        return $this->user_id ? $this->user->phone : $this->guest_phone;
    }

    public function getIsRegisteredByAuthUserAttribute(): bool
    {
        return $this->user_id !== null;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'registered' => 'Terdaftar',
            'attended' => 'Hadir',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
