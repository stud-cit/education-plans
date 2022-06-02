<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required',
            'new_password' => 'required|min:8|confirmed', // 'new_password_confirmation'
        ]);

        $user = Auth::user();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->user()->fill([
            'password' => Hash::make($validated['new_password'])
        ])->save();

        return [
            'message' => __('passwords.reset')
        ];
    }
}
