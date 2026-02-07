@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0"><span class="text-muted">Admin User</span> <span class="mx-2">/</span>
                Update
                Admin User
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{{route('admin.update',$user->id)}}" id="updateAdmin" method="post"
                          autocomplete="off">
                        @csrf
                        <div class="px-3 py-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                           placeholder="Enter the user first name" value="{{$user->first_name}}">
                                    <span class="text-danger" style="font-weight: bold" id="firstNameError"></span>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name"
                                           placeholder="Enter the user last name" value="{{$user->last_name}}">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" name="email"
                                           placeholder="Enter the user email" value="{{$user->email}}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="phone"
                                           placeholder="Enter the user Phone Number" value="{{$user->phone}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Role</label>
                                    <select class="form-select select2" name="role_id" id="role_id">
                                        <option value="" selected disabled>Select Role</option>
                                        @if(!empty($selectRoles))
                                            <option value="{{$selectRoles->id}}"
                                                    selected>{{$selectRoles->name}}</option>
                                        @endif
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
                                        @if(!empty($selectBranch))

                                            <option value="{{$selectBranch->id}}"
                                                    selected>{{$selectBranch->name}}</option>
                                        @endif
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label>Active / Inactive</label>

                                    <label class="switch">
                                        <input type="checkbox" name="status"
                                               value="1" {{$user->status == 1 ? 'checked' : ''  }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="submit" class="btn btn-success" value="Update">
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
    <script>$(document).ready(function () {
            $('#updateAdmin').on('submit', function (event) {
                event.preventDefault();  // Prevent the default form submission

                // Clear previous errors
                $('#roleIdError').text('');
                $('#firstNameError').text('');
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
                        console.log(response)
                        if (response.status === 422) {  // Laravel validation error code
                            let errors = response.responseJSON.errors;
                            console.log(errors)

                            if (errors.first_name) {
                                $('#firstNameError').text(errors.first_name[0]);
                            }
                            if (errors.role_id) {
                                $('#roleIdError').text(errors.role_id[0]);
                            }
                            if (errors.password) {
                                $('#passwordError').text(errors.password[0]);
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
