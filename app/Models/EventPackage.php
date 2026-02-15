<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_type_id',
        'name',
        'slug',
        'description',
        'base_price',
        'min_guests',
        'max_guests',
        'included_services',
        'features',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'included_services' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

