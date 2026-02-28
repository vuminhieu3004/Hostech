<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable, HasFactory, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guard_name = 'sanctum';

    protected $fillable = [
        'org_id',
        'role',
        'full_name',
        'phone',
        'email',
        'password_hash',
        'phone_verified_at',
        'email_verified_at',
        'failed_login_count',
        'locked_until',
        'last_login_at',
        'invite_code',
        'invite_expires_at',
        'invited_by_user_id',
        'mfa_enabled',
        'mfa_method',
        'mfa_secret_encrypted',
        'mfa_enrolled_at',
        'otp_required',
        'is_active',
        'deleted_at',
        'meta',
    ];

    protected $hidden = ['password_hash', 'mfa_secret_encrypted'];

    protected $casts = [
        // 'password_hash' => 'hashed',
        'is_active' => 'boolean',
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'mfa_enabled' => 'boolean',
        'mfa_enrolled_at' => 'datetime',
        'otp_required' => 'boolean',
        'meta' => 'array',
    ];

    //laravel auth dùng hàm này để lấy password
    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }
    public function org()
    {
        return $this->belongsTo(\App\Models\Org::class, 'org_id');
    }
    public function propertyUserRoles()
    {
        return $this->hasMany(\App\Models\PropertyUserRole::class, 'user_id');
    }
}
