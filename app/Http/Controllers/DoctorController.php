<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use App\Traits\LogTrait;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class DoctorController extends Controller
{


    use LogTrait;

    public function formatMobileInternational($mobile)
    {
        $length = strlen($mobile);
        $m = '+256';
        //format 1: +256752665888
        if ($length == 13)
        //return mobile with out the + sign
            return  substr($mobile, 1);
        elseif ($length == 12) //format 2: 256752665888
            return $mobile;
        elseif ($length == 10) //format 3: 0752665888
            return $m .= substr($mobile, 1);
        elseif ($length == 9) //format 4: 752665888
            return $m .= $mobile;

        return $mobile;
    }

    public function getDoctorDetails($id)
    {

        $doctor = Doctor::find($id);
        return response(['response' => 'success','data'=>$doctor]);
    }

    public function index()
    {
        $doctors = Doctor::all();
        //get all pending requests
        $pending_requests = ClientRequest::where("status", 'pending')->get();
        $pending_requests_total =  ClientRequest::where("status", 'pending')->count();

        return view('healthworkers', compact('doctors', 'pending_requests', 'pending_requests_total'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     *
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'role' => 'required',
            'charges' => 'required',
            'qualification' =>'required',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        // Store all ID images under one folder
        $destination_path = 'public/dps';
        //rename the image file to users name and current time
        $old_name = $request->profile_image->getClientOriginalName();
        $new_name = $input['name'] . '_' . time() . '.' . $request->profile_image->getClientOriginalExtension();
        $request->profile_image->storeAs($destination_path, $new_name);

        // if($validator->fails()){
        //     return ($validator->errors());
        // }

        $username = $this->formatMobileInternational($input['phone']);
        $user =  User::create([
            'name' => $input['name'],
            'role' => $input['role'],
            'email' => $input['email'],
            'isDoctor' => true,
            'username' => $username,
            'password' => Hash::make($input['email']),
        ]);

        $doctor_id = FacadesDB::table('users')->where( 'username', '=', $username)->first();

        $doctor = Doctor::create([
            'name' => $input['name'],
            'phone' => $this->formatMobileInternational($input['phone']),
            'address' => request()->ip(),
            'role' => $input['role'],
            'email' => $input['email'],
            'charges' => $input['charges'],
            'isDoctor' => true,
            'qualification' => $input['qualification'],
            'password' => Hash::make($input['email']),
            'user_id' => $doctor_id->id,
            'profile_image' => $new_name,
        ]);
        /**
         * Create a new user instance after a valid registration.
         *
         * @param  array  $data
         * @return \App\Models\User
         */
            // Log activity
            $this->createActivityLog('Register', 'A new Doctor Registered', 'Web', true);


        return redirect('healthworkers')->with('status', 'Doctor Added Successfully');
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function edit($id)
    {
        $doctor = Doctor::find($id);

        if (is_null($doctor)) {
            return('Doctor not found.');
        }
        $pending_requests = ClientRequest::where("status", 'pending')->get();
        $pending_requests_total =  ClientRequest::where("status", 'pending')->count();

        return view('edit-doctor', compact('doctor', 'pending_requests', 'pending_requests_total'));
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

        $doctor = Doctor::find($id);
        //check if an image was uploaded
        if ($request->hasFile('profile_image')) {
            // Store all ID images under one folder
            $destination_path = 'public/dps';
            //rename the image file to users name and current time
            $old_name = $request->profile_image->getClientOriginalName();
            //remove spaces in the name and replace with underscore

            $new_name =   str_replace(' ', '_', $request->input('name')). '_' . time() . '.' . $request->profile_image->getClientOriginalExtension();
            $request->profile_image->storeAs($destination_path, $new_name);
            $doctor->profile_image = $new_name;

        }
        $cats = $doctor->email;
        $doctor->name = $request->input('name');
        $doctor->email = $request->input('email');
        $doctor->phone = $request->input('phone');
        $doctor->status = $request->input('status');
        $doctor->charges = $request->input('charges');
        $doctor->qualification = $request->input('qualification');
        $doctor->update();

        $user = User::select('id')->where('email', '=', $cats)->update([
            'name' => $doctor->name,
            'email' => $doctor->email,
            'username' => $doctor->email,
        ]);
        // Log activity
        $this->createActivityLog('Update', 'Doctor Updated', 'Web', true);
        return redirect('healthworkers')->with('status', 'Doctor Updated Successfully');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);
        $cats = $doctor->email;
        $doctor->delete();
        $user = User::select('id')->where('email', '=', $cats)->delete();

        // Log activity
        $this->createActivityLog('Delete', 'Doctor Has been Deleted', 'Web', true);
        return redirect('healthworkers')->with('status', 'Doctor Deleted Successfully');
    }
//get all health workers

    public function doctors()
    {
        $doctors = Doctor::all();
        return response(['response' => 'success','data'=>$doctors]);
    }

//update Doctor Location
    public function updateDoctor(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        Doctor::find($id)->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        // Log Activity
        $this->createActivityLog('Doctor', 'Doctor Status Updated');

        return response(['response' => 'success', 'message' => 'Doctor Updated successfully']);
    }

    public function activateDoctor($id)
    {

        $doctor = Doctor::find($id);
        if(is_null($doctor)) {
            return response(['response' => 'Failed', 'message' => 'Doctor Not Found!!']);
        }
        Doctor::find($id)->update([
            'status' => 'active',
        ]);


        // Log Activity
        $this->createActivityLog('Doctor', 'Doctor Status activated');

        return response(['response' => 'success', 'message' => 'Doctor Status activated successfully']);
    }


    public function deactivateDoctor($id)
    {
        $doctor = Doctor::find($id);
        if(is_null($doctor)) {
            return response(['response' => 'Failed', 'message' => 'Doctor Not Found!!']);
        }
        Doctor::find($id)->update([
            'status' => 'inactive',
        ]);

        // Log Activity
        $this->createActivityLog('Doctor', 'Doctor Status deactivated');

        return response(['response' => 'success', 'message' => 'Doctor Status deactivated successfully']);
    }

    public function doctorStatus($id)
    {
        $doctor = Doctor::find($id);
        if(is_null($doctor)) {
            return response(['response' => 'Failed', 'message' => 'Doctor Not Found!!']);
        }

        // Log Activity
        return response(['message' => 'Doctor Status retrieved successfully','data'=>['status'=>$doctor->status]]);
    }
}
