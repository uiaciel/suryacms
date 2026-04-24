<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $users = User::all();

        return view('suryacms::livewire.admin.user.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('suryacms::livewire.admin.user.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // Handle password update
        if ($request->has('update_password') && $request->update_password) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'min:6', 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);

            return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
        }

        // Handle profile update
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ], [], [
            'name' => 'Name',
            'email' => 'Email Address',
        ]);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
        ], [], [
            'password' => 'Password',
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function create(Request $request): View
    {
        // Only admin (user ID 1) can create new users
        if (auth()->id() !== 1) {
            abort(403, 'Unauthorized access.');
        }

        $verified = session('password_verified', false);

        return view('suryacms::livewire.admin.user.create', compact('verified'));
    }

    public function verifyPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required'],
        ], [], [
            'password' => 'Password',
        ]);

        $user = auth()->user();

        if (Hash::check($request->password, $user->password)) {
            session(['password_verified' => true]);

            return redirect()->route('admin.users.create');
        }

        return back()->withErrors(['password' => 'Password is incorrect.']);
    }

    public function store(Request $request): RedirectResponse
    {
        // Only admin (user ID 1) can create new users
        if (auth()->id() !== 1 || ! session('password_verified')) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [], [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'password' => 'Password',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        session()->forget('password_verified');

        return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
    }
}
