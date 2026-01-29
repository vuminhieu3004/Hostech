<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'properties';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'org_id',
        'code',
        'name',
        'address',
        'note',
        'default_billing_cycle',
        'default_due_day',
        'default_cutoff_day',
        'bank_accounts',
    ];

    protected $casts = [
        'bank_accounts' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id');
    }

    public function propertyUserRoles()
    {
        return $this->hasMany(PropertyUserRole::class, 'property_id');
    }
}
