<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Contact as InboxContactForm;
use Uiaciel\SuryaCms\Models\Gallery;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;

class SearchResult extends Component
{
    public $query;

    public $results = [
        'posts' => [],
        'pages' => [],
        'galleries' => [],
        'inbox_contact_forms' => [],
    ];

    public function mount()
    {
        $this->query = request()->get('query');

        if (! empty($this->query)) {
            $this->performSearch();
        }
    }

    public function performSearch()
    {
        $searchTerm = '%'.$this->query.'%';

        $this->results['posts'] = Post::where('title', 'like', $searchTerm)->get();
        $this->results['pages'] = Page::where('title', 'like', $searchTerm)->get();
        $this->results['galleries'] = Gallery::where('name', 'like', $searchTerm)->get();
        $this->results['inbox_contact_forms'] = InboxContactForm::where('name', 'like', $searchTerm)
            ->orWhere('email', 'like', $searchTerm)
            ->orWhere('subject', 'like', $searchTerm)
            ->get();
    }

    public function render()
    {
        return view('suryacms::livewire.admin.search-result')->layout('suryacms::layouts.app');
    }
}
