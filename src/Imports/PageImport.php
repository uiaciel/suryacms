<?php

namespace Uiaciel\SuryaCms\Imports;

use Uiaciel\SuryaCms\Models\Page;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PageImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {

        $titleKey = 'title';
        $slugKey = 'slug';
        $userIdKey = 'user_id';

        if (empty($row[$titleKey]) || empty($row[$slugKey]) || empty($row[$userIdKey])) {
            return NULL;
        }

        return new Page([
            'language_id' => $row['language_id'] ?? 1,
            'translation_id' => $row['translation_id'] ?? NULL,
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

        ]);
    }
}
