<?php

namespace App\Http\Controllers\Api;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use App\Mail\DoctorTemplate;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\ClientRequest;
use App\Models\User;
use App\Models\Client;
use App\Traits\LogTrait;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendingEmail;
use App\Models\PatientSummary;
use App\Traits\HelperTrait;
use App\Traits\SendPushNotifications;
use Carbon\Carbon;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Js;
use Nette\Utils\Json;
use PhpParser\Node\Stmt\TryCatch;

class RequestController extends Controller
{

    use LogTrait, SendPushNotifications, HelperTrait;


    public function getRequestStatus(Request $request , $id){
        $request = ClientRequest::where('id', $id)->first();
        $status = $request->status;
        $client_status = $request->client_status;

        return response(['response' => 'success', 'data' =>['status'=>$status , 'client'=>$client_status] ], 200);
    }

    public function showDetails($id)
    {

        $patient_summary = PatientSummary::where('request_id', $id)->get();

        if ($patient_summary->isEmpty()) {
            //set lab services to empty array
            $lab_services = [];
            return View('requests_show', compact('patient_summary', 'lab_services'));
        }
        $data =    Json::decode($patient_summary[0]->lab_services);
        //split the lab services into an array
        $lab_services = explode(',', $data);
        //return view
        return View('requests_show', compact('patient_summary', 'lab_services'));
    }

    public function updateSummary(Request $request, $id)
    {
        //check if request exists in the database if not create a new one otherwise update the existing one
        $patient_summary = PatientSummary::where('request_id', $id)->get();
        //get the client request
        $client_request = ClientRequest::where('id', $id)->first();
        if ($patient_summary->isEmpty()) {

            $patient_summary = PatientSummary::create([
                'request_id' => $id,
                'patient_names' => $request->patient_names,
                'doctor_names' => $request->doctor_names,
                'lab_services' => Json::encode($request->lab_services),
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'added_charge' => $request->added_charge,
                'lab_charge' => $request->lab_charge,
                'doctor_charge' => $request->doctor_charge,
                'payment_status' => 'pending',
                'payment_reference' => $this->generatePaymentReference(),
                'client_id' => $client_request->client_id,
                'doctor_id' => $client_request->doctor_id,
                'request_type' => $client_request->request_type,


            ]);
            return response(['response' => 'success', 'data' => $patient_summary]);
        } else {
            $patient_summary = PatientSummary::where('request_id', $id)->first();
            $patient_summary->request_id = $id;
            $patient_summary->patient_names = $request->patient_names;
            $patient_summary->doctor_names = $request->doctor_names;
            $patient_summary->lab_services = Json::encode($request->lab_services);
            $patient_summary->description = $request->description;
            $patient_summary->total_amount = $request->total_amount;
            $patient_summary->added_charge = $request->added_charge;
            $patient_summary->lab_charge = $request->lab_charge;
            $patient_summary->mode_of_payment = $request->mode_of_payment;




            $patient_summary->save();
            return response(['response' => 'success', 'data' => $patient_summary]);
        }
    }

    public function getSummary($id)
    {
        $patient_summary = PatientSummary::where('request_id', $id)->get();
        return response(['response' => 'success', 'data' => $patient_summary]);
    }

    public function getDoctor(Request $request , $id)
    {
         try {

        $client = Client::find($id);

        if (is_null($client)) {
            // Log Activity
            $this->createActivityLog('Client', 'Client Not Found');
            return response(['message' => 'failure', 'data'=>'Client Not Found'],404);
        }
        //add validation to check for required fields

        else{
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
                'health_worker' => 'required',

            ]);
            if ($validator->fails()) {
                // Log Activity
                $this->createActivityLog('Client', 'Validation Error');
                return response(['message' => 'failure', 'data'=>$validator->errors()],404);
            }


            //check if the client has a pending request
            $client_request = ClientRequest::where('client_id', $id)->where('status', 'pending')->get();

            if ($client_request->isEmpty()) {
                // Log Activity
                // $this->createActivityLog('Client', 'No Pending Request');
                // return response(['message' => 'failure', 'data'=>'No Pending Request'],404);
                $lat1 = $request->latitude;
                $long1 = $request->longitude;
                $health_worker = $request->health_worker;
                $fname = $client->fname;
                $lname = $client->lname;
                $address = $client->address;
                $names = $fname . ' ' . $lname;
                //check if the doctor of a given role is available and status is active
                $doctor = Doctor::where('role', $health_worker)->where('status', 'active')->whereNotNull('latitude')->whereNotNull('longitude')->get();

                //if doctor is not available
                if ($doctor->isEmpty()) {
                    // Log Activity
                    $this->createActivityLog('Doctor', 'No Doctor Available Now Getting a default doctor');
                    //get a default doctor
                    $doctor = Doctor::where('role', 'default')->get();
                    //get doctor email
                    $email = $doctor[0]->email;
                    $name = $doctor[0]->name;
                    $user_id = $doctor[0]->user_id;
                    //find the user from the users table
                    $user = User::find($user_id);
                    //get the push notification token
                    $token = $user->push_token;
                    //send push notification to the doctor
                    $this->sendPushNotification($token, 'Pending Request', 'You have a pending request from ' . $names);
                    //send an email to the doctor
                    Mail::to($email)
                          ->cc('adfamedicare69@gmail.com')
                        ->send(new DoctorTemplate($name, $names));
                    //create a new request
                    $request = ClientRequest::create([
                        'client_id' => $client->id,
                        'doctor_id' => $doctor[0]->id,
                        'status' => 'pending',
                        'message'=> 'No Doctor Available Now Getting a default doctor',
                        'request_type' => $health_worker,

                    ]);
                    //return $request;
                    // Log Activity
                    $this->createActivityLog('Client Request', 'Request Created');
                    //return response(['message' => 'success', 'data' => $request]);
                    return response(['response' => 'success', 'data' => ['doctor' => $doctor[0], 'request' => $request]]);
                }
                else{
                    $lat1 = $request->latitude;
                    $long1 = $request->longitude;
                    $health_worker = $request->health_worker;
                    $fname = $client->fname;
                    $lname = $client->lname;
                    $address = $client->address;
                    $names = $fname . ' ' . $lname;
                    //select from the available doctors the doctor who is nearest to the client based on the latitude and longitude ignoring null longitude and latitude
                    $doctor = Doctor::where('role', $health_worker)->where('status', 'active')->whereNotNull('latitude')->whereNotNull('longitude')->orderByRaw("SQRT(POW(69.1 * (latitude - ?), 2) + POW(69.1 * (? - longitude) * COS(latitude / 57.3), 2))", [$lat1, $long1])->first();
                    //$doctor = Doctor::where('role', $health_worker)->where('status', 'active')->orderByRaw("SQRT(POW(69.1 * (latitude - ?), 2) + POW(69.1 * (? - longitude) * COS(latitude / 57.3), 2))", [$lat1, $long1])->first();
                    //get doctor email
                    $email = $doctor->email;
                    $name = $doctor->name;
                    $user_id = $doctor->user_id;
                    //find the user from the users table
                    $user = User::find($user_id);
                    //get the push notification token
                    $token = $user->push_token;
                    //send push notification to the doctor
                    $this->sendPushNotification($token, 'Pending Request', 'You have a pending request from ' . $names);
                    //send an email to the doctor
                    Mail::to($email)
                          ->cc('adfamedicare69@gmail.com')
                        ->send(new DoctorTemplate($name, $names));
                    //make doctor unavailable
                    $doctor->status = 'inactive';
                    $doctor->save();
                    //create a new request
                    $request = ClientRequest::create([
                        'client_id' => $client->id,
                        'doctor_id' => $doctor->id,
                        'status' => 'pending',
                        'request_type' => $health_worker,
                         'message'=> "$health_worker : Doctor Available"

                    ]);
                    // Log Activity
                    $this->createActivityLog('Client Request', 'Request Created');
                    return response(['response' => 'success', 'data' => ['doctor' => $doctor, 'request' => $request]]);

                }

            }
            else{
                $request = ClientRequest::where('client_id', $id)->where('status', 'pending')->first();
                $doctor = Doctor::find($request->doctor_id);
                //get doctor email
                $email = $doctor->email;
                $name = $doctor->name;
                $client_names = $client->fname . ' ' . $client->lname;
                $user_id = $doctor->user_id;
                //find the user from the users table
                $user = User::find($user_id);
                //get the push notification token
                $token = $user->push_token;
                //send push notification to the doctor
                $this->sendPushNotification($token, 'Pending Request', 'You have a pending request from ' . $client->fname . ' ' . $client->lname);
                //send an email to the doctor
                Mail::to($email)
                ->cc('adfamedicare69@gmail.com')
                ->send(new DoctorTemplate($name, $client_names));


                return response(['response' => 'success', 'data' => ['doctor' => $doctor, 'request' => $request]]);
            }

        }
         } catch (\Throwable $th) {
            return response(['response' => 'error', 'details' => $th->getTrace()]);
         }

    }


    public function acceptRequest($id)
    {
        $request = FacadesDB::table('requests')->where('id', '=', $id)->first();
        if (is_null($request)) {
            $this->createActivityLog('Request', 'Request Not Found');
            return response(['message' => 'Request Not Found']);
        }

        $update_request = FacadesDB::table('requests')->where('id', '=', $id)->update([
            'status' => 'accepted',
        ]);
        //get the client id from the request
        $client_id = $request->client_id;
        //get the doctor id from the request
        $doctor_id = $request->doctor_id;

        //get the user_id from  the clients table
        $user_id = FacadesDB::table('clients')->where('id', '=', $client_id)->first()->user_id;
        //get the user token
        $user = User::find($user_id);
        $message = "Your request has been accepted. Please check your app for more details";

        $token = $user->push_token;
        if ($token) {
            $this->sendPushNotification(
                $token,
                'Request Accepted',
                $message,
                ['data' => 'Your request has been accepted']
            );
        }
        $requestAccepted = FacadesDB::table('requests')->where('id', '=', $id)->first();
        $this->createActivityLog('Request', 'Request accepted');
        return response(['message' => 'Request accepted', 'data' => ['request' => $requestAccepted]]);
    }

    public function NotifyUsers($id, $message, $title, $data = [])
    {
        $user = User::find($id);
        $message = $message;
        //['data' => 'Your request has been accepted']

        $token = $user->push_token;
        if ($token) {
            $this->sendPushNotification(
                $token,
                $title,
                $message,

            );
        }
    }

    public function cancelRequest($id)
    {
        $request = FacadesDB::table('requests')->where('id', '=', $id)->first();

        if (is_null($request)) {
            $this->createActivityLog('Request', 'Request not found');
            return response(['message' => 'Request Not Found']);
        }

        //get the client id from the request
        $client_id = $request->client_id;
        //get the doctor id from the request
        $doctor_id = $request->doctor_id;

        //get the user_id from  the clients table
        $user_id = FacadesDB::table('clients')->where('id', '=', $client_id)->first()->user_id;
        //get the user token
        $user = User::find($user_id);
        $message = "Your request has been cancelled. Please check your app for more details";

        $token = $user->push_token;
        if ($token) {
            $this->sendPushNotification(
                $token,
                'Request Cancelled',
                $message,
                ['data' => 'Your request has been cancelled']
            );
        }

        $update_request = FacadesDB::table('requests')->where('id', '=', $id)->update([
            'status' => 'cancelled',
            'client_status' => 'cancelled',
        ]);
        $doctor_id = $request->doctor_id;
        $update_doctor = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        return response(['message' => 'Request has been cancelled']);
    }

    public function cancelRequestClient($id)
    {
        $request = FacadesDB::table('requests')->where('id', '=', $id)->first();

        if (is_null($request)) {
            $this->createActivityLog('Request', 'Request not found');
            return response(['message' => 'Request Not Found']);
        }
        $doctor_id = $request->doctor_id;
        $client_id = $request->client_id;
        $update_doctor = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        $update_request = FacadesDB::table('requests')->where('id', '=', $id)->update(
            [
                'status' => 'cancelled',
                'client_status'=>'cancelled'
            ]
        );
        //get the user_id from  the clients table
        $user_id = FacadesDB::table('clients')->where('id', '=', $client_id)->first()->user_id;
        //get the user token
        $user = User::find($user_id);
        $message = "Your cancelled the request. Please check your app for more details";
        //get the user token
        $token = $user->push_token;
        if ($token) {
            $this->sendPushNotification(
                $token,
                'Request Cancelled',
                $message,
                ['data' => 'Your request has been cancelled']
            );
        }
        //get the user id from the doctor table
        $doctor_user_id = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->first()->user_id;
        //get the user token
        $doctor_user = User::find($doctor_user_id);
        $doctor_message = "The client cancelled the request. Please check your app for more details";
        //get the user token
        $doctor_token = $doctor_user->push_token;
        if ($doctor_token) {
            $this->sendPushNotification(
                $doctor_token,
                'Request Cancelled',
                $doctor_message,
                ['data' => 'Your request has been cancelled']
            );
        }


        $this->createActivityLog('Request', 'Request has been cancelled');
        return response(['message' => 'Request deleted']);
    }

    public function doctorRequests($id)
    {

        $doctor = FacadesDB::table('requests')->where('doctor_id', '=', $id)->first();
        if (is_null($doctor)) {
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'Doctor not Found']);
        }
        $requests = FacadesDB::table('requests')->where([['doctor_id', '=', $id], ['status', '!=', 'completed'], ['status', '!=', 'cancelled']])->orderBy("id", 'desc')->get();

        if ($requests->isEmpty()) {
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }

        $client = $requests[0]->client_id;
        $request = $requests[0];

        $request_client = FacadesDB::table('clients')->where('id', '=', $client)->get();
        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response(['message' => 'Doctor Requests Returned successfully', 'data' => ['request' => $requests[0], 'client' => $request_client]]);
    }

    public function doctorHistory($id)
    {

        $doctor = FacadesDB::table('requests')->where('doctor_id', '=', $id)->first();
        if (is_null($doctor)) {
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['doctor_id', '=', $id]
        ])->orderBy("id", 'desc')->get();

        foreach ($requests as $request) {
            $client[] = FacadesDB::table('clients')->where('id', '=', $request->client_id)->get();
            $clients = $client;
        }

        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response()->json(['message' => "Doctor's History Retrieved successfully", 'data' => ['request' => [$requests], 'client' => $clients]]);
    }

    public function clientHistory($id)
    {

        $client = FacadesDB::table('requests')->where('client_id', '=', $id)->first();
        if (is_null($client)) {
            $this->createActivityLog('clientRequests', 'Client viewed his requests but had none');
            return response(['message' => 'You do not have any History yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['client_id', '=', $id]
        ])->orderBy("id", 'desc')->get();

        foreach ($requests as $request) {
            $doctor[] = FacadesDB::table('doctors')->where('id', '=', $request->doctor_id)->get();
        }

        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response(['message' => "Doctor's History Retrieved successfully", 'data' => ['request' => [$requests], 'doctor' => $doctor]]);
    }

    public function completeRequest($id)
    {
        $request = FacadesDB::table('requests')->where('id', '=', $id)->first();
        if (is_null($request)) {
            $this->createActivityLog('Request', 'Request Not Found');
            return response(['message' => 'Request Not Found']);
        }

        $update_request = FacadesDB::table('requests')->where('id', '=', $id)->update([
            'status' => 'completed',
        ]);
        $doctor_id = $request->doctor_id;
        $update_doctor = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        $requestAccepted = FacadesDB::table('requests')->where('id', '=', $id)->first();
        //send notification to client and doctor
        //get user id from the doctors table using the doctor_id
        $doctor_id = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->first()->user_id;
        $this->NotifyUsers($doctor_id, 'Request Completed', 'Your completed the client request successfully');
        $client_id = FacadesDB::table('clients')->where('id', '=', $request->client_id)->first()->user_id;
        $this->NotifyUsers($client_id, 'Request Completed', 'Your request has been completed successfully');
        //send notification to client and doctor
        $this->createActivityLog('Request', 'Request Completed');
        return response(['message' => 'Request Completed', 'data' => ['request' => $requestAccepted]]);
    }


    public function currentRequest($id)
    {

        $client = FacadesDB::table('requests')->where('client_id', '=', $id)->first();
        if (is_null($client)) {
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['client_id', '=', $id], ['status', '!=', 'completed'], ['status', '!=', 'cancelled']
        ])->orderBy("id", 'desc')->get();

        $doctor = $requests[0]->doctor_id;
        $request = $requests[0];

        $request_doctor = FacadesDB::table('doctors')->where('id', '=', $doctor)->get();

        $this->createActivityLog('ClientRequests', 'Client Viewed Current Request');
        return response(['message' => 'Client Request Returned successfully', 'data' => ['request' => [$request], 'doctor' => $request_doctor]]);
    }


    public function completeClient(Request $request, $id)
    {
        $req = FacadesDB::table('requests')->where([
            ['id', '=', $id], ['status', '=', 'completed']
        ])->first();
        if (is_null($req)) {
            $this->createActivityLog('Request', 'Request Not yet Completed');
            return response(['message' => 'Request Not Yet Completed']);
        }

        $request->validate([
            'client_review' => 'string',
            'rating' => 'string',
        ]);
        $update_request = FacadesDB::table('requests')->where('id', '=', $id)->update([
            'client_review' => $request->client_review,
            'rating' => $request->rating,
            'updated_at' => Carbon::now(),
        ]);
        $requestCompleted = FacadesDB::table('requests')->where('id', '=', $id)->first();
        $this->createActivityLog('Request', 'Request confirmed by client Completed');

        return response(['message' => 'Request confirmed by client Completed', 'data' => ['request' => $requestCompleted]]);
    }




    //Requests
    public function showRequests()
    {
        $requests = FacadesDB::table('requests')->get();
        //get all pending requests
        $pending_requests = ClientRequest::where("status", 'pending')->get();
        $pending_requests_total =  ClientRequest::where("status", 'pending')->count();

        return view('Requests', compact('requests', 'pending_requests', 'pending_requests_total'));
    }

    public function edit($id)
    {
        $request = FacadesDB::table('requests')->where('id', "=", $id)->first();

        if (is_null($request)) {
            return ('Request not found.');
        }

        return view('EditRequest', compact('request'));
    }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function update(Request $request, $id)
    {
        $requests = FacadesDB::table('requests')->where('id', "=", $id)->first();
        $update_doctor = FacadesDB::table('requests')->where('id', '=', $id)->update([
            'status' => $request->input('status'),
        ]);
        // Log activity
        $this->createActivityLog('Update', 'Request Updated', 'Web', true);
        return redirect('Requests')->with('status', 'Request Updated Successfully');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy($id)
    {
        $request = FacadesDB::table('requests')->where('id', "=", $id)->delete();

        // Log activity
        $this->createActivityLog('Delete', 'Request Has been Deleted', 'Web', true);
        return redirect('Requests')->with('status', 'Request Deleted Successfully');
    }
}

//0700216664
