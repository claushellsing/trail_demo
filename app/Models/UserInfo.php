<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $casts = [
        'date_of_birth' => 'datetime',
        'date_of_marriage' => 'datetime'
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'country',
        'city',
        'date_of_birth',
        'is_married',
        'date_of_marriage',
        'country_of_marriage',
        'is_widowed',
        'has_been_married'
    ];
}
