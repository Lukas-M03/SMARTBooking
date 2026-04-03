<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Enter a valid email address (example@domain.com).',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Your new password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = User::where('email', $request->string('email')->toString())->first();

        if (! $user) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'No account found for this email address.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->string('password')->toString()),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('success', 'Your password has been reset successfully.');
    }
}
