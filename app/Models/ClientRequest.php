<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'doctor_id',
        'message',
        'status',
        'client_review',
        'rating',
    ];
}
