<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'lname',
        'fname',
        'phone',
        'username',
        'address',
        'dob',
        'latitude',
        'longitude',
        'health_worker',
        'isDoctor',
        'user_id',
        
    ];
}
