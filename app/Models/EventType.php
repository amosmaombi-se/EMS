<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'default_checklist',
        'typical_duration_hours',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'default_checklist' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(EventPackage::class);
    }

    public function vendorPortfolios(): HasMany
    {
        return $this->hasMany(VendorPortfolio::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
