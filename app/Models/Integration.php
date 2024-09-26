<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    protected $fillable = ['name', 'integration_type_id'];

    protected string $schema = 'public';

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }
}
