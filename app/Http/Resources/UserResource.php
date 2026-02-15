<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar 
                ? asset('storage/' . $this->avatar) 
                : asset('images/default-avatar.png'),
            
            // Personal Information
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->date_of_birth?->age,
            'gender' => $this->gender,
            
            // Address Information
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'full_address' => $this->getFullAddress(),
            
            // Preferences
            'timezone' => $this->timezone,
            'language' => $this->language ?? 'en',
            
            // Status
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            
            // Security
            'two_factor_enabled' => $this->two_factor_enabled,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'last_login_ip' => $this->when($request->user()?->id === $this->id, $this->last_login_ip),
            
            // Relationships
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'role_names' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name')->toArray();
            }),
            'permissions' => $this->whenLoaded('roles', function () {
                return $this->roles->flatMap->permissions->pluck('name')->unique()->values()->toArray();
            }),
            
            // Statistics
            'events_count' => $this->whenCounted('events'),
            'bookings_count' => $this->whenCounted('bookings'),
            'reviews_count' => $this->whenCounted('reviews'),
            'documents_count' => $this->whenCounted('documents'),
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }

    /**
     * Get the full formatted address
     */
    protected function getFullAddress(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
            ],
        ];
    }
}