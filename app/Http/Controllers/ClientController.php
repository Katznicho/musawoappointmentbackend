<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Client;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use DB;
use App\Traits\LogTrait;

class ClientController extends Controller
{
    use LogTrait;
    //
    public function index()
    {
        $clients =  Client::all();
        return view('clients', compact('clients'));
    }

    public function getClientDetails($id){
        $client = Client::find($id);
        return response(['response' => 'success','data'=>$client]);
    }

//get all clients
    public function clients()
    {
        $clients = Client::all();
        return response(['response' => 'success','data'=>$clients]);
    }

//update Client
    public function updateClient(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|max:255',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'address' => 'required|max:255',
            'health_worker' => 'required|max:255',
        ]);

        Client::find($id)->update([
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'health_worker' => $request->health_worker,
        ]);

        //insert  into request table


        //Log Activity
        $this->createActivityLog('Client', 'Client Status Updated');

        return response(['response' => 'success', 'message' => 'Client Updated successfully']);
    }
}
