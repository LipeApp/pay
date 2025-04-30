<?php

namespace UzPaymentGateways\Http\Controllers\Admin;

use UzPaymentGateways\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('payment-gateways::admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $allowedUsers = Config::get('payment-gateways.admin.auth.users', []);
        $credentials = $request->only('email', 'password');

        foreach ($allowedUsers as $user) {
            if ($user['email'] === $credentials['email'] && $user['password'] === $credentials['password']) {
                Auth::loginUsingId(1); // Используем ID 1 для авторизации
                return redirect()->intended(route('admin.transactions.index'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
} 