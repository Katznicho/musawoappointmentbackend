<?php

namespace App\Http\Controllers;

use App\Models\PatientSummary;
use App\Traits\LogTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    use LogTrait;

    public function index()
    {
         //select from PatientSummary table
        $payments = PatientSummary::all();
        return view('payments.index', compact('payments'));
    }

    public function show(Request $request , $id){
        $payment = PatientSummary::find($id);
        return view('payments.show', compact('payment'));

    }

    public function edit(Request $request , $id){
        $payment = PatientSummary::find($id);
        return view('payments.edit', compact('payment'));

    }

    // update payment
     public function update(Request $request, $id)
    {
        $this->logInfo('PaymentController', 'update', 'Updating a payment');

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
