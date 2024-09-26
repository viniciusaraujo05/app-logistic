<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationType extends Model
{
    protected $fillable = ['name'];

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }
}