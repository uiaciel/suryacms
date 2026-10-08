<?php

namespace Uiaciel\SuryaCms\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Uiaciel\SuryaCms\Models\Menu;

class MenuExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Menu::select('name', 'category', 'type', 'link', 'parent_id', 'order')->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'category',
            'type',
            'link',
            'parent_id',
            'order',
        ];
    }
}
