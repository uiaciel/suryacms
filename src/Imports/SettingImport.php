<?php

namespace Uiaciel\SuryaCms\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Uiaciel\SuryaCms\Models\Setting;

class SettingImport implements ToModel, WithHeadingRow
{
    protected $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function model(array $row)
    {
        // Karena ini adalah data tunggal, kita akan mengisi (fill) dan menyimpan (save)
        // model yang sudah ada, bukan membuat yang baru.
        $this->setting->fill([
            'sitename' => $row['sitename'],
            'tagline' => $row['tagline'],
            'description' => $row['description'],
            'keywords' => $row['keywords'],
            'sitename_translation' => $row['sitename_translation'],
            'tagline_translation' => $row['tagline_translation'],
            'description_translation' => $row['description_translation'],
            'keywords_translation' => $row['keywords_translation'],
            'googlesiteverification' => $row['googlesiteverification'],
            'address' => $row['address'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'whatsapp' => $row['whatsapp'],
            'facebook' => $row['facebook'],
            'twitter' => $row['twitter'],
            'instagram' => $row['instagram'],
            'linkedin' => $row['linkedin'],
            'youtube' => $row['youtube'],
            'tiktok' => $row['tiktok'],
            'url' => $row['url'],
            'logo' => $row['logo'],
            'favicon' => $row['favicon'],
            'images' => $row['images'],
            'active_theme' => $row['active_theme'],
            'google_analytics' => $row['google_analytics'],
            'google_adsense' => $row['google_adsense'],
            'language' => $row['language'],
            'is_multilingual' => $row['is_multilingual'],
            'code' => $row['code'],
            'color_primary' => $row['color_primary'],
            'color_secondary' => $row['color_secondary'],
            'color_success' => $row['color_success'],
            'color_danger' => $row['color_danger'],
            'color_warning' => $row['color_warning'],
            'color_info' => $row['color_info'],
            'color_light' => $row['color_light'],
            'color_dark' => $row['color_dark'],
            'site_maintenance' => $row['site_maintenance'],
            'email_forwarder' => $row['email_forwarder'],
            'date_format' => $row['date_format'],

        ])->save();

        // Kita return null karena kita tidak membuat model baru per baris.
        return null;
    }
}
