@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('customer.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Customer</a>
                <span class="mx-2">/</span> View Customer
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <div class="px-3 py-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name"
                                       value="{{$customer->first_name}}" disabled>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name"
                                       value="{{$customer->last_name}}" disabled>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Nic No</label>
                                <input type="text" class="form-control" name="nic" value="{{$customer->nic}}"
                                       disabled>

                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" value="{{$customer->email}}"
                                       disabled>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Contact No.</label>
                                <input type="text" class="form-control" name="phone" value="{{$customer->phone}}"
                                       disabled>

                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" cols="10"
                                          rows="5" disabled>{{$customer->address}}</textarea>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
