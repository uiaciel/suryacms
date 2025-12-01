<?php

namespace Uiaciel\SuryaCms\Exports;

use Uiaciel\SuryaCms\Models\Page;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PageExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Page::query();
    }

    public function headings(): array
    {
        return [
            'language_id',
            'translation_id',
            'user_id',
            'title',
            'slug',
            'content',
            'pdf',
            'datepublish',
            'view',
            'status',
            'html',
            'css',
        ];
    }

    public function map($pages): array
    {
        return [
            $pages->language_id,
            $pages->translation_id,
            $pages->user_id,
            $pages->title,
            $pages->slug,
            $pages->content,
            $pages->pdf,
            $pages->datepublish,
            $pages->view,
            $pages->status,
            $pages->html,
            $pages->css,
        ];
    }
}
