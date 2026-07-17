<?php

namespace Uiaciel\SuryaCms\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Uiaciel\SuryaCms\Models\Gallery;

class GalleryImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
        Gallery::query()->delete();
    }

    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        return new Gallery([
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'image_path' => $row['image_path'] ?? null,
            'is_tinymce_upload' => $row['is_tinymce_upload'] ?? false,
            'category' => $row['category'] ?? null,
            'status' => $row['status'] ?? 'Draft',
        ]);
    }
}
