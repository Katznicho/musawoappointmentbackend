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
    <h3 class="card-title ">Requests List</h3>
  </div>
  <!-- /.card-header -->
  <!-- this is the card hearde -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
      <th scope="col">Created Date</th>
      <th scope="col">message</th>
      <th scope="col">status</th>
      <th scope="col">Client Review</th>
      <th scope="col">Rating</th>
      <th scope="col">Updated Date</th>
      <th scope="col">Edit</th>
      <th scope="col">delete</th>
      </tr>
      </thead>
      <tbody>
                
      @foreach ($requests as $request)
      <tr>

        <td>{{ $request->created_at }}</td>
        <td>{{ $request->message }}</td>
        <td>{{ $request->status }}</td>
        <td>{{ $request->client_review }}</td>
        <td>{{ $request->rating }}</td>
        <td>{{ $request->updated_at }}</td>

        <td>
          <a href="{{ url('edit-request/'.$request->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </td>
        <td>
          <a href="{{ url('delete-request/'.$request->id) }}" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>
    @endforeach
      </tbody>

    </table>
  </div>
</div>
</div>
@endsection