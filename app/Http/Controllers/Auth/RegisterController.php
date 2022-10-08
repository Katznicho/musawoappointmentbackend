<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Client;
use App\Models\Doctor;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\sendingEmail;
use Tokens;
use App\Traits\LogTrait;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use Symfony\Component\HttpFoundation\Response;


class RegisterController extends Controller
{

    use LogTrait;


    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return
            User::create([
                'name' => $data['name'],
                'role' => 'admin',
                'isDoctor' => true,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

    }

    public function store(Request $request){

        $credentials = $request->only('password', 'username', 'fname','lname','dob', 'c_password');
        //valid credential
        $validator = Validator::make($credentials, [
            'fname' => 'required',
            'lname' => 'required',
            'username' => 'required|unique:users,username',
            'dob' => 'required',
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }

        $rand = rand(1000, 9999);
        //log the random number



        $data = [];

        $email = $request['username'];

        $otp = $request['otp'];

        User::create([
            'name' => $request['fname'],
            'isDoctor' => $request['isDoctor']= false,
            'otp' => $request['otp']= $rand,
            'username' => $request['username'],
        ]);

        $client_id = FacadesDB::table('users')->where( 'username', '=', $email)->first();

        $client = Client::create([
            'fname' => $request['fname'],
            'lname' => $request['lname'],
            'username' => $request['username'],
            'dob' => $request['dob'],
            'isDoctor' => false,
            'user_id' => $client_id->id,
        ]);


        if (is_numeric($email)) {
            //$response = Http::get('https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&passwor
            //d=log//10tan10&message='.$rand.'&reciever='.$email);\
            $phone = $email;
            $message = $rand;
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://sms.thinkxsoftware.com/sms_api/api.php?link=sendmessage&user=musawoadfa&password=log10tan10&message='.$message.'&reciever='.$phone,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return response()->json(['status'=>200,'message' => 'Verification Code has been sent to your number']);
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data = ['otp'=>$rand];

                Mail::send('email_template', $data, function($message) use($email, $rand) {

                    $message->to($email)->subject('musawo Adfa');
                });

                return response()->json(['message' => 'Verification Code has been sent to your email']);
         }

        // Log activity
        $this->createActivityLog('Register', 'A new user registered');

        return response()->json(['message' => 'Verification Code has been to your Email'], 200);
    }

    public function login(Request $request){




        $credentials = $request->only( 'username');
        //valid credential
        $validator = Validator::make($credentials, [
            'username' => 'required'
        ]);


       //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'failure', 'data' => $validator->errors()],  Response::HTTP_BAD_REQUEST);
        }


        // $credentials = $request->only('username');



        if(!User::where('username', $request->username)->exists()) {
        // Log activity
        $this->createActivityLog('login', 'An authorized user trying to login');
            return response()->json(['message'=>'Unauthorized'], 401);
        }



        $user = User::where('username', $request->username)->first();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        $email = $user->username;
        $user_id = $user->id;
        $doctor = $user->email;
        $isDoctor = $user->isDoctor;
        $phone = $user->phone;


        if($isDoctor == true) {

            $doctor = FacadesDB::table('doctors')->where( 'phone', '=', $phone)->first();
            if($doctor == null){
                $this->createActivityLog('Error', 'A Doctor Failed to Logged in');
                return response()->json(['message'=>'Unauthorized'], 401);
            }
            else{
                $this->createActivityLog('Login', 'A Doctor Logged in');

                return response()->json(['message' => 'Logged successfully','data' => [
                    'user' => $doctor,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                    'user_id' => $user_id,
                ]]);

            }



        } else {
            $client = FacadesDB::table('clients')->where( 'username', '=', $email)->get();
            $this->createActivityLog('Login', 'A Client Logged in');

            return response()->json(['message' => 'Logged successfully','data' => [
                'user' => $client,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user_id' => $user_id,
            ]]);
        }
     }
}
