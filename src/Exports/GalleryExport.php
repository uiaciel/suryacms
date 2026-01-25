<?php

namespace Uiaciel\SuryaCms\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Uiaciel\SuryaCms\Models\Gallery;

class GalleryExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Gallery::query();
    }

    public function headings(): array
    {
        return [
            'name',
            'description',
            'image_path',
            'category',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function map($gallery): array
    {
        return [

            $gallery->name,
            $gallery->description,
            $gallery->image_path,
            $gallery->category,
            $gallery->status,
            $gallery->created_at,
            $gallery->updated_at,
        ];
    }
}
