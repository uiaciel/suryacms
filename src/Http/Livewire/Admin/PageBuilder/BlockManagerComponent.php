<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder;

use Livewire\Component;
use App\Models\CustomBlock;

class BlockManagerComponent extends Component
{
    public $name, $category, $html, $css;
    protected $listeners = ['saveBlockFromJs' => 'handleSaveBlock'];

    public function handleSaveBlock($data)
    {
        // Validasi data
        CustomBlock::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'html' => $data['html'],
            'css' => $data['css'],
            'settings' => ['type' => 'custom']
        ]);

        // Dispatch event untuk memberitahu UI bahwa blok baru telah disimpan
        $this->dispatch('blockSaved');
        session()->flash('message', 'Blok berhasil disimpan!');
    }

    public function exportBlock($id)
    {
        $block = CustomBlock::findOrFail($id);
        $jsonData = json_encode($block->only(['name', 'category', 'html', 'css']));

        return response()->streamDownload(function () use ($jsonData) {
            echo $jsonData;
        }, "block_{$block->name}.json");
    }

    public function importBlock($jsonData)
    {
        $data = json_decode($jsonData, true);
        CustomBlock::create($data);
        session()->flash('message', 'Blok berhasil diimpor!');
    }
}
