<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountData extends Model
{
    protected $table = 'account_data';

    protected $fillable = [
        'logo_image_url',
        'website',
        'business_area',
        'contact',
    ];

    protected $casts = [
        'business_area' => 'array',
    ];
}
