<?php

namespace Uiaciel\SuryaCms\Imports;

use Uiaciel\SuryaCms\Models\Menu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MenuImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
          Menu::query()->delete();
    }

    public function model(array $row)
    {

        if (empty($row['name'])) {
            return null;
        }

        return new Menu([
            'name'      => $row['name'],
            'category'  => $row['category'] ?? null,
            'type'      => $row['type'] ?? 'custom',
            'link'      => $row['link'] ?? '#',
            'parent_id' => $row['parent_id'] ?? null,
            'order'     => $row['order'] ?? 0,
        ]);
    }

}
