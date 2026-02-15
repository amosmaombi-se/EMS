<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_number',
        'user_id',
        'event_type_id',
        'event_package_id',
        'title',
        'description',
        'event_date',
        'event_end_date',
        'start_time',
        'end_time',
        'venue_name',
        'venue_address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'expected_guests',
        'confirmed_guests',
        'estimated_budget',
        'actual_cost',
        'status',
        'notes',
        'custom_fields',
        'cover_image',
        'is_public',
        'is_recurring',
        'recurrence_pattern',
        'published_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'custom_fields' => 'array',
        'is_public' => 'boolean',
        'is_recurring' => 'boolean',
        'estimated_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'published_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',

    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function eventPackage(): BelongsTo
    {
        return $this->belongsTo(EventPackage::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EventSchedule::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(EventChecklist::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(EventGuest::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Accessors
    public function getBudgetUsagePercentageAttribute(): float
    {
        if ($this->estimated_budget == 0) {
            return 0;
        }
        return ($this->actual_cost / $this->estimated_budget) * 100;
    }

    public function getRemainingBudgetAttribute(): float
    {
        return $this->estimated_budget - $this->actual_cost;
    }

    // Helper methods
    public function generateEventNumber(): string
    {
        $year = now()->year;
        $lastEvent = static::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastEvent ? intval(substr($lastEvent->event_number, -5)) + 1 : 1;
        return 'EVT-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_number)) {
                $event->event_number = $event->generateEventNumber();
            }
        });
    }
}

