<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Org extends Model
{
    use HasUuids;

    protected $table = 'orgs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];
}
