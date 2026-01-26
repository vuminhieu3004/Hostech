<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuditLog extends Model
{
    use HasUuids;

    protected $table = 'audit_logs';
    public $incrementing = false;
    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];
}
