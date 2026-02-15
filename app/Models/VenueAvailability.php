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
        'date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'price_override' => 'decimal:2',
    ];
}
