<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'church_id',
        'user_id',
        'title',
        'slug',
        'content',
        'location',
        'type',
        'activity_date',
        'image_path',
        'views_count',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'is_published' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    // Relations
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('activity_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('activity_date', '<', now()->toDateString());
    }

    // Accessors & Mutators
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
