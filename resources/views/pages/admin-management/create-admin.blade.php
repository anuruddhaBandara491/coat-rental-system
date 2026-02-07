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
                    <form action="{{route('admin.store')}}" id="createAdmin" method="post" autocomplete="off">
                        @csrf
                        <div class="px-3 py-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                           placeholder="Enter the user first name">
                                    <span class="text-danger" style="font-weight: bold" id="firstNameError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name"
                                           placeholder="Enter the user last name">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email"
                                           placeholder="Enter the user email">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="phone"
                                           placeholder="Enter the user Phone Number">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Role</label>
                                    <select class="form-select select2" name="role_id" id="role_id">
                                        <option value="" selected disabled>Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" style="font-weight: bold" id="roleIdError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Branch</label>
                                    <select class="form-select select2" name="branch" id="branch">
                                        <option value="" selected disabled>Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="inputPassword">Password</label>
                                    <input type="password" id="inputPassword" class="form-control"
                                           placeholder="Password"
                                           name="password">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="inputConfirmPassword">Confirm password</label>
                                    <input type="password" id="inputConfirmPassword" class="form-control"
                                           placeholder="Confirm password" name="password_confirmation">
                                    <span class="text-danger" style="font-weight: bold" id="passwordError"></span>
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
            $('#createAdmin').on('submit', function (event) {
                event.preventDefault();  // Prevent the default form submission

                // Clear previous errors
                $('#roleIdError').text('');
                $('#firstNameError').text('');
                $('#passwordConfirmationError').text('');
                $('#phoneError').text('');

                // Send the form data via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        showAlert('success', "{{ session('success') }}", "/admin");

                    },
                    error: function (response) {
                        if (response.status === 422) {  // Laravel validation error code
                            let errors = response.responseJSON.errors;

                            if (errors.first_name) {
                                $('#firstNameError').text(errors.first_name[0]);
                            }
                            if (errors.role_id) {
                                $('#roleIdError').text(errors.role_id[0]);
                            }
                            if (errors.password) {
                                $('#passwordError').text(errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $('#passwordConfirmationError').text(errors.password_confirmation[0]);  // Display confirmation error
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
