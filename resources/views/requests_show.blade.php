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
    <h3 class="card-title ">Requests  Details</h3>
  </div>
  <!-- /.card-header -->
  <!-- this is the card hearde -->

  @if (count($patient_summary) > 0)

  <div class="card-body">
    {{-- show details here --}}
      <div class="row">
         <div class="col-md-4">
           {{-- put a disabled input field --}}
           <input type="text" class="form-control" value="Patient Details" disabled>
           {{-- put a disabled input field --}}
          <h3>patient Names</h3>
          <p>{{ $patient_summary[0]->patient_names }}</p>

          <h3>Lab Services</h3>
          <p>
            @foreach ($lab_services as $key=>$value)
             {{-- create an ordered list --}}
              <li>
                {{ $value }}
              </li>



            @endforeach
           </p>

         </div>
          <hr/>

         <div class="col-md-4">
          <input type="text" class="form-control" value="Doctor  Details" disabled>
          <h3>Doctor Names</h3>
          <p>{{ $patient_summary[0]->doctor_names }}</p>

          <h3>Description</h3>
          <p>{{ $patient_summary[0]->description}}</p>





         </div>
         <hr/>
         <div class="col-md-4">
          <input type="text" class="form-control" value="Charges Applied" disabled>
            <h3>Doctot Charge</h3>
            <p>shs {{  number_format($patient_summary[0]->doctor_charge, 2) }}</p>

            <h3>Lab Charge</h3>
            <p>shs {{ number_format($patient_summary[0]->lab_charge ,2)}}</p>

            <h3>Added Charge</h3>
            <p>shs {{ number_format($patient_summary[0]->added_charge, 2) }}</p>

            <h3>Total Charge</h3>
            <p>shs {{ number_format($patient_summary[0]->total_amount, 2) }}</p>

         </div>

      </div>
    {{-- show details here --}}


  </div>

  @else
   {{-- display no details --}}


   {{-- display no details --}}

  @endif

</div>
</div>
@endsection
