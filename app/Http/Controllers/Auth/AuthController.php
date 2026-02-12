<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Expertise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            if ($user->isStudent()) {
                return redirect()->intended('/student/dashboard');
            } elseif ($user->isAdviser()) {
                return redirect()->intended('/adviser/dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $expertiseList = Expertise::all();
        return view('auth.register', compact('expertiseList'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:student,adviser'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'expertise' => ['nullable', 'array'],
            'expertise.*' => ['exists:expertise,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_id' => $validated['student_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        // Attach expertise if the user is an adviser
        if ($validated['role'] === 'adviser' && !empty($validated['expertise'])) {
            $user->expertise()->attach($validated['expertise']);
        }

        Auth::login($user);

        if ($user->isStudent()) {
            return redirect('/student/dashboard');
        } elseif ($user->isAdviser()) {
            return redirect('/adviser/dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
