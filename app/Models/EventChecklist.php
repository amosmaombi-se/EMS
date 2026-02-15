<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'sort_order',
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class);
    }

    // Accessors
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total == 0) {
            return 0;
        }
        $completed = $this->tasks()->where('status', 'completed')->count();
        return ($completed / $total) * 100;
    }
}
