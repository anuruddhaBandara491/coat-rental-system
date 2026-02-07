<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->has('remember');

        if (Auth::attempt($credentials, $rememberMe)) {
            $user = Auth::user();
//            if ($user->status == 0) {
//                Auth::guard('web')->logout();
//                $request->session()->invalidate();
//                throw ValidationException::withMessages([
//                    'inactive' => 'You are Not Activate. Please Contact Admin',
//                ]);
//            }
//            if ($user->hasAnyRole(['branch_operator', 'admin'])) {
//                $request->session()->regenerate();

                return redirect()->route('dashboard.index')->with('success', 'You have Successfully logged in!!');
//            }

        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'inactive' => 'You have entered invalid credentials',
            ]);

        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        return redirect('/login');
    }
}
