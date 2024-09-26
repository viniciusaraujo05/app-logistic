<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chart extends Model
{
    use HasFactory;

    protected $fillable = ['data', 'chart_type_id'];

    public function chartType(): BelongsTo
    {
        return $this->belongsTo(ChartType::class);
    }
}
