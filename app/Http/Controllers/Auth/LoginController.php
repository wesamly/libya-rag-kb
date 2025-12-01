<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
     /**
     * Show the login form (which is just the Vue app).
     * We redirect to /admin if already logged in.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.index');
        }
        // This view loads the Vue app, which handles the login form UI
        return view('admin');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Return a JSON response for the Vue app
        return response()->json(['message' => 'Login successful.']);
    }

    /**
     * Handle a logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return a JSON response
        return response()->json(['message' => 'Logout successful.']);
    }
}
