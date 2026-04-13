<?php

namespace Uiaciel\SuryaCms\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Uiaciel\SuryaCms\Models\Page;

class PageExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $page = null;

    public function __construct($page = null)
    {
        $this->page = $page;
    }

    public function query()
    {
        if ($this->page) {
            return Page::query()->where('id', $this->page->id);
        }
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
