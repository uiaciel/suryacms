<?php

namespace Uiaciel\SuryaCms\Exports;

use Uiaciel\SuryaCms\Models\Menu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MenuExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Menu::select('name', 'category', 'type', 'link', 'parent_id', 'order')->get();
    }

    /**
     * @return array
     */
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
