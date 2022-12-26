<?php

namespace App\Http\Controllers;

use App\Traits\LogTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    use LogTrait;

    public function index()
    {
        return view('payments');
    }

    //add a new payment
    public function addPayment(Request $request, $id)
    {
        $this->logInfo('PaymentController', 'addPayment', 'Adding a new payment');

        $this->validate($request, [
            'patient_names' => 'required',
            'doctor_names' => 'required',
            'request_id' => 'required',
            'lab_services' => 'required',
            'description' => 'required',
            'total_amount' => 'required',
            'added_charge' => 'required',
            'lab_charge' => 'required',
            'doctor_charge' => 'required',
            'payment_status' => 'required',
            'mode_of_payment' => 'required',
            'payment_reference' => 'required',
            'narrative' => 'required',
        ]);

    }
}
