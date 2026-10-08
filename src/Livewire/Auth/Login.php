<?php

namespace Uiaciel\SuryaCms\Livewire\Auth;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Setting;

class Login extends Component
{
    public $email;

    public $password;

    public $remember = false;

    public function render()
    {
        $setting = Setting::first();

        // Merender view dari namespace 'suryacms' atau 'auth'
        return view('suryacms::livewire.auth.login', compact('setting'))
            ->layout('suryacms::layouts.guest'); // Menggunakan layout guest kustom SuryaCMS
    }
}
