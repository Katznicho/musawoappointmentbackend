<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabService;
use App\Models\LabRequest;
use Illuminate\Support\Facades\Validator;
use App\Traits\LogTrait;
use App\Models\Client;
use Illuminate\Support\Facades\DB ;
use Carbon\Carbon;
use App\Mail\sendingEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;


class LabController extends Controller
{
    use LogTrait;
    //

    public function index()
    {
        $services = LabService::all();
        return view('lab.labServices', compact('services'));
    }

    public function create(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'price' => 'required|string',
        ]);
   
        if($validator->fails()){
            return ($validator->errors());       
        }
   
        $service = LabService::create([
            'name' => $input['name'],
            'price' => $input['price'],
        ]);
        /**
         * Create a new user instance after a valid registration.
         *
         * @param  array  $data
         * @return \App\Models\LabService
         */
   
        // Log activity
        $this->createActivityLog('Created', 'New laboratory service added', 'Web', true);
        return redirect('labServices')->with('status', 'Laboratory Service Added Successfully');
    } 

    public function edit($id)
    {
        $service = LabService::find($id);
  
        if (is_null($service)) {
            return('Lab Service not found.');
        }
   
        return view('lab.editLabService', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = LabService::find($id);
        $service->name = $request->input('name');
        $service->price = $request->input('price');
        $service->update();
        // Log activity
        $this->createActivityLog('Update', 'Laboratory Service Updated', 'web', true);
        return redirect('labServices')->with('status', 'Laboratory Service Updated Successfully');
    }

        // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy($id)
    {
        $service = LabService::find($id);
        $service->delete();  
        // Log activity
        $this->createActivityLog('Deleted', 'Laboratory Service Has been Deleted', 'Web', true);
        return redirect('labServices')->with('status', 'Laboratory Service Deleted Successfully');
    }

    public function allServices()
    {
        $services = LabService::all();
        return response(['response' => 'success','data'=>$services]);
    }

    public function requestService(Request $request, $id)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'service_name' => 'required|string',
            'client_contact' => 'required|string',
            'client_address' => 'required|string',
        ]);
   
        if($validator->fails()){
            return ($validator->errors());       
        }

        $pending = DB::table('lab_requests')->where([['client_id', '=', $id], ['status', '=', 'pending']])->get();
        if ($pending->isEmpty()){
            $client = DB::table('clients')->where([['id', '=', $id]])->first();
            $req_price = DB::table('lab_services')->where('name', '=', $input['service_name'])->get();

            if($req_price->isEmpty()){
                return response(['message' => 'service not found']);
            }

            $price = $req_price[0]->price;

            $client_name = $client->fname;
       
            $labRequest = LabRequest::create([
                'client_id' => $id,
                'service_name' => $input['service_name'],
                'client_name' => $client_name,
                'client_contact' => $input['client_contact'],
                'client_address' => $input['client_address'],
                'status' => 'pending',
                'price'  => $price,
            ]);

            //lab request alert messages
            $data = [
                'otp'=>"Hello, there is a new Lab Request"
            ];
            Mail::send('email_template', $data, function($message) {
                $message->to('adfamedicare69@gmail.com')->subject('Musawo Adfa Lab Request');
           });
           $response = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message=NewLabRequest&reciever=0709184468');
           $res = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message=NewLabRequest&reciever=0772795991');
           $respo = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message=NewLabRequest&reciever=0785423523');
    
            return response(['status'=>$response->getStatusCode(), 'code'=>$res->getStatusCode(), 'number'=>$respo->getStatusCode(),'response' => 'success','data'=>$labRequest]);
        } else {
            $r_id = $pending[0]->id;
            $pendingService = DB::table('lab_requests')->where('id', '=',$r_id)->get();
            return response(['response' => 'success','data'=>['request'=>$pendingService[0]]]);
        }

    }

    public function displayLabRequests()
    {
        $labRequests = LabRequest::latest()->paginate(200);
        return view('lab.labRequest', compact('labRequests'));
    }

    public function cancelLabRequest($id) {
        $request = DB::table('lab_requests')->where( 'id', '=', $id)->first();

        if(is_null($request)){
            return response(['message' => 'Lab Request Not Found']);
        }
        $update_request = DB::table('lab_requests')->where( 'id', '=', $id)->delete();
        return response(['message' => 'Lab Request has been cancelled']);
        
    }

    public function rateLabRequest(Request $request, $id) {
        $req = DB::table('lab_requests')->where([
            ['id', '=', $id], ['status', '=', 'completed']])->first();
        if(is_null($req)){
            return response(['message' => 'Request Not Yet Completed']);
        }

        $request->validate([
            'client_review' => 'string',
            'rating' => 'string',
        ]);
        $update_request = DB::table('lab_requests')->where( 'id', '=', $id)->update([
            'client_review' => $request->client_review, 
            'rating' => $request->rating,
            'updated_at' => Carbon::now(),
        ]);
        $requestCompleted = DB::table('lab_requests')->where( 'id', '=', $id)->first();
        return response(['message' => 'Request confirmed by client Completed', 'data'=>['request'=>$requestCompleted]]);

    }

    public function editRequest($id)
    {
        $labRequest = LabRequest::find($id);
  
        if (is_null($labRequest)) {
            return('Lab Request not found.');
        }
   
        return view('lab.editLabRequest', compact('labRequest'));
    }

    public function updateRequest(Request $request, $id)
    {
        $labRequest = LabRequest::find($id);
        $labRequest->status = $request->input('status');
        $labRequest->update();
        // Log activity
        $this->createActivityLog('Update', 'Laboratory Request Updated', 'web', true);
        return redirect('labRequest')->with('status', 'Laboratory Request Updated Successfully');
    }

        // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroyRequest($id)
    {
        $labRequest = LabRequest::find($id);
        $labRequest->delete();  
        // Log activity
        $this->createActivityLog('Deleted', 'Laboratory Request Has been Deleted', 'Web', true);
        return redirect('labRequest')->with('status', 'Laboratory Request Deleted Successfully');
    }

    public function currentLabRequest($id){

        $client = DB::table('lab_requests')->where( 'client_id', '=', $id)->first();
        if(is_null($client)){
            return response(['message' => 'You do not have any requests yet']);
        }
        $requests = DB::table('lab_requests')->where([
            ['client_id', '=', $id], ['status', '!=', 'completed'], ['status', '!=', 'cancelled']])->orderBy("id", 'desc')->get();

        if ($requests->isEmpty()) {
            return response(['message' => 'No on going Requests']);
        }
        return response(['message' => 'Client Lab Request Returned successfully', 'data'=>['request'=>$requests[0]]]);
    }

}
