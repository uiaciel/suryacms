<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Page;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Page;

class PageCreate extends Component
{
    use WithFileUploads;

    public $titlePage;

    public $languages;

    public $title;

    public $konten;

    public $pdf;

    public $datepublish;

    public $language_id;

    public $translation_id;

    public $status;

    public $pages;

    public function getTranslations($languageId)
    {
        return response()->json(Page::where('language_id', '!=', $languageId)->get());
    }

    public function mount()
    {
        $this->titlePage = 'New Page';
        $this->status = 'Publish';
        $this->datepublish = now()->format('Y-m-d');
        $this->language_id = 1;
        $this->translation_id = null;
        $this->languages = Language::all(); // Ensure Language model is imported
        $this->title = '';
        $this->konten = '';
        $this->pdf = '';
    }

    public function savePage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:pages,id',
            'pdf' => 'nullable|file|mimes:pdf|max:20480',
            'status' => 'required',
        ]);

        Page::create([
            'title' => $this->title,
            'content' => $this->konten,
            'pdf' => $this->pdf ? $this->pdf->store('pages', 'public') : null,
            'datepublish' => $this->datepublish,
            'language_id' => $this->language_id,
            'translation_id' => $this->translation_id ?: null,
            'status' => $this->status,
            'slug' => Str::slug($this->title),
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Page created Successfully!',
        ]);

        session()->flash('success', 'Page created successfully.');

        return $this->redirect('/admin/pages');
    }

    public function render()
    {

        return view('suryacms::livewire.admin.page.page-create')->layout('suryacms::layouts.app');
    }
}
