<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Post;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // Import WithPagination trait
use Maatwebsite\Excel\Facades\Excel; // Import Session facade
use Uiaciel\SuryaCms\Exports\PostExport;
use Uiaciel\SuryaCms\Imports\PostImport;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;

class PostIndex extends Component
{
    use WithPagination; // Use the trait for pagination

    protected $paginationTheme = 'bootstrap';

    use WithFileUploads;

    public $titlePage = 'All Posts';

    public $categories;

    public $setting; // Added to handle multilingual setting

    public $date;

    // Properties for filters
    public $searchFilter = '';

    public $categoryFilter = '';

    public $statusFilter = '';

    public $importFile;

    // Query string configuration to persist filters in URL
    protected $queryString = [
        'searchFilter' => ['except' => '', 'as' => 'searchFilter'],
        'categoryFilter' => ['except' => '', 'as' => 'categoryFilter'],
        'statusFilter' => ['except' => '', 'as' => 'statusFilter'],
    ];

    // Listeners for SweetAlert and other dispatches
    protected $listeners = ['deletePostConfirmed'];

    public function mount()
    {
        $this->categories = Category::all();
        $this->setting = Setting::first();
        $this->date = now()->format('d-m-Y');
    }

    // Method to reset pagination when a filter changes
    public function updating($name, $value)
    {
        if (in_array($name, ['searchFilter', 'categoryFilter', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    // Method to reset all filters
    public function resetFilters()
    {
        $this->reset(['searchFilter', 'categoryFilter', 'statusFilter']);
        $this->resetPage(); // Reset pagination after clearing filters
    }

    // This method is called from the Blade via @click on the delete button
    // It dispatches an event to show the confirmation modal
    public function deletePost($id)
    {
        $post = Post::find($id);
        if ($post) {
            $this->dispatch('show-delete-confirmation', [
                'postId' => $post->id,
                'postTitle' => $post->title,
            ]);
        } else {
            Session::flash('error', 'Post not found.');
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Post not found!']);
        }
    }

    // This method is called when the delete confirmation is confirmed from the modal
    public function deletePostConfirmed($postId)
    {
        try {
            $post = Post::findOrFail($postId); // Use findOrFail for stricter checking
            $post->delete();
            Session::flash('success', 'Post deleted successfully.');
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Deleted!', 'text' => 'Post deleted Successfully!']);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to delete post: '.$e->getMessage());
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Failed to delete post!']);
        }

        // Hide the confirmation modal after deletion attempt
        $this->dispatch('hide-delete-confirmation');
    }

    public function dataImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        Excel::import(new PostImport, $this->importFile);

        return $this->redirect('/admin/posts/');

    }

    public function dataExport()
    {
        session()->flash('message', 'Post Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new PostExport, 'backup-Posts-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function render()
    {
        $query = Post::query();

        // Apply search filter
        if ($this->searchFilter) {
            $query->where('title', 'like', '%'.$this->searchFilter.'%');
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->whereHas('category', function ($q) {
                $q->where('name', $this->categoryFilter);
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('suryacms::livewire.admin.post.post-index', [
            'posts' => Post::with('category')->OrderBy('created_at', 'desc')->get(),
            'categories' => Category::all(),

            'titlePage' => 'Post List',
        ])->layout('suryacms::layouts.app');
    }
}
