@extends('layouts.app1')

@section('content')
<div class="container">
<div class="card">
  <div class="card-header">
    <h3 class="card-title ">Clients List</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">Date of Birth</th>
      <th scope="col">Registerd Date</th>
      </tr>
      </thead>
      <tbody>
                
      @foreach ($clients as $client)
      <tr>

        <td>{{ $client->id }}</td>
        <td>{{ $client->fname}} {{ $client->lname}}</td>
        <td>{{ $client->dob }}</td>
        <td>{{ $client->created_at }}</td>
      </tr>
    @endforeach
      </tbody>

    </table>
  </div>
</div>
</div>
@endsection