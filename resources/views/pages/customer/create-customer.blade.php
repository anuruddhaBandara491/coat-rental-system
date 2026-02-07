@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0"><span class="text-muted">Customer</span> <span class="mx-2">/</span> Add
                Customer
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{{route('customer.store')}}" id="customer-create-form" method="post" enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf
                        <div class="px-3 py-4">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name"
                                           placeholder="Enter the customer first name">
                                    <span class="text-danger" id="firstNameError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name"
                                           placeholder="Enter the customer last name">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Nic No</label>
                                    <input type="text" class="form-control" name="nic"
                                           placeholder="Enter the customer nic">
                                    <span class="text-danger" id="nicError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email"
                                           placeholder="Enter the customer email">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="phone"
                                           placeholder="Enter the customer Phone Number">
                                    <span class="text-danger" id="phoneError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact No 2.</label>
                                    <input type="text" class="form-control" name="phone1"
                                           placeholder="Enter the customer Another Phone Number">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" cols="10" rows="5"></textarea>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="submit" class="btn btn-success" value="Submit">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#customer-create-form').on('submit', function (event) {
                event.preventDefault();  // Prevent the default form submission
                // Send the form data via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        showAlert('success', response.success, "/customer");

                    },
                    error: function (response) {
                        if (response.status === 422) {  // Laravel validation error code
                            let errors = response.responseJSON.errors;

                            if (errors.first_name) {
                                $('#firstNameError').text(errors.first_name[0]);
                            }
                            if (errors.nic) {
                                $('#nicError').text(errors.nic[0]);
                            }
                            if (errors.phone) {
                                $('#phoneError').text(errors.phone[0]);
                            }

                        }
                    }
                });
            });
        });
    </script>
@endpush
