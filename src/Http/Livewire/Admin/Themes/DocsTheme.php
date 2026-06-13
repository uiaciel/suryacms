<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Livewire\Component;

class DocsTheme extends Component
{
    public function render()
    {
        return view('suryacms::livewire.admin.themes.docs-theme')->layout('suryacms::layouts.app');

    }
}
