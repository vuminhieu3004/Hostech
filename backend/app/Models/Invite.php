<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'org_id',
        'created_by_user_id',
        'target_role',
        'target_email',
        'target_phone',
        'code',
        'expires_at',
        'max_uses',
        'used_count',
        'status',
        'property_id',
        'room_id',
        'accepted_by_user_id',
        'accepted_at',
        'revoked_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'meta' => 'array',
    ];

    // Relationships
    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    public function isValid(): bool
    {
        return $this->status === 'PENDING'
            && !$this->isExpired()
            && $this->used_count < $this->max_uses;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid();
    }

    public function incrementUse(): void
    {
        $this->increment('used_count');

        // Auto-update status if max uses reached
        if ($this->used_count >= $this->max_uses) {
            $this->update(['status' => 'ACCEPTED']);
        }
    }

    public function revoke(): void
    {
        $this->update([
            'status' => 'REVOKED',
            'revoked_at' => now(),
        ]);
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'EXPIRED']);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('status', 'PENDING')
            ->where('expires_at', '>', now())
            ->whereColumn('used_count', '<', 'max_uses');
    }

    public function scopeForOrg($query, string $orgId)
    {
        return $query->where('org_id', $orgId);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
