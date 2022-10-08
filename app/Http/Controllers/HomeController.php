<?php

namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Client;
use App\Traits\LogTrait;
use App\Models\ClientRequest;
use DB;

class HomeController extends Controller
{

    use LogTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $doctors =  Doctor::all();
        $users =  User::all();
        $clients = Client::all();
        $requests = DB::table('requests')->get();
        return view('admin.index',compact('doctors','users', 'clients', 'requests'));
    }

    // 
    // Show Activity Logs
    public function activityLogs(Request $request)
    {
        $logs = UserActivityLog::latest()->paginate(200);

        return view('admin.activityLogs', compact('logs'));
    }
}
