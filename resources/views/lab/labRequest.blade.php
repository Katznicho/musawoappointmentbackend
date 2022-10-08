@extends('layouts.app1')

@section('content')
@if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
  @endif
<div class="container">
<div class="card">
  <div class="card-header">
    <h3 class="card-title ">Laboratory Services Requests</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>Service Name</th>
        <th>Client Name</th>
        <th>Client Contact</th>
        <th>Client Address</th>
        <th>Request Status</th>
        <th>Price</th>
        <th>Date Created</th>
        <th scope="col">Edit</th>
      </tr>
      </thead>
      <tbody>
                
                   @foreach ($labRequests as $labRequest)
                   <tr>
                     <td>
                        {{ $labRequest->service_name }}
                     </td>
                     <td>{{ $labRequest->client_name}}</td>
                     <td>{{ $labRequest->client_contact }}</td>
                     <td>{{ $labRequest->client_address }}</td>
                     <td>{{ $labRequest->status }}</td>
                     <td>{{ $labRequest->price }}</td>
                     <td>{{ date('d/m/Y', strtotime($labRequest->created_at)) }}</td>       
                     <td>
                        <a href="{{ url('edit-LabRequest/'.$labRequest->id) }}" class="btn btn-primary btn-sm">Edit</a>
                     </td>

                   </tr>
                   @endforeach

      </tbody>

    </table>
  </div>
</div>
    
</div>
@endsection