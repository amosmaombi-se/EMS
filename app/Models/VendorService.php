<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class VendorService extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'description',
        'base_price',
        'pricing_type',
        'min_quantity',
        'max_quantity',
        'duration_hours',
        'features',
        'images',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'features' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bookingItems(): MorphMany
    {
        return $this->morphMany(BookingItem::class, 'itemable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function calculatePrice(int $quantity): float
    {
        return match($this->pricing_type) {
            'per_person', 'per_hour', 'per_day' => $this->base_price * $quantity,
            default => $this->base_price,
        };
    }
}