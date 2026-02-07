@extends('layouts.auth.app')
@section('title', 'Login')
@section('content')
    <div class="vh-100 vw-100 d-flex align-items-center justify-content-center ic-auth-page">

        <div class="container">
            <div class="row">
                <div class="col-md-4 mx-auto">

                    <form method="post" action="{{ route('auth.authenticate') }}">

                        @csrf
                        <div class="d-flex flex-column gap-4 bg-white shadow rounded p-4 mb-4">
                            <div class="text-center">
                                <img class="img-fluid" src="{{ asset('assets/img/sanjaya.png') }}"
                                    style="height: 100px; background-color:black" alt="logo">

                            </div>
                            @error('inactive')
                            <span style="color: red; font-weight: bold">{{ $message }}</span>
                            @enderror
                            <p class="text-center mb-0">
                                Please sign-in to your account.
                            </p>
                            <div class="form-floating form-floating-outline">
                                <input type="email" id="inputEmail" class="form-control" placeholder="Email"
                                       name="email">
                                <label for="inputEmail">Email</label>
                                @error('email')
                                <span style="color: red; font-weight: bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-floating form-floating-outline">
                                <input type="password" id="inputPassword" class="form-control" placeholder="Password"
                                       name="password">
                                <label for="inputPassword">Password</label>
                                @error('password')
                                <span style="color: red; font-weight: bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-sm-6 text-center text-sm-start">
                                    <div class="form-check mb-0">
                                        <input type="checkbox" id="inputRememberMe" class="form-check-input" name="">
                                        <label for="inputRememberMe" class="form-check-label">Remember Me</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 text-center text-sm-end">
                                    <a href="./forgot-password" class="footer-link text-primary">Forgot Password?</a>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary">Login</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @if(session('success'))
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <script>
            swal("Success!", "{{ session('success') }}", "success");
        </script>
    @elseif(session('error'))
        <script>
            swal("Error!", "{{ session('error') }}", "error");
        </script>
    @endif
@endpush
