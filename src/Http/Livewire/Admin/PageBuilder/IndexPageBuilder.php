<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;

class IndexPageBuilder extends Component
{
    public $titlePage = 'Page Builder';

public $pagesbuilder;

    public function mount()
    {
        $this->pagesbuilder = Page::whereNotNull('html')->get();
    }

    public function render()
    {

        return view('suryacms::livewire.admin.page-builder.index')->layout('suryacms::layouts.app');

    }
}
