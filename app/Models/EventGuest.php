<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGuest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'category',
        'guest_type',
        'rsvp_status',
        'plus_ones',
        'plus_one_allowed',
        'dietary_preference',
        'allergies',
        'special_requirements',
        'accessibility_needs',
        'accommodation_needs',
        'transportation_needs',
        'invitation_sent',
        'invitation_sent_at',
        'invitation_method',
        'invitation_link',
        'rsvp_responded_at',
        'rsvp_last_reminded_at',
        'is_vip',
        'language_preference',
        'notes',
        'welcome_pack_sent',
        'check_in_time',
        'check_out_time',
        'invitation_token',
    ];

    protected $casts = [
        'plus_one_allowed' => 'boolean',
        'invitation_sent' => 'boolean',
        'is_vip' => 'boolean',
        'welcome_pack_sent' => 'boolean',
        'invitation_sent_at' => 'datetime',
        'rsvp_responded_at' => 'datetime',
        'rsvp_last_reminded_at' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'total_guests_count',
        'has_special_needs',
        'invitation_status',
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getTotalGuestsCountAttribute(): int
    {
        return 1 + ($this->plus_ones ?? 0);
    }

    public function getHasSpecialNeedsAttribute(): bool
    {
        return !empty($this->dietary_preference) || 
               !empty($this->special_requirements) || 
               !empty($this->allergies) ||
               !empty($this->accessibility_needs) ||
               !empty($this->accommodation_needs);
    }

    public function getInvitationStatusAttribute(): string
    {
        if (!$this->invitation_sent) {
            return 'not_sent';
        }
        
        if ($this->rsvp_responded_at) {
            return 'responded';
        }
        
        return 'sent_pending_response';
    }

    // Scopes
    public function scopeAttending($query)
    {
        return $query->where('rsvp_status', 'attending');
    }

    public function scopeNotAttending($query)
    {
        return $query->where('rsvp_status', 'not_attending');
    }

    public function scopePending($query)
    {
        return $query->where('rsvp_status', 'pending');
    }

    public function scopeMaybe($query)
    {
        return $query->where('rsvp_status', 'maybe');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeWithPlusOnes($query)
    {
        return $query->where('plus_ones', '>', 0);
    }

    public function scopeWithDietaryPreferences($query)
    {
        return $query->whereNotNull('dietary_preference');
    }

    public function scopeNeedsInvitation($query)
    {
        return $query->where('invitation_sent', false)
                    ->whereNotNull('email');
    }

    public function scopeNeedsReminder($query)
    {
        return $query->where('invitation_sent', true)
                    ->whereNull('rsvp_responded_at')
                    ->where('rsvp_status', 'pending')
                    ->where(function($q) {
                        $q->whereNull('rsvp_last_reminded_at')
                          ->orWhere('rsvp_last_reminded_at', '<', now()->subDays(3));
                    });
    }

    // Helper methods
    public function markInvitationSent(string $method = 'email', string $link = null): void
    {
        $this->update([
            'invitation_sent' => true,
            'invitation_sent_at' => now(),
            'invitation_method' => $method,
            'invitation_link' => $link,
        ]);
    }

    public function markRsvpResponded(string $status, int $plusOnes = null): void
    {
        $this->update([
            'rsvp_status' => $status,
            'rsvp_responded_at' => now(),
            'plus_ones' => $plusOnes ?? $this->plus_ones,
        ]);
    }

    public function getCommunicationPreference(): string
    {
        if (!empty($this->email) && !empty($this->phone)) {
            return 'both';
        }
        
        if (!empty($this->email)) {
            return 'email';
        }
        
        if (!empty($this->phone)) {
            return 'sms';
        }
        
        return 'none';
    }

    public function getAllergiesArray(): array
    {
        return $this->allergies ? explode(',', $this->allergies) : [];
    }

    public function getSpecialNeedsSummary(): string
    {
        $needs = [];
        
        if ($this->dietary_preference) {
            $needs[] = "Dietary: {$this->dietary_preference}";
        }
        
        if ($this->allergies) {
            $needs[] = "Allergies: {$this->allergies}";
        }
        
        if ($this->accessibility_needs) {
            $needs[] = "Accessibility: {$this->accessibility_needs}";
        }
        
        if ($this->accommodation_needs) {
            $needs[] = "Accommodation: {$this->accommodation_needs}";
        }
        
        return implode(' | ', $needs);
    }

    // Generate invitation token
    public function generateInvitationToken(): string
    {
        return hash('sha256', $this->id . $this->event_id . $this->email . time());
    }
}