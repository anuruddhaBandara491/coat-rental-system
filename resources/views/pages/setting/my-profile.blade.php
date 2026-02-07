@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('content')

    <span></span>
    <div class="row">
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header bg-light-grey">
                    <h6 class="fw-semibold mb-0">Update Profile</h6>
                </div>
                <form action="{{route('setting.profile-update')}}" method="post">
                    @csrf
                    <div class="card-body d-flex flex-column gap-4">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" value="{{$loggedInUser->first_name}}"
                                           name="first_name" id="first_name">
                                    <label for="first_name">First name</label>
                                    @error('first_name')
                                    <span style="color: red;font-weight: bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" value="{{$loggedInUser->last_name}}"
                                           name="last_name" id="last_name">
                                    <label for="last_name">Last name</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating form-floating-outline">
                            <input type="email" class="form-control" value="{{$loggedInUser->email}}" name="email"
                                   id="email">
                            <label for="email">Email</label>
                        </div>
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" value="{{$loggedInUser->phone}}" name="phone"
                                   id="phone">
                            <label for="phone">Phone</label>
                        </div>
                        <div>
                            <button class="btn btn-secondary" type="submit" id="submitBtn" name="">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header bg-light-grey">
                    <h6 class="fw-semibold mb-0">Change Password</h6>
                </div>
                <form action="{{route('setting.update-password')}}" method="post">
                    @csrf
                    <div class="card-body d-flex flex-column gap-4">
                        <div class="alert alert-warning mb-0">
                            <p class="mb-0">
                                Ensure that these requirements are met<br>
                                <small>Minimum 8 characters long, uppercase & symbol</small>
                            </p>
                        </div>
                        <div class="form-floating form-floating-outline">
                            <input type="password" class="form-control" value="" name="current_password">
                            <label>Current Password</label>
                            @error('current_password', 'updatePassword')
                            <span style="color: red; font-weight: bold" class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-floating form-floating-outline">
                            <input type="password" class="form-control" value="" name="password">
                            <label>New Password</label>
                        </div>
                        <div class="form-floating form-floating-outline">
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation">
                            <label for="password_confirmation">Confirm New Password</label>
                            @error('password', 'updatePassword')
                            <span style="color: red; font-weight: bold" class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <button class="btn btn-secondary" type="submit" name="">Change Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        @if(session('success'))

        swal("Success!", "{{ session('success') }}", "success");
        @elseif(session('error'))
        swal("Error!", "{{ session('error') }}", "error");
        @endif
    });
</script>
<script>

    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll('input[type="password"]');

        inputs.forEach(input => {
            input.addEventListener("input", function () {
                const errorMessage = this.parentElement.querySelector('span');
                if (errorMessage) {
                    errorMessage.style.display = "none";
                }
            });
        });
    });
</script>
