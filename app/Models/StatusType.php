<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusType extends Model
{
    protected $fillable = ['name'];

    public function orderStatuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class);
    }
}
