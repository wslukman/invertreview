<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'church_id',
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'capacity',
        'registered_count',
        'status',
        'image_path',
        'contact_person',
        'contact_phone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'capacity' => 'integer',
        'registered_count' => 'integer',
    ];

    // Relations
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '>', now()->toDateString());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<', now()->toDateString());
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getIsFullAttribute(): bool
    {
        return $this->registered_count >= $this->capacity;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->capacity - $this->registered_count);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'draft' => 'Draft',
            'active' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return [
            'pelatihan_kerja' => 'Pelatihan Kerja',
            'pembagian_sembako' => 'Pembagian Sembako',
        ][$this->type] ?? $this->type;
    }
}
