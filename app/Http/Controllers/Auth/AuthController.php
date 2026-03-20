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
        // Modules shown in registration come directly from the expertise table.
        $expertiseList = Expertise::all();

        // Load advisers with their expertise so the registration form can
        // dynamically filter the adviser list based on module selection.
        $advisers = User::where('role', 'adviser')
            ->with('expertise')
            ->get()
            ->map(fn ($a) => [
                'id'         => $a->id,
                'name'       => $a->name,
                'expertise'  => $a->expertise->pluck('id')->all(),
            ]);

        return view('auth.register', compact('expertiseList', 'advisers'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'             => ['required', 'string', 'min:8', 'confirmed'],
            'role'                 => ['required', 'in:student,adviser'],
            'student_id'           => ['nullable', 'string', 'max:50'],
            'phone'                => ['nullable', 'string', 'max:20'],
            // Adviser fields
            'expertise_id'         => ['nullable', 'required_if:role,adviser', 'exists:expertise,id'],
            // Student fields
            // Students pick a single module (same table as adviser expertise).
            'modules'              => ['nullable', 'required_if:role,student', 'exists:expertise,id'],
            'preferred_adviser_id' => ['nullable', 'exists:users,id'],
        ]);

        $user = User::create([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'password'             => Hash::make($validated['password']),
            'role'                 => $validated['role'],
            'student_id'           => $validated['student_id'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'preferred_adviser_id' => $validated['preferred_adviser_id'] ?? null,
        ]);

        // Attach expertise areas if the user is an adviser.
        if ($validated['role'] === 'adviser' && !empty($validated['expertise_id'])) {
            $user->expertise()->attach($validated['expertise_id']);
        }

        // Attach selected student module (single expertise ID) via pivot table.
        if ($validated['role'] === 'student' && !empty($validated['modules'])) {
            $user->modules()->attach($validated['modules']);
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
