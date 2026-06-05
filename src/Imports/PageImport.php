<?php

namespace Uiaciel\SuryaCms\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Uiaciel\SuryaCms\Models\Page;

class PageImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        $titleKey = 'title';
        $slugKey = 'slug';
        $userIdKey = 'user_id';

        if (empty($row[$titleKey]) || empty($row[$slugKey]) || empty($row[$userIdKey])) {
            return null;
        }

        return new Page([
            'language_id' => $row['language_id'] ?? 1,
            'translation_id' => $row['translation_id'] ?? null,
            'user_id' => $row['user_id'],
            'title' => $row['title'],
            'slug' => $row['slug'],
            'content' => $row['content'],
            'pdf' => $row['pdf'],
            'datepublish' => $row['datepublish'],
            'view' => $row['view'],
            'status' => $row['status'],
            'html' => $row['html'],
            'css' => $row['css'],
            'is_builder' => $row['is_builder'] ?? false,

        ]);
    }
}
