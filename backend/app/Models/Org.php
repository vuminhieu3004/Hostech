<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Org extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'orgs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'org_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'org_id');
    }
}
