<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientSummary extends Model
{
    use HasFactory;
    //added a table name
    protected $table = 'patients_summary';

    //fillable fields
    protected $fillable = [
        'patient_names',
        'doctor_names',
        'request_id',
        'lab_services',
        'description',
        'total_amount',
        'added_charge',
        'lab_charge',
        'doctor_charge',
        'payment_status',
        'mode_of_payment',
        'payment_reference',
        'narrative',


    ];
}
