<?php

namespace App\Models;

use App\PropertyScopedTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, HasUuids, SoftDeletes, PropertyScopedTrait;

    protected $fillable = [
        'org_id',
        'property_id',
        'code',
        'name',
        'type',
        'area',
        'floor',
        'capacity',
        'base_price',
        'status',
        'description',
        'amenities',
        'utilities',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'base_price' => 'decimal:2',
        'amenities' => 'array',
        'utilities' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function org()
    {
        return $this->belongsTo(Org::class);
    }
}
