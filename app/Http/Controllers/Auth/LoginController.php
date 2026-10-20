<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(Request $request): View
    {
        // "Log in to write a review" links pass ?redirect= so customers land back on the product.
        // Only addresses on this site are accepted, so the link can't be abused to send people elsewhere.
        $redirect = $request->string('redirect')->toString();

        if (str_starts_with($redirect, url('/').'/')) {
            redirect()->setIntendedUrl($redirect);
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $fallback = $request->user()->isAdmin() ? route('admin.dashboard') : route('home');

        return redirect()->intended($fallback)->with('success', "Welcome back, {$request->user()->name}!");
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
