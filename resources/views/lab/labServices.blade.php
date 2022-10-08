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
    <h3 class="card-title ">Laboratory Services</h3>
    <h4 class="float-sm-right ">
      <a class="btn btn-success" href="{{ url('addLabService') }}"> Add  New Lab Service</a>
    </h4>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>ID</th>
        <th>SERVICE NAME</th>
        <th>PRICE</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
      </thead>
      <tbody>
                
                   @foreach ($services as $service)
                   <tr>
                     <td>{{ $service->id}}</td>
                     <td>
                        {{ $service->name }}
                     </td>
                     <td>UGX {{ $service->price }}</td>
                     <td>
                        <a class="btn btn-primary btn-sm" href="{{ url('editLabService/'.$service->id) }}">Edit</a>
                     </td>
                     <td>
                        <a class="btn btn-danger btn-sm" href="{{ url('delete-service/'.$service->id) }}">Delete</a>
                     </td>
                   </tr>
                   @endforeach

      </tbody>

    </table>
  </div>
</div>
    
</div>
@endsection
