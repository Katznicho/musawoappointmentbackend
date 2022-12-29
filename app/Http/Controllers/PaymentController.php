<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;
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

        $this->validate($request, [
            'payment_method' => 'required',
            'payment_status' => 'required',
        ]);
        PatientSummary::find($id)->update(
            [
                'mode_of_payment' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'narrative' => $request->description,
            ]

        );
        //log the activity

        $this->createActivityLog('update', 'Payment updated successfully', 'web', true);
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');

    }

    public function updatePayment(Request $request , $id){
        $this->validate($request, [
            'payment_method' => 'required',
            'payment_status' => 'required',
        ]);
        PatientSummary::find($id)->update(
            [
                'mode_of_payment' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'narrative' => $request->description,
            ]

        );
        //log the activity
        //update the client status to completed from pending
         $request_id  = PatientSummary::find($id)->request_id;
         ClientRequest::find($request_id)->update(
            [
                'client_status' => 'completed',
            ]
            );


        $this->createActivityLog('update', 'Payment updated successfully', 'web', true);
        //return
        return response(['response' => 'success','data'=>"payment updated"]);

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

    public function getClientPaymentDetails(Request $request , $id){
        $payments = PatientSummary::where('client_id', $id)->get();
        return response(['response' => 'success','data'=>$payments]);

    }
    public function getDoctorPaymentDetails(Request $request , $id){
        $payments = PatientSummary::where('doctor_id', $id)->get();
        return response(['response' => 'success','data'=>$payments]);

    }

}
