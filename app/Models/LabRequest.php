<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'client_name',
        'client_address',
        'client_contact',
        'status',
        'client_id',
        'price',
        'client_review',
        'rating',
    ];

}
