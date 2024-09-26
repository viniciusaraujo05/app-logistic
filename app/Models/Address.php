<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'contact_name',
        'phone_number',
        'email',
        'street',
        'place',
        'postal_code',
        'city',
    ];
}
