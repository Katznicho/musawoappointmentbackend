<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DoctorTemplate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $doctor_name;
    public $patient_name;
    public function __construct(

        $doctor_name,
        $patient_name
    )
    {

        $this->doctor_name = $doctor_name;
        $this->patient_name = $patient_name;
    }



    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'emails.doctor',


            [
                'doctor_name' => $this->doctor_name,
                'patient_name' => $this->patient_name,
            ],

    )->subject('New Patient');
    
    }
}
