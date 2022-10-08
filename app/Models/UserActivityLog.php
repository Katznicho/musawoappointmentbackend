<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name', 
        'ip_address',
         'action', 
         'method', 
         'path', 
         'description',
          'platform', 
          'status'
        ];
}
