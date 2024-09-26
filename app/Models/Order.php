<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Order extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'shipping_address',
        'email',
        'phone',
        'notes',
        'status',
        'weight',
        'price',
        'products',
        'responsible',
        'source_integration_id',
        'status_id',
        'shipping_method',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'products' => 'array',
    ];

    public function sourceIntegration(): BelongsTo
    {
        return $this->belongsTo(Integration::class, 'source_integration_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function trackings(): HasOne
    {
        return $this->hasOne(Tracking::class);
    }
}
