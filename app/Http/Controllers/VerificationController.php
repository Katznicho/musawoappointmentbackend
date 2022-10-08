<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Models\User;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    // use VerifiesEmails;

    // /**
    //  * Where to redirect users after verification.
    //  *
    //  * @var string
    //  */
    // // protected $redirectTo = RouteServiceProvider::HOME;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    public function __construct()
    {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

        /**
        * Resend the email verification notification
        * @param \Illuminate\Http\Request $request
        * @return \Illuminate\Http\Response
        * @throws \Illuminate\Auth\Access\AuthorizationException
     */


    // public function verify(Request $request){
    //     auth()->loginUsingId($request->route('id'));

    //     if ($request->route('id') != $request->user()->getKey()){
    //         throw new AuthorizarionException;
    //     }

    //     if ($request->user()->hasVerifiedEmail()){
    //         return response(['message'=>'Already']);
    //         // return redirect($this->redirectionPath());
    //     }
    //     if ($request->user()->markEmailAsVerified()){
    //         event(new Verified($request->user()));
    //     }

    //     return response(['message'=>'SuccessFully verified']);
    // }
    /**
        * Resend the email verification notification
        * @param \Illuminate\Http\Request $request
        *@return \Illuminate\Http\Response
     */



    // public function resend(Request $request) {
    //     if($request->user()->hasVerifiedEmail()){
    //         return response(['message'=>'Already Verified']);
    //     }
    //     $request->user()->sendEmailVerificationNotification();

    //     if ($request->wantsJson()) {
    //         return response(['message'  => 'Email Sent']);
    //     }

    //     return back()->with('resent', true); 
    // }

    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }
    
        $user = User::findOrFail($user_id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return response(['message'=>'Already']);
    }
    
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }
    
        auth()->user()->sendEmailVerificationNotification();
    
        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}