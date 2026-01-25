<?php

namespace Uiaciel\SuryaCms\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Uiaciel\SuryaCms\Models\Post;

class PostExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Post::query();
    }

    public function headings(): array
    {
        return [
            'language_id',
            'translation_id',
            'user_id',
            'category_id',
            'title',
            'slug',
            'content',
            'datepublish',
            'tags',
            'source_url',
            'source_favicon',
            'source_title',
            'feature',
            'flash',
            'view',
            'status',
        ];
    }

    public function map($posts): array
    {
        return [
            $posts->language_id,
            $posts->translation_id,
            $posts->user_id,
            $posts->category_id,
            $posts->title,
            $posts->slug,
            $posts->content,
            $posts->datepublish,
            $posts->tags,
            $posts->source_url,
            $posts->source_favicon,
            $posts->source_title,
            $posts->feature,
            $posts->flash,
            $posts->view,
            $posts->status,

        ];
    }
}
