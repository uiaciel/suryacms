<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Livewire\Component;

class Gallery extends Component
{
    public function render()
    {
        return view('suryacms::livewire.admin.gallery')->layout('suryacms::layouts.app');
    }
}
