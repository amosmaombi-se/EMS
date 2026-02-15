<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'type',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'contact_person',
        'email',
        'phone',
        'capacity_min',
        'capacity_max',
        'area_sqft',
        'base_price_per_day',
        'base_price_per_hour',
        'security_deposit',
        'amenities',
        'images',
        'virtual_tour_url',
        'terms_and_conditions',
        'cancellation_policy',
        'rating',
        'total_reviews',
        'total_bookings',
        'is_verified',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'area_sqft' => 'decimal:2',
        'base_price_per_day' => 'decimal:2',
        'base_price_per_hour' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'amenities' => 'array',
        'images' => 'array',
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(VenueAvailability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function bookingItems(): MorphMany
    {
        return $this->morphMany(BookingItem::class, 'itemable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByCapacity($query, int $guests)
    {
        return $query->where('capacity_min', '<=', $guests)
            ->where('capacity_max', '>=', $guests);
    }

    public function scopeTopRated($query, float $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Helper methods
    public function updateRating(): void
    {
        $avgRating = $this->reviews()->avg('rating');
        $totalReviews = $this->reviews()->count();

        $this->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }

    public function isAvailableOn(string $date): bool
    {
        return $this->availability()
            ->where('date', $date)
            ->where('status', 'available')
            ->exists();
    }

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }
}
