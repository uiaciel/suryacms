<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Uiaciel\SuryaCms\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Authenticate an administrator through the SuryaCMS login form.
     */
    public function store(LoginRequest $request): RedirectResponse|Redirector
    {
        $request->authenticate();

        $request->session()->regenerate();

        $dashboardRoute = Route::has('admin.dashboard')
            ? 'admin.dashboard'
            : 'admin.admin';

        return redirect()->intended(route($dashboardRoute));
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
