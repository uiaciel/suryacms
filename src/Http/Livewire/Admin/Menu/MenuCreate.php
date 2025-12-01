<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Menu;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Menu;
use Uiaciel\SuryaCms\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use Uiaciel\SuryaCms\Exports\MenuExport;
use Uiaciel\SuryaCms\Imports\MenuImport;
use Uiaciel\SuryaCms\Models\Language;
use Livewire\WithFileUploads;

class MenuCreate extends Component
{
    use WithFileUploads;
    public $titlePage = 'Menu Management';
    public $categoriesmenu = ['Primary', 'Secondary'];
    public $name;
    public $date;
    public $order;
    public $type;
    public $link;
    public $category;
    public $parent_id;
    public $menux;
    public $language;
    public $setting;
    public $importFile;

    public function dataImport()
    {
        if (Menu::count() > 0) {
            $this->dataExport();
        }

        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        Menu::query()->truncate();

        Excel::import(new MenuImport, $this->importFile);

        session()->flash('success', 'Menus imported successfully!');
        $this->redirect(route('admin.menu.create'), navigate: true);
    }

    public function dataExport()
    {
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);
        $fileName = 'backup-Menus-' . $sanitizedUrl . '-' . $this->date . '.xlsx';

        return Excel::download(new MenuExport, $fileName,  \Maatwebsite\Excel\Excel::XLSX);
    }

    public function mount()
    {
        $this->date = now()->format('d-m-Y');
        $this->language = Language::All();
        $this->setting = Setting::first();
        $this->menux = Menu::whereNull('parent_id')->with('children')->get();
        $categories = Menu::distinct()->pluck('category')->filter()->values()->toArray();
        $this->categoriesmenu = array_values(array_unique(array_merge(['Primary', 'Secondary'], $categories)));
    }

    public function storeMenu()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'link' => $this->type === 'custom' || $this->type === 'post' || $this->type === 'page' || $this->type === 'categoryPost'
                ? 'required|string|max:255'
                : 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:menus,id',
        ]);

        Menu::create([
            'name' => $this->name,
            'type' => $this->type,
            'order' => $this->order ?? (Menu::max('order') + 1),
            'link' => $this->link,
            'category' => $this->category,
            'parent_id' => $this->parent_id ?: null,
        ]);

        $this->dispatch('menu-created');

        session()->flash('message', 'Menu created successfully.');
        $this->reset(['name', 'type', 'link', 'category', 'parent_id']);

        return $this->redirect('/admin/menu');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.menu.menu-create')->layout('suryacms::layouts.app');
    }
}
