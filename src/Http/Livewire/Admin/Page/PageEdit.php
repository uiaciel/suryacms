<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Page;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Page;

class PageEdit extends Component
{
    use WithFileUploads;

    public $pageId;

    public $titlePage;

    public $languages;

    public $title;

    public $konten;

    public $datepublish;

    public $language_id;

    public $status;

    public $pages;

    public $translation_id;

    public $pdf;

    public function mount()
    {
        $this->pageId = request()->id;
        $page = Page::find($this->pageId);

        if ($page) {
            $this->titlePage = 'Edit Page';
            $this->title = $page->title;
            $this->konten = $page->content;
            $this->datepublish = $page->datepublish;
            $this->language_id = $page->language_id;
            $this->translation_id = $page->translation_id;
            $this->pdf = $page->pdf;
            $this->status = $page->status;
            $this->languages = Language::all();
        } else {
            abort(404, 'Page not found');
        }
    }

    public function updatePage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:pages,id',
            'pdf' => 'nullable|file|mimes:pdf|max:102400',
            'status' => 'required',
        ]);
        $page = Page::find($this->pageId);

        if ($page) {
            $page->update([
                'title' => $this->title,
                'content' => $this->konten,
                'pdf' => $this->pdf ? $this->pdf->store('pages', 'public') : $page->pdf,
                'datepublish' => $this->datepublish,
                'language_id' => $this->language_id,
                'translation_id' => $this->translation_id ?: null,
                'status' => $this->status,
                'slug' => Str::slug($this->title),
                'user_id' => Auth::id(),
            ]);
        } else {
            abort(404, 'Page not found');
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Page updated Successfully!',
        ]);

        session()->flash('success', 'Page updated successfully.');

        return $this->redirect('/admin/pages');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.page.page-edit')->layout('suryacms::layouts.app');
    }
}
