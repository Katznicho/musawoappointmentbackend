@extends('layouts.app1')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Payment') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{route('payments.update', $payment->id)}}">
                        @method('PUT')
                        @csrf

                        <div class="form-group row">
                            <label for="patient_names" class="col-md-4 col-form-label text-md-right">{{ __('Patient Names') }}</label>

                            <div class="col-md-6">
                                <input id="patient_names" type="text" class="form-control @error('patient_names')
                                is-invalid @enderror" name="patient_names"
                                value="{{ $payment->patient_names }}"
                                disabled
                                required>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="total_amount" class="col-md-4 col-form-label text-md-right">{{ __('Total Amount') }}</label>

                            <div class="col-md-6">
                                <input id="total_amount" type="text" class="form-control
                                 @error('total_amount') is-invalid @enderror"
                                 total_amount="total_amount"
                                 value="{{ $payment->total_amount }}"
                                 required autocomplete="total_amount"
                                  readonly
                                  >

                                @error('total_amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="payment_ref" class="col-md-4 col-form-label text-md-right">{{ __('Payment Reference') }}</label>

                            <div class="col-md-6">
                                <input id="payment_ref" type="text" class="form-control @error('payment_ref')
                                is-invalid @enderror" name="payment_ref"
                                 value="{{ $payment->payment_reference }}"
                                 required
                                 autocomplete="payment_ref"
                                 disabled
                                 >

                                @error('payment_ref')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="payment_status" class="col-md-4 col-form-label text-md-right">{{ __('Payment Status') }}</label>

                            <div class="col-md-6">
                                <select class="form-control @error('payment_status') is-invalid @enderror"
                                id="payment_status"
                                 name="payment_status" value="{{ old('payment_status') }}"
                                 required autocomplete="payment_status">
                                  <option selected>
                                    {{
                                        $payment->payment_status

                                    }}

                                  </option>

                                    <option>pending</option>
                                    <option>completed</option>

                                </select>

                                @error('payment_status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- mode of payment --}}
                        <div class="form-group row">
                            <label for="payment_method" class="col-md-4 col-form-label text-md-right">{{ __('Payment Method') }}</label>

                            <div class="col-md-6">
                                <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method"
                                 name="payment_method" value="{{ old('payment_method') }}"
                                 required autocomplete="payment_method">
                                  <option selected>
                                    {{
                                        $payment->mode_of_payment

                                    }}

                                  </option>

                                    <option>mobile money</option>
                                    <option>cash</option>
                                    <option>merchant code</option>
                                    <option>app</option>

                                </select>

                                @error('paymet_method')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- mode of payment --}}

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <input id="description"
                                type="text" class="form-control
                                @error('description') is-invalid @enderror" name="description"
                                required autocomplete="description"
                                value="{{ $payment->narrative }}"
                                >

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Payment') }}
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
