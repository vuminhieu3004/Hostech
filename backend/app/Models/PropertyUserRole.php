<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PropertyUserRole extends Model
{
    use HasUuids;

    protected $table = 'property_user_roles';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'property_id',
        'user_id',
        'role',
        'is_active',
        'permissions',
        'created_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
        'created_at' => 'datetime',
    ];
}
