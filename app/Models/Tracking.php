<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 'tracking';

    protected $fillable = [
        'id',
        'tracking',
        'order_code',
        'integration_id',
        'order_id',
        'volumes_total',
        'receiver_name',
        'tracking',
        'additional',
        'carrier_name',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function transits(): HasOne
    {
        return $this->hasOne(TrackingTransit::class, 'tracking_id');
    }
}
