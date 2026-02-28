<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'date',
        'status',
        'start_time',
        'end_time',
        'price_override',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'price_override' => 'decimal:2',
    ];

    public function getStartTimeAttribute($value): ?string
    {
        return $value ? substr($value, 0, 5) : null;
    }

    public function getEndTimeAttribute($value): ?string
    {
        return $value ? substr($value, 0, 5) : null;
    }
}