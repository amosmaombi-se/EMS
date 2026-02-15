<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'event_id',
        'user_id',
        'venue_id',
        'booking_date',
        'event_date',
        'event_end_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'payment_status',
        'customer_notes',
        'internal_notes',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
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
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('payment_status', 'partially_paid');
    }

    public function scopeFullyPaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    // Accessors
    public function getPaymentProgressPercentageAttribute(): float
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->paid_amount / $this->total_amount) * 100;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_amount > 0 && 
               $this->event_date < now() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    // Helper methods
    public function generateBookingNumber(): string
    {
        $year = now()->year;
        $lastBooking = static::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastBooking ? intval(substr($lastBooking->booking_number, -5)) + 1 : 1;
        return 'BKG-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total_price');
        $taxAmount = $subtotal * 0.1; // 10% tax (adjust as needed)
        $total = $subtotal + $taxAmount - $this->discount_amount;
        $due = $total - $this->paid_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'due_amount' => max(0, $due),
        ]);

        $this->updatePaymentStatus();
    }

    public function updatePaymentStatus(): void
    {
        $status = match(true) {
            $this->paid_amount == 0 => 'unpaid',
            $this->paid_amount >= $this->total_amount => 'paid',
            default => 'partially_paid',
        };

        $this->update(['payment_status' => $status]);
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        $this->logStatusChange('pending', 'confirmed');
    }

    public function cancel(string $reason): void
    {
        $oldStatus = $this->status;

        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->logStatusChange($oldStatus, 'cancelled', $reason);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
        $this->logStatusChange($this->status, 'completed');
    }

    protected function logStatusChange(string $from, string $to, ?string $notes = null): void
    {
        $this->statusHistory()->create([
            'user_id' => auth()->id(),
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'changed_at' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = $booking->generateBookingNumber();
            }
            if (empty($booking->booking_date)) {
                $booking->booking_date = now();
            }
        });
    }
}
