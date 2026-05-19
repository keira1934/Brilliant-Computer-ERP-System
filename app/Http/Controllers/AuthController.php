<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;

class AuthController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Your account has been deactivated. Contact your administrator.')->withInput();
            }

            $this->auditService->log('auth', 'login', $user, null, null, "User {$user->name} logged in");

            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Invalid email or password.')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $this->auditService->log('auth', 'logout', $user, null, null, "User {$user->name} logged out");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
