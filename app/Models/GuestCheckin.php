<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class GuestCheckin extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guest_checkins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guest_id',
        'event_id',
        'check_in_time',
        'check_out_time',
        'method',
        'check_in_code',
        'device_id',
        'location',
        'total_guests',
        'checked_in_guests',
        'welcome_pack_given',
        'welcome_pack_items',
        'badge_given',
        'badge_number',
        'notes',
        'checked_in_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'welcome_pack_given' => 'boolean',
        'badge_given' => 'boolean',
        'total_guests' => 'integer',
        'checked_in_guests' => 'array',
        'checked_in_by' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'check_in_time',
        'check_out_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Check-in methods constants.
     */
    public const METHOD_QR_CODE = 'qr_code';
    public const METHOD_MANUAL = 'manual';
    public const METHOD_FACIAL_RECOGNITION = 'facial_recognition';
    public const METHOD_RFID = 'rfid';
    public const METHOD_MOBILE_APP = 'mobile_app';
    public const METHOD_KIOSK = 'kiosk';

    /**
     * Get all available check-in methods.
     *
     * @return array
     */
    public static function getMethods(): array
    {
        return [
            self::METHOD_QR_CODE,
            self::METHOD_MANUAL,
            self::METHOD_FACIAL_RECOGNITION,
            self::METHOD_RFID,
            self::METHOD_MOBILE_APP,
            self::METHOD_KIOSK,
        ];
    }

    /**
     * Get the guest associated with the check-in.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(EventGuest::class, 'guest_id');
    }

    /**
     * Get the event associated with the check-in.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user who performed the check-in.
     */
    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Scope a query to only include active check-ins (checked in but not out).
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('check_in_time')
                    ->whereNull('check_out_time');
    }

    /**
     * Scope a query to only include completed check-ins.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('check_in_time')
                    ->whereNotNull('check_out_time');
    }

    /**
     * Scope a query to only include check-ins for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include check-ins for a specific guest.
     */
    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    /**
     * Scope a query to only include check-ins on a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('check_in_time', $date);
    }

    /**
     * Scope a query to only include check-ins within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in_time', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include VIP check-ins.
     */
    public function scopeVip($query)
    {
        return $query->whereHas('guest', function ($q) {
            $q->where('is_vip', true);
        });
    }

    /**
     * Check if the check-in is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return !is_null($this->check_in_time) && is_null($this->check_out_time);
    }

    /**
     * Check if the check-in is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return !is_null($this->check_in_time) && !is_null($this->check_out_time);
    }

    /**
     * Get the duration of the stay in minutes.
     *
     * @return int|null
     */
    public function getDurationInMinutes(): ?int
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        return $this->check_in_time->diffInMinutes($this->check_out_time);
    }

    /**
     * Get the duration of the stay formatted.
     *
     * @return string|null
     */
    public function getDurationFormatted(): ?string
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        $duration = $this->check_in_time->diff($this->check_out_time);
        
        $parts = [];
        if ($duration->h > 0) {
            $parts[] = $duration->h . ' hour' . ($duration->h > 1 ? 's' : '');
        }
        if ($duration->i > 0) {
            $parts[] = $duration->i . ' minute' . ($duration->i > 1 ? 's' : '');
        }
        
        return implode(' ', $parts) ?: 'Less than a minute';
    }

    /**
     * Get the check-in time in a formatted string.
     *
     * @param string $format
     * @return string|null
     */
    public function getFormattedCheckInTime(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->check_in_time ? $this->check_in_time->format($format) : null;
    }

    /**
     * Get the check-out time in a formatted string.
     *
     * @param string $format
     * @return string|null
     */
    public function getFormattedCheckOutTime(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->check_out_time ? $this->check_out_time->format($format) : null;
    }

    /**
     * Perform a check-out for the guest.
     *
     * @param array $data Additional check-out data
     * @return bool
     */
    public function checkOut(array $data = []): bool
    {
        if ($this->isCompleted()) {
            return false;
        }

        return $this->update(array_merge([
            'check_out_time' => now(),
        ], $data));
    }

    /**
     * Get the welcome pack items as an array.
     *
     * @return array
     */
    public function getWelcomePackItemsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }

    /**
     * Set the welcome pack items as a comma-separated string.
     *
     * @param array $value
     * @return void
     */
    public function setWelcomePackItemsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['welcome_pack_items'] = implode(',', $value);
        } else {
            $this->attributes['welcome_pack_items'] = $value;
        }
    }

    /**
     * Get the names of checked-in guests as an array.
     *
     * @param mixed $value
     * @return array
     */
    public function getCheckedInGuestsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value ?? '[]', true) ?? [];
    }

    /**
     * Set the names of checked-in guests.
     *
     * @param array $value
     * @return void
     */
    public function setCheckedInGuestsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['checked_in_guests'] = json_encode($value);
        } else {
            $this->attributes['checked_in_guests'] = $value;
        }
    }

    /**
     * Add a guest to the checked-in guests list.
     *
     * @param string $name
     * @return void
     */
    public function addCheckedInGuest(string $name): void
    {
        $guests = $this->checked_in_guests;
        $guests[] = $name;
        $this->checked_in_guests = $guests;
    }

    /**
     * Generate a check-in code.
     *
     * @return string
     */
    public static function generateCheckInCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));
    }

    /**
     * Get the check-in location with fallback.
     *
     * @return string
     */
    public function getLocationWithFallback(): string
    {
        return $this->location ?? 'Main Entrance';
    }

    /**
     * Get the check-in method in a readable format.
     *
     * @return string
     */
    public function getMethodReadable(): string
    {
        return match($this->method) {
            self::METHOD_QR_CODE => 'QR Code',
            self::METHOD_MANUAL => 'Manual',
            self::METHOD_FACIAL_RECOGNITION => 'Facial Recognition',
            self::METHOD_RFID => 'RFID',
            self::METHOD_MOBILE_APP => 'Mobile App',
            self::METHOD_KIOSK => 'Kiosk',
            default => ucfirst(str_replace('_', ' ', $this->method)),
        };
    }
}