<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Request;

class SettingController extends Controller
{
    public function viewProfile()
    {
        $loggedInUser = auth()->user();

        return view('pages.setting.my-profile', compact('loggedInUser'));

    }

    public function profileUpdate(Request $request)
    {
        $loggedInUser = auth()->user();
        if ($loggedInUser->first_name == $request['first_name'] &&
            $loggedInUser->last_name == $request['last_name'] &&
            $loggedInUser->email == $request['email'] &&
            $loggedInUser->phone == $request['phone']) {
            // No changes, so no need to update the profile
            return back()->withErrors('error', 'No changes made.');
        }
        $loggedInUser->update([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
        ]);

        return back()->with('success', 'Profile Updated Successfully.!!');
    }

    public function updatePassword(Request $request)
    {

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])/', 'confirmed'],
        ],
            [
                'password.regex' => 'The password must contain at least one uppercase letter and one symbol.',
            ]

        );

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => date('Y-m-d H:i:s'),
        ]);

        return back()->with('success', 'password updated Successfully!');
    }
}
