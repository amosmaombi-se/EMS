<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'user_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'notes',
        'terms',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'viewed']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->whereIn('status', ['sent', 'viewed'])
                  ->where('due_date', '<', now());
            });
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && 
               !in_array($this->status, ['paid', 'cancelled']);
    }

    public function getPaymentProgressPercentageAttribute(): float
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->paid_amount / $this->total_amount) * 100;
    }

    // Helper methods
    public function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastInvoice = static::whereYear('created_at', $year)->latest('id')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -5)) + 1 : 1;
        return 'INV-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total_price');
        $taxAmount = ($subtotal - $this->discount_amount) * ($this->tax_rate / 100);
        $total = $subtotal + $taxAmount - $this->discount_amount;
        $balance = $total - $this->paid_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'balance_due' => max(0, $balance),
        ]);

        $this->updateStatus();
    }

    public function updateStatus(): void
    {
        if ($this->balance_due <= 0) {
            $this->update(['status' => 'paid']);
        } elseif ($this->paid_amount > 0) {
            $this->update(['status' => 'partially_paid']);
        } elseif ($this->due_date < now()) {
            $this->update(['status' => 'overdue']);
        }
    }

    public function markAsSent(): void
    {
        $this->update(['status' => 'sent']);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $this->total_amount,
            'balance_due' => 0,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
            }
        });
    }
}