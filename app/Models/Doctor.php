<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'address',
        'password',
        'latitude',
        'longitude',
        'isDoctor',
        'status',
        'charges',
        'qualification',
        'user_id',
        'profile_image'
    ];
}
