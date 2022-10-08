@extends('layouts.app1')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Lab Request') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('update-LabRequest/'.$labRequest->id) }}" id="editLabRequest" name="editLabRequest">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>

                            <div class="col-md-6">
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" value="{{$labRequest->status}}" required autocomplete="role">

                              <option>accepted</option>
                              <option>cancelled</option>
                              <option>completed</option>
                            </select>

                                @error('status')
                                    <span class="invalid-feedback" role="alert"> 
                                        <strong>{{ $status }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Status') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection