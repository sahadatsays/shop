<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login', [
            'title' => 'Admin Sign In',
        ]);
    }

    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::guard('admin')->user();

        if (! $user->is_active) {
            Auth::guard('admin')->logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This admin account is inactive.']);
        }

        if (! $user->roles()->exists()) {
            Auth::guard('admin')->logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account does not have an assigned admin role.']);
        }

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
