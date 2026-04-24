<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ProfileEdit extends Component
{
    public $titlePage = 'Profile Edit';

    public $name;

    public $email;

    public $password;

    public $current_password;

    public $password_confirmation;

    public $passwordVisible = false;

    public $currentPasswordVisible = false;

    public $passwordConfirmationVisible = false;

    public function mount()
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updatePassword()
    {

        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            session()->flash('error', 'Validation failed. Please check your input and try again.');
            throw $e;
        }

        $user = User::find(Auth::id());
        $user->password = Hash::make($validated['password']);
        $user->save();

        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('success', 'Password updated successfully.');
    }

    public function togglePasswordVisibility()
    {
        $this->passwordVisible = ! $this->passwordVisible;
    }

    public function toggleCurrentPasswordVisibility()
    {
        $this->currentPasswordVisible = ! $this->currentPasswordVisible;
    }

    public function togglePasswordConfirmationVisibility()
    {
        $this->passwordConfirmationVisible = ! $this->passwordConfirmationVisible;
    }

    public function updateProfile()
    {

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
        ]);

        $user = User::find(Auth::id());
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.user.profile-edit');
    }
}
