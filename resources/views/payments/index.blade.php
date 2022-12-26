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
    <h3 class="card-title ">Payment |List</h3>
  </div>
  <!-- /.card-header -->
  <!-- this is the card hearde -->
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead>
      <tr>
      <th scope="col">Patient Name</th>
      <th scope="col">Doctor Name</th>
      <th scope="col">Total Amount</th>
      <th scope="col">Payment Status</th>
      <th scope="col">Payment Reference</th>
      <th scope="col">Method Of Payment</th>
      <th scope="col">Narrative</th>
        <th scope="col">Payment  Date</th>
      <th scope="col">Edit</th>
      {{-- <th scope="col">Show</th> --}}
      </tr>
      </thead>
      <tbody>

      @foreach ($payments as $payment)
      <tr>

        <td>{{ $payment->patient_names}}</td>
        <td>{{ $payment->doctor_names }}</td>
        <td>{{ $payment->total_amount }}</td>
        <td>{{ $payment->payment_status }}</td>
        <td>{{ $payment->payment_reference }}</td>
        <td>{{ $payment->mode_of_payment }}</td>
        <td>{{ $payment->narrative }}</td>
        <td>{{ $payment->updated_at }}</td>

        <td>
          <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </td>
        {{-- <td>
          <a href="{{ url('delete-request/'.$request->id) }}" class="btn btn-danger btn-sm">show</a>
        </td> --}}
        {{-- <td>
          <a href="{{ url('show-details/'.$payment->id) }}" class="btn btn-info btn-sm">show</a>
        </td>
      </tr> --}}
    @endforeach
      </tbody>

    </table>
  </div>
</div>
</div>
@endsection
