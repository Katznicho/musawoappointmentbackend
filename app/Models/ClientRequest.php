<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;
    //add table name
    protected $table = 'requests';

    protected $fillable = [
        'client_id',
        'doctor_id',
        'message',
        'status',
        'client_review',
        'rating',
        'request_type',
        'client_status'
    ];
}
