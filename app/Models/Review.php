<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'booking_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'comment',
        'photos',
        'status',
        'admin_response',
        'responded_at',
        'is_verified_purchase',
        'helpful_count',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_verified_purchase' => 'boolean',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeHighRated($query, int $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Helper methods
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
        $this->reviewable->updateRating();
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function respond(string $response): void
    {
        $this->update([
            'admin_response' => $response,
            'responded_at' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            if ($review->status === 'approved') {
                $review->reviewable->updateRating();
            }
        });

        static::updated(function ($review) {
            if ($review->wasChanged('status') && $review->status === 'approved') {
                $review->reviewable->updateRating();
            }
        });

        static::deleted(function ($review) {
            $review->reviewable->updateRating();
        });
    }
}
