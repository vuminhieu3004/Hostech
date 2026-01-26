<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserSession extends Model
{
    use HasUuids;

    protected $table = 'user_sessions';
    public $incrementing = false;
    protected $keyType = 'string';

    const UPDATED_AT = null; // nếu bảng không có updated_at

    protected $guarded = [];

    protected $casts = [
        'is_trusted' => 'boolean',
        'otp_expires_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'otp_sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'created_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
