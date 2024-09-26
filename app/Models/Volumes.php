<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volumes extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'volume_id',
        'weight',
        'order_code',
        'integration_id',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
