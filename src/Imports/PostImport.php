<?php

namespace Uiaciel\SuryaCms\Imports;

use Uiaciel\SuryaCms\Models\Post;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SebastianBergmann\Type\NullType;

class PostImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['title']) || empty($row['slug'])) {
            return NULL;
        }

        return new Post([
            'language_id' => $row['language_id'] ?? 1,
            'translation_id' => $row['translation_id'] ?? NULL,
            'user_id' => $row['user_id'],
            'category_id' => $row['category_id'],
            'title' => $row['title'],
            'slug' => $row['slug'],
            'content' => $row['content'],
            'datepublish' => $row['datepublish'],
            'tags' => $row['tags'],
            'source_url' => $row['source_url'],
            'source_favicon' => $row['source_favicon'],
            'source_title' => $row['source_title'],
            'feature' => $row['feature'],
            'flash' => $row['flash'],
            'view' => $row['view'],
            'status' => $row['status'],

        ]);
    }
}
