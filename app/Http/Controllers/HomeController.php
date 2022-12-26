<?php

namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Client;
use App\Traits\LogTrait;
use App\Models\ClientRequest;
use App\Models\PatientSummary;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

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
        $requests = FacadesDB::table('requests')->get();
        //count all payments where status is pending
         $pending_payments =  PatientSummary::where('payment_status', 'pending')->count();
        //count all payments where status is completed
        $completed_payments =  PatientSummary::where('payment_status', 'completed')->count();
        //sum up all payments where status is completed
        $total_completed_payments =  PatientSummary::where('payment_status', 'completed')->sum('total_amount');
        //count all payments where status is pending
        $total_pending_payments =  PatientSummary::where('payment_status', 'pending')->sum('total_amount');

        return view(
            'admin.index',
        compact('doctors','users', 'clients', 'requests', 'pending_payments', 'completed_payments', 'total_completed_payments', 'total_pending_payments')
    );
    }

    //
    // Show Activity Logs
    public function activityLogs(Request $request)
    {
        $logs = UserActivityLog::latest()->paginate(200);

        return view('admin.activityLogs', compact('logs'));
    }
}
