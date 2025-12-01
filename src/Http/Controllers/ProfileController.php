<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Uiaciel\SuryaCms\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{

    public function index()
    {
        $users = User::All();

        return view('suryacms::profile.users', [
            'users' => $users,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('suryacms::profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

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
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function create(Request $request)
    {
        // Cek apakah user ID 1 dan sudah verifikasi password
        if (auth()->id() !== 1) {
            abort(403, 'Akses ditolak.');
        }

        $verified = session('password_verified', false);
        return view('suryacms::livewire.admin.user.create', compact('verified'));
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = auth()->user();

        if (Hash::check($request->password, $user->password)) {
            session(['password_verified' => true]);
            return redirect()->route('admin.users.create');
        } else {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }
    }

    public function store(Request $request)
    {
        if (auth()->id() !== 1 || !session('password_verified')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        session()->forget('password_verified');

        return redirect()->route('admin.users.create')->with('success', 'User created successfully.');
    }
}
