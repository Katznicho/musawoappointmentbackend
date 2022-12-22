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
use App\Traits\SendPushNotifications;
use Carbon\Carbon;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Js;
use Nette\Utils\Json;

class RequestController extends Controller
{
    use LogTrait, SendPushNotifications;

      public function showDetails($id){

        $patient_summary = PatientSummary::where('request_id', $id)->get();

        if($patient_summary->isEmpty()){
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

      public function updateSummary(Request $request , $id){
          //check if request exists in the database if not create a new one otherwise update the existing one
            $patient_summary = PatientSummary::where('request_id', $id)->get();
            if($patient_summary->isEmpty()){

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

                ]);
                return response(['response' => 'success','data'=>$patient_summary]);
            }
            else{
                $patient_summary = PatientSummary::where('request_id', $id)->first();
                 $patient_summary->request_id = $id;
                $patient_summary->patient_names = $request->patient_names;
                $patient_summary->doctor_names = $request->doctor_names;
                $patient_summary->lab_services = Json::encode($request->lab_services);
                $patient_summary->description = $request->description;
                $patient_summary->total_amount = $request->total_amount;
                $patient_summary->added_charge = $request->added_charge;
                $patient_summary->lab_charge = $request->lab_charge;
                $patient_summary->doctor_charge = $request->doctor_charge;

                $patient_summary->save();
                return response(['response' => 'success','data'=>$patient_summary]);
            }

      }

      public function getSummary($id){
        $patient_summary = PatientSummary::where('request_id', $id)->get();
        return response(['response' => 'success','data'=>$patient_summary]);
      }

    public function getDoctor($id) {
        $client = Client::find($id);

        if (is_null($client)) {
            // Log Activity
            $this->createActivityLog('Client', 'Client Not Found');
            return response(['message' => 'Client Not Found']);
        }

        $pending = FacadesDB::table('requests')->where([['client_id', '=', $id], ['status', '=', 'pending']])->get();
        if ($pending->isEmpty()){
            $lat1 = $client->latitude;
            $long1 = $client->longitude;
            $health_worker = $client->health_worker;
            $fname = $client->fname;
            $lname = $client->lname;
            $address = $client->address;

            $names = $fname . ' ' . $lname;

        //     if (is_null($health_worker)) {
        //     // Log Activity
        //         $this->createActivityLog('Client', 'Please Make a request');
        //         return response(['message' => 'Please Make a request']);
        //    }

            $role = FacadesDB::table('doctors')->where([

                ['role', '=', $health_worker],
                 ['status', '=', 'active']

                 ])
                 ->get();

            if ($role->isEmpty()) {
                $defaultDoctor = FacadesDB::table('doctors')->where( 'role', '=', 'Default')->get();
                $ddoctor_id = $defaultDoctor[0]->id;
                $user_id = $defaultDoctor[0]->user_id;
                $ddoctor_name = $defaultDoctor[0]->name;
                $ddoctor_email = $defaultDoctor[0]->email;
                $insertRequest = FacadesDB::insert("insert into requests (client_id, doctor_id, message) values ('$id', '$ddoctor_id', 'Doctor: $ddoctor_name, Client: $fname $lname Location: $address')");
                $data = [
                    'otp'=>"Hello you have a new Request"
                ];
            //     Mail::send('email_template', $data, function($message) use($ddoctor_email) {
            //         $message->to($ddoctor_email)->subject('Musawo Adfa, you have a request');
            //    });
            Mail::to($ddoctor_email)
            ->cc('adfamedicare69@gmail.com')
            ->send(new DoctorTemplate($ddoctor_name, $names));

            //get the user token
            $user = User::find($user_id);
            $message = "You have a new patient  request from  $names . Please check your app for more details";
            $token = $user->push_token;
            if($token){
                $this->sendPushNotification(
                    $token,
                    'New Patient Request',
                    $message,
                    ['data' => 'You have a new request']
                );

            }


                $getRequest = FacadesDB::table('requests')->where( 'client_id', '=',$id)->orderBy("id", 'desc')->get();
                return response(['response' => 'success','data'=>['doctor'=>$defaultDoctor[0], 'request'=>$getRequest[0]]]);
           }

            $doctor = Doctor::selectRaw("*,( 6371 * acos( cos( radians(?) ) *cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?)) + sin( radians(?) ) *sin( radians( latitude ) ) )) AS distance", [$lat1, $long1, $lat1])->where([['role', '=', $health_worker],['status', '=', 'active']])->orderBy("distance", 'asc')->get();
            $name = $doctor[0]->name;
            $doctor_id = $doctor[0]->id;
            $user_id = $doctor[0]->user_id;
            //get the user token
            $user = User::find($user_id);
            $message = "You have a new patient  request from  $names . Please check your app for more details";
            $token = $user->push_token;
            if($token){
                $this->sendPushNotification(
                    $token,
                    'New Patient Request',
                    $message,
                    ['data' => 'You have a new request']
                );

            }





            $request_data = FacadesDB::insert("insert into requests (client_id, doctor_id, message) values ('$id', '$doctor_id', 'Doctor: $name, Client: $fname $lname Location: $address')");
            //send email to the doctor
            $data = [
                'otp'=>"Hello you have a new Request"
            ];
            $email = $doctor[0]->email;
        //     Mail::send('email_template', $data, function($message) use($email) {
        //         $message->to($email)->subject('Musawo Adfa');
        //    });
        Mail::to($email)
        ->cc('adfamedicare69@gmail.com')
        ->send(new DoctorTemplate($name, $names));

           // update the doctor status
            $update_doctor = FacadesDB::table('doctors')->where( 'id', '=', $doctor_id)->update([
                'status' => 'inactive',
           ]);

           //get the request
            $request = FacadesDB::table('requests')->where( 'client_id', '=',$id)->orderBy("id", 'desc')->get();

            //log activity
            $this->createActivityLog('Client', 'Client Makes a request');
            return response(['response' => 'success','data'=>['doctor'=>$doctor[0], 'request'=>$request[0]]]);

        } else {
            $d_id = $pending[0]->doctor_id;
            $pendingDoctor = FacadesDB::table('doctors')->where('id', '=', $d_id)->get();
            return response(['response' => 'success','data'=>['doctor'=>$pendingDoctor[0], 'request'=>$pending[0]]]);
        }

    }


    public function acceptRequest($id) {
        $request = FacadesDB::table('requests')->where( 'id', '=', $id)->first();
        if(is_null($request)){
            $this->createActivityLog('Request', 'Request Not Found');
            return response(['message' => 'Request Not Found']);
        }

        $update_request = FacadesDB::table('requests')->where( 'id', '=', $id)->update([
            'status' => 'accepted',
        ]);
        //get the client id from the request
        $client_id = $request->client_id;
        //get the doctor id from the request
        $doctor_id = $request->doctor_id;

        //get the user_id from  the clients table
        $user_id = FacadesDB::table('clients')->where( 'id', '=', $client_id)->first()->user_id;
        //get the user token
        $user = User::find($user_id);
        $message = "Your request has been accepted. Please check your app for more details";

        $token = $user->push_token;
        if($token){
            $this->sendPushNotification(
                $token,
                'Request Accepted',
                $message,
                ['data' => 'Your request has been accepted']
            );

        }
        $requestAccepted = FacadesDB::table('requests')->where( 'id', '=', $id)->first();
        $this->createActivityLog('Request', 'Request accepted');
        return response(['message' => 'Request accepted', 'data'=>['request'=>$requestAccepted]]);

    }

    public function NotifyUsers($id,$message , $title, $data=[]){
        $user = User::find($id);
        $message = $message;
        //['data' => 'Your request has been accepted']

        $token = $user->push_token;
        if($token){
            $this->sendPushNotification(
                $token,
                $title,
                $message,

            );

        }
    }

    public function cancelRequest($id) {
        $request = FacadesDB::table('requests')->where( 'id', '=', $id)->first();

        if(is_null($request)){
            $this->createActivityLog('Request', 'Request not found');
            return response(['message' => 'Request Not Found']);
        }

        //get the client id from the request
        $client_id = $request->client_id;
        //get the doctor id from the request
        $doctor_id = $request->doctor_id;

        //get the user_id from  the clients table
        $user_id = FacadesDB::table('clients')->where( 'id', '=', $client_id)->first()->user_id;
        //get the user token
        $user = User::find($user_id);
        $message = "Your request has been cancelled. Please check your app for more details";

        $token = $user->push_token;
        if($token){
            $this->sendPushNotification(
                $token,
                'Request Accepted',
                $message,
                ['data' => 'Your request has been accepted']
            );

        }

        $update_request = FacadesDB::table('requests')->where( 'id', '=', $id)->update([
            'status' => 'cancelled',
        ]);
        $doctor_id = $request->doctor_id;
        $update_doctor = FacadesDB::table('doctors')->where( 'id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        return response(['message' => 'Request has been cancelled']);

    }

    public function cancelRequestClient($id) {
        $request = FacadesDB::table('requests')->where( 'id', '=', $id)->first();

        if(is_null($request)){
            $this->createActivityLog('Request', 'Request not found');
            return response(['message' => 'Request Not Found']);
        }
        $doctor_id = $request->doctor_id;
        $update_doctor = FacadesDB::table('doctors')->where( 'id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        $update_request = FacadesDB::table('requests')->where( 'id', '=', $id)->delete();
        $this->createActivityLog('Request', 'Request has been cancelled');
        return response(['message' => 'Request deleted']);

    }

    public function doctorRequests($id){

        $doctor = FacadesDB::table('requests')->where( 'doctor_id', '=', $id)->first();
        if(is_null($doctor)){
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'Doctor not Found']);
        }
        $requests = FacadesDB::table('requests')->where([['doctor_id', '=', $id], ['status', '!=', 'completed'], ['status', '!=', 'cancelled']])->orderBy("id", 'desc')->get();

        if($requests->isEmpty()){
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }

        $client = $requests[0]->client_id;
        $request = $requests[0];

        $request_client = FacadesDB::table('clients')->where( 'id', '=', $client)->get();
        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response(['message' => 'Doctor Requests Returned successfully', 'data'=>['request'=>$requests[0],'client'=>$request_client]]);


    }

    public function doctorHistory($id){

        $doctor = FacadesDB::table('requests')->where( 'doctor_id', '=', $id)->first();
        if(is_null($doctor)){
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['doctor_id', '=', $id]])->orderBy("id", 'desc')->get();

            foreach ($requests as $request) {
                $client[] = FacadesDB::table('clients')->where( 'id', '=', $request->client_id)->get();
                $clients = $client;

            }

        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response()->json(['message' => "Doctor's History Retrieved successfully", 'data'=>['request'=>[$requests],'client'=>$clients]]);
    }

    public function clientHistory($id){

        $client = FacadesDB::table('requests')->where( 'client_id', '=', $id)->first();
        if(is_null($client)){
            $this->createActivityLog('clientRequests', 'Client viewed his requests but had none');
            return response(['message' => 'You do not have any History yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['client_id', '=', $id]])->orderBy("id", 'desc')->get();

            foreach ($requests as $request) {
                $doctor[] = FacadesDB::table('doctors')->where( 'id', '=', $request->doctor_id)->get();
            }

        $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests');
        return response(['message' => "Doctor's History Retrieved successfully", 'data'=>['request'=>[$requests], 'doctor'=>$doctor]]);
    }

    public function completeRequest($id) {
        $request = FacadesDB::table('requests')->where( 'id', '=', $id)->first();
        if(is_null($request)){
            $this->createActivityLog('Request', 'Request Not Found');
            return response(['message' => 'Request Not Found']);
        }

        $update_request = FacadesDB::table('requests')->where( 'id', '=', $id)->update([
            'status' => 'completed',
        ]);
        $doctor_id = $request->doctor_id;
        $update_doctor = FacadesDB::table('doctors')->where( 'id', '=', $doctor_id)->update([
            'status' => 'active',
        ]);
        $requestAccepted = FacadesDB::table('requests')->where( 'id', '=', $id)->first();
        //send notification to client and doctor
        //get user id from the doctors table using the doctor_id
        $doctor_id = FacadesDB::table('doctors')->where( 'id', '=', $doctor_id)->first()->user_id;
        $this->NotifyUsers($doctor_id, 'Request Completed', 'Your completed the client request successfully');
        $client_id = FacadesDB::table('clients')->where( 'id', '=', $request->client_id)->first()->user_id;
        $this->NotifyUsers($client_id, 'Request Completed', 'Your request has been completed successfully');
        //send notification to client and doctor
        $this->createActivityLog('Request', 'Request Completed');
        return response(['message' => 'Request Completed', 'data'=>['request'=>$requestAccepted]]);

    }


    public function currentRequest($id){

        $client = FacadesDB::table('requests')->where( 'client_id', '=', $id)->first();
        if(is_null($client)){
            $this->createActivityLog('DoctorRequests', 'Doctor viewed his requests but had none');
            return response(['message' => 'You do not have any requests yet']);
        }
        $requests = FacadesDB::table('requests')->where([
            ['client_id', '=', $id], ['status', '!=', 'completed'], ['status', '!=', 'cancelled']])->orderBy("id", 'desc')->get();

        $doctor = $requests[0]->doctor_id;
        $request = $requests[0];

        $request_doctor = FacadesDB::table('doctors')->where( 'id', '=', $doctor)->get();

        $this->createActivityLog('ClientRequests', 'Client Viewed Current Request');
        return response(['message' => 'Client Request Returned successfully', 'data'=>['request'=>[$request],'doctor'=>$request_doctor]]);
    }


    public function completeClient(Request $request, $id) {
        $req = FacadesDB::table('requests')->where([
            ['id', '=', $id], ['status', '=', 'completed']])->first();
        if(is_null($req)){
            $this->createActivityLog('Request', 'Request Not yet Completed');
            return response(['message' => 'Request Not Yet Completed']);
        }

        $request->validate([
            'client_review' => 'string',
            'rating' => 'string',
        ]);
        $update_request = FacadesDB::table('requests')->where( 'id', '=', $id)->update([
            'client_review' => $request->client_review,
            'rating' => $request->rating,
            'updated_at' => Carbon::now(),
        ]);
        $requestCompleted = FacadesDB::table('requests')->where( 'id', '=', $id)->first();
        $this->createActivityLog('Request', 'Request confirmed by client Completed');

        return response(['message' => 'Request confirmed by client Completed', 'data'=>['request'=>$requestCompleted]]);

    }




    //Requests
   public function showRequests(){
    $requests = FacadesDB::table('requests')->get();

       return view('Requests',compact('requests'));
   }

   public function edit($id)
   {
    $request = FacadesDB::table('requests')->where('id', "=", $id)->first();

       if (is_null($request)) {
           return('Request not found.');
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
       $update_doctor = FacadesDB::table('requests')->where( 'id', '=', $id)->update([
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
