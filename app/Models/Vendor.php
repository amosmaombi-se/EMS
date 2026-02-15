<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vendor_category_id',
        'business_name',
        'slug',
        'description',
        'logo',
        'cover_image',
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'business_registration_number',
        'tax_id',
        'minimum_order_value',
        'years_of_experience',
        'team_size',
        'service_areas',
        'specializations',
        'rating',
        'total_reviews',
        'total_bookings',
        'verification_status',
        'verified_at',
        'verification_notes',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'service_areas' => 'array',
        'specializations' => 'array',
        'minimum_order_value' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(VendorService::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(VendorAvailability::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(VendorPortfolio::class);
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
        return $query->where('verification_status', 'verified');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('vendor_category_id', $categoryId);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
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
}