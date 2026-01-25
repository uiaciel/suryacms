<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Page;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Uiaciel\SuryaCms\Exports\PageExport;
use Uiaciel\SuryaCms\Imports\PageImport;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Setting;

class PageIndex extends Component
{
    use WithFileUploads;

    public bool $showImportExportModal = false;

    public $pages;

    public $languages;

    public $setting;

    public $titlePage = 'All Pages';

    public $date;

    public $importFile;

    public function mount()
    {
        $this->setting = Setting::first();
        $this->languages = Language::All();
        $this->date = now()->format('d-m-Y');
    }

    public function openImportExportModal()
    {
        $this->showImportExportModal = true;
        $this->dispatch('show-ie-modal');
    }

    public function closeImportExportModal()
    {
        $this->showImportExportModal = false;
        $this->dispatch('hide-ie-modal');
    }

    public function dataImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        Excel::import(new PageImport, $this->importFile);

        session()->flash('success', 'Pages imported successfully!');
        $this->redirect(route('admin.page.index'), navigate: true);

    }

    public function dataExport()
    {
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);
        $fileName = 'backup-Pages-'.$sanitizedUrl.'-'.$this->date.'.xlsx';
        $this->closeImportExportModal();

        return Excel::download(new PageExport, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function render()
    {
        $query = Page::query()->orderBy('created_at', 'desc');

        // Filter Bahasa
        if (! empty($this->filterLanguage)) {
            $query->where('language_id', $this->filterLanguage);
        }

        // Filter Status
        if (! empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        // Filter Tanggal
        if (! empty($this->filterDate)) {
            // Kita asumsikan datepublish adalah kolom tipe DATE
            $query->whereDate('datepublish', $this->filterDate);
        }

        $this->pages = $query->get();

        return view('suryacms::livewire.admin.page.page-index', [
            'pages' => $this->pages,
        ])->layout('suryacms::layouts.app');
    }

    public function deletePage($id)
    {
        $page = Page::find($id);

        if ($page) {
            $page->delete();
            session()->flash('success', 'Page deleted successfully.');
        } else {
            session()->flash('error', 'Page not found.');
        }
    }
}
