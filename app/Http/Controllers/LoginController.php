<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
         $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return [
            'token' => $user->createToken('admin')->plainTextToken,
            'user' => $user,
        ];

    }

    public function logout(Request $request)
    {
        Auth::logout();
        // if (!$request->user()) return;
        // $request->user()->currentAccessToken()->delete();
        // return [
        //     'message' => __('auth.logout'),
        // ];
    }
}
