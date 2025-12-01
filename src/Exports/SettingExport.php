<?php

namespace Uiaciel\SuryaCms\Exports;

use Uiaciel\SuryaCms\Models\Setting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class SettingExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Setting::query();
    }

    public function headings(): array
    {
        return [
            'sitename',
            'tagline',
            'description',
            'keywords',
            'sitename_translation',
            'tagline_translation',
            'description_translation',
            'keywords_translation',
            'googlesiteverification',
            'address',
            'email',
            'phone',
            'whatsapp',
            'facebook',
            'twitter',
            'instagram',
            'linkedin',
            'youtube',
            'tiktok',
            'url',
            'logo',
            'favicon',
            'images',
            'active_theme',
            'google_analytics',
            'google_adsense',
            'language',
            'code',
            'color_primary',
            'color_secondary',
            'color_success',
            'color_danger',
            'color_warning',
            'color_info',
            'color_light',
            'color_dark',
            'site_maintenance',
            'email_forwarder',
            'date_format'

        ];
    }

    public function map($settings): array
    {
        return [

            $settings->sitename,
            $settings->tagline,
            $settings->description,
            $settings->keywords,
            $settings->sitename_translation,
            $settings->tagline_translation,
            $settings->description_translation,
            $settings->keywords_translation,
            $settings->googlesiteverification,
            $settings->address,
            $settings->email,
            $settings->phone,
            $settings->whatsapp,
            $settings->facebook,
            $settings->twitter,
            $settings->instagram,
            $settings->linkedin,
            $settings->youtube,
            $settings->tiktok,
            $settings->url,
            $settings->logo,
            $settings->favicon,
            $settings->images,
            $settings->themes,
            $settings->google_analytics,
            $settings->google_adsense,
            $settings->language,
            $settings->code,
            $settings->color_primary,
            $settings->color_secondary,
            $settings->color_success,
            $settings->color_danger,
            $settings->color_warning,
            $settings->color_info,
            $settings->color_light,
            $settings->color_dark,
            $settings->site_maintenance,
            $settings->email_forwarder,
            $settings->date_format,
        ];
    }
}
