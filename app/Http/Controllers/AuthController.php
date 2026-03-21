<?php

namespace App\Http\Controllers;

use App\Actions\Auth\AuthenticateUser;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request, AuthenticateUser $authenticateUser)
    {
        $authenticateUser->handle(
            $request->validated('email'),
            $request->validated('password'),
        );

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Authenticated.']);
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect('/login');
    }
}
