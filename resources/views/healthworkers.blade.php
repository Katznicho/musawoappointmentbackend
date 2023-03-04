@extends('layouts.app1')

@section('content')
<div class="container">
   @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
  @endif
<div class="card">
  <div class="card-header">
    <h3 class="card-title ">Health Workers List</h3>
    <h4 class="float-sm-right ">
      <a class="btn btn-success" href="{{ route('addDoctor') }}">Add New Health Worker</a>
    </h4>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
      <th scope="col">IMAGE</th>
      <th scope="col">NAME</th>
      <th scope="col">EMAIL</th>
      <th scope="col">CONTACT</th>
      <th scope="col">ROLE</th>
      <th scope="col">LOCATION</th>
      <th scope="col">CHARGES</th>
      <th scope="col">QUALIFICATION</th>
      <th scope="col">STATUS</th>
      <th scope="col">Edit</th>
      {{-- <th scope="col">Delete</th> --}}
      </tr>
      </thead>
      <tbody>

      @foreach ($doctors as $doctor)
      <tr>
        <td>
            <img
             src="https://app.musawo.adfamedicareservices.com/musawoappointmentbackend/public/storage/dps/{{$doctor->profile_image}}"
             width="100px"
             height="100px"
             style="border-radius: 50%"
            />
        </td>
        <td>{{ $doctor->name }}</td>
        <td>{{ $doctor->email }}</td>
        <td>{{ $doctor->phone }}</td>
        <td>{{ $doctor->role }}</td>
        <td>{{ $doctor->address }}</td>
        <td>UGX. {{ $doctor->charges }}</td>
        <td>{{ $doctor->qualification }}</td>
        <td>{{ $doctor->status }}</td>

        <td>
          <a href="{{ url('edit-doctor/'.$doctor->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </td>
        {{-- <td>
          <a href="{{ url('delete-doctor/'.$doctor->id) }}" class="btn btn-danger btn-sm">Delete</a>
        </td> --}}
      </tr>
    @endforeach
      </tbody>

    </table>
  </div>
</div>
</div>
@endsection
