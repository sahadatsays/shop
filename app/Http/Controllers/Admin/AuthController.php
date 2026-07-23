<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private AuditService $audit) {}

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
            $this->audit->logAdminLoginFailed($request->string('email')->toString(), 'Invalid credentials.', $request);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::guard('admin')->user();

        if (! $user->is_active) {
            Auth::guard('admin')->logout();
            $this->audit->logAdminLoginFailed($request->string('email')->toString(), 'Inactive account.', $request);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This admin account is inactive.']);
        }

        if (! $user->roles()->exists()) {
            Auth::guard('admin')->logout();
            $this->audit->logAdminLoginFailed($request->string('email')->toString(), 'No admin role assigned.', $request);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account does not have an assigned admin role.']);
        }

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();
        $this->audit->logAdminLogin($user, $request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('admin')->user();
        $admin = $user instanceof User ? $user : null;

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $this->audit->logAdminLogout($admin, $request);

        return redirect()->route('admin.login');
    }
}
