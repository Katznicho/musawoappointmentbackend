@extends('layouts.app1')

@section('content')
<div class="container">
            <div class="card">
                <div class="card-header">Activity logs</div>

                <div class="card-body">

                <!-- {{ $logs->links() }} -->

    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
      <th scope="col">User</th>
      <th scope="col">Ip Address</th>
      <th scope="col">Action</th>
      <th scope="col">Method</th>
      <th scope="col">Path</th>
      <th scope="col">Description</th>
      <th scope="col">Platform</th>
      <th scope="col">Status</th>
      <th scope="col">Date</th>
      </tr>
      </thead>
      <tbody>
                
      @foreach ($logs as $log)
      <tr>

        <td>{{ $log->user_name ? $log->user_name : 'Anonymous' }}</td>
        <td>{{ str_replace('.', '. ', $log->ip_address) }}</td>
        <td>{{ $log->action }}</td>
        <td>{{ $log->method }}</td>
        <td>{{ str_replace('/', '/ ', $log->path) }}</td>
        <td>{{ $log->description }}</td>
         <td>{{ $log->platform }}</td>
        <td>{{ $log->status }}</td>
        <td>{{ date('d/m/Y', strtotime($log->created_at)) }}</td>
      </tr>
    @endforeach
      </tbody>

    </table>

            <!-- {{ $logs->links() }} -->
        </div>
    </div>
</div>
@endsection