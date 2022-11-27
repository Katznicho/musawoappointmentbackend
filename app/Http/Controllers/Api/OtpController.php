<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\sendingEmail;
use DB;
use App\Models\Client;
use App\Traits\LogTrait;
use Illuminate\Support\Facades\DB as FacadesDB;
use Symfony\Component\HttpFoundation\Response;

class OtpController extends Controller
{

    use LogTrait;
    //

    public function verify(Request $request){


        $credentials = $request->only('otp');
        $validator = Validator::make($credentials, [
            'otp' => 'required',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $otp = $request['otp'];
        $user =FacadesDB::table('users')->where( 'otp', '=', $otp)->get();
        $setOtp = User::select('otp')->where('otp', '=', $otp)->update([
            'otp' => NULL,
        ]);

        if ($user->isEmpty()) {
        // Log activity
        $this->createActivityLog('Verify', 'OTP Invalid', 'App');
            return response()->json(['message'=>'Invalid Code,'], 401);
        } else {

        // Log activity
        $email = $user[0]->username;
        $client = FacadesDB::table('clients')->where( 'username', '=', $email)->get();
        $this->createActivityLog('Verify', 'OTP Verified', 'APP');
        
            return response()->json(
                [
                    'message' => 'Logged successfully',
                    'data'=>['user'=>$client]
                ]
            );
        }
    }

    public function resend(Request $request){

        $credentials = $request->only('username');
        $validator = Validator::make($credentials, [
            'username' => 'required',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $otp = rand(1000,9999);
        $email = $request['username'];
        $client = User::select('id')->where('username', '=', $email)->get();
        if($client->isEmpty()){
            return response()->json(['message' => 'No client found with this username'], 200);
        } else {
            $user = User::select('id')->where('username', '=', $email)->update([
                'otp' => $otp,
            ]);

            if (is_numeric($email)) {
                $response = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message='.$otp.'&reciever='.$email);
                return response()->json(['status'=>$response->getStatusCode(),'message' => 'Verification Code has been sent to your number']);
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data = ['otp'=>$otp];
                Mail::send('email_template', $data, function($message) use($email, $otp) {

                    $message->to($email)->subject('Musawo Adfa');
                });

                return response()->json(['message' => 'OTP has been sent to your email']);
             }

            // Log activity
            $this->createActivityLog('resend', 'New Otp generated and sent', 'App');

            return response()->json(['message' => 'Verification Code has been to your Email'], 200);
        }
    }
  //'https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message=1234&reciever=0759983853


// forgot passsword
    public function forgotPassword(Request $request){

        $credentials = $request->only('username');
        $validator = Validator::make($credentials, [
            'username' => 'required',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $otp = rand(1000,9999);
        $email = $request['username'];
        $client = User::select('id')->where('username', '=', $email)->get();
        if($client->isEmpty()){
            return response()->json(['message' => 'No client found with this username'], 200);
        } else {
            $user = User::select('id')->where('username', '=', $email)->update([
                'otp' => $otp,
            ]);
            if (is_numeric($email)) {
                $response = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message='.$otp.'&reciever='.$email);
                return response()->json(['status'=>$response->getStatusCode(),'message' => 'Verification Code has been sent to your number']);
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data = ['otp'=>$otp];
                Mail::send('email_template', $data, function($message) use($email, $otp) {

                    $message->to($email)->subject('Musawo Adfa');
                });

                return response()->json(['message' => 'OTP has been sent to your email']);
             }
        }
    }
// verify otp
    public function verifyOtp(Request $request){

         $credentials = $request->only('otp');
        $validator = Validator::make($credentials, [
            'otp' => 'required|string',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $otp = $request['otp'];
        $user = FacadesDB::table('users')->where( 'otp', '=', $otp)->get();
        $setOtp = User::select('otp')->where('otp', '=', $otp)->update([
            'otp' => NULL,
        ]);

        if ($user->isEmpty()) {
        // Log activity
        $this->createActivityLog('Verify', 'OTP Invalid', 'App');
            return response()->json(['message'=>'Invalid Code,'], 401);
        } else {

        // Log activity
        $email = $user[0]->username;
        $this->createActivityLog('Verify', 'OTP Verified', 'APP');
            return response()->json(
                [
                    'message' => 'Logged successfully',
                    'data'=>['email'=>$email]
                ]
            );
        }
    }

    public function resetPassword(Request $request, $email){

        $credentials = $request->only('password', 'c_password');
        $validator = Validator::make($credentials, [
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $user = FacadesDB::table('users')->where('username', "=", $email)->get();
        if ($user->isEmpty()) {
            return response()->json(['message'=>'User Not Found,'], 401);
        } else {

                $updateUser = FacadesDB::table('users')->where('username', "=", $email)->update([
                    'password' => Hash::make($request['password'])
                ]);

                   // Log activity
                   $this->createActivityLog('resend', 'New Otp generated and sent', 'App');

                   return response()->json(['message' => 'Password Reset successfully'], 200);

        }


    }

    public function updateToken(Request $request, $id){
        $request->validate([
            'push_token' => 'required|string',
        ]);

        $credentials = $request->only('push_token');
        $validator = Validator::make($credentials, [
            'push_token' => 'required|string',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message'=>'User not Found'], 401);
        } else {

            $setToken = User::select('id')->where('id', '=', $id)->update([
                'push_token' => $request['push_token'],
            ]);

            return response()->json(
                [
                    'message' => 'Token Updated Successfully'
                ]
            );
        }
    }

    public function retrieveToken($id) {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message'=>'User not Found'], 401);
        } else {
            $token = User::select('push_token')->where('id', '=', $id)->get();
            return response()->json(
                [
                    'message' => 'Token Updated Successfully',
                    'data'=>['push_token'=>$token]
                ]
            );
        }
    }
}
