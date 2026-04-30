<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password request form.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Confirm the email address and redirect to the password reset screen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Enter a valid email address (example@domain.com).',
        ]);

        $email = $request->string('email')->toString();
        $token = Str::random(40);

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $email,
        ])->with('success', 'Email confirmed. You can now reset your password.');
    }
}
