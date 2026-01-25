<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Setting;

class GenerateOfflinePage extends Command
{
    protected $signature = 'app:generate-offline';

    protected $description = 'Generate offline.html from the frontend homepage';

    public function handle()
    {
        $setting = Setting::first();
        $baseUrl = rtrim($setting->url ?? env('APP_URL', 'http://localhost'), '/');
        $multilanguage = $setting->is_multilingual === 'Yes';
        $urlsToFetch = [];

        // 2. Determine URLs and Language Codes to Fetch
        if ($multilanguage) {
            // MULTI-LANGUAGE: Fetch all published languages
            $languages = Language::where('status', 'Publish')->get();

            if ($languages->isEmpty()) {
                $this->error('Multilingual is enabled, but no published languages were found.');

                return 1;
            }

            $this->info('Multilingual mode is active. Preparing to fetch '.$languages->count().' language versions...');

            foreach ($languages as $lang) {
                // URL format: https://baseurl/lang_code
                $urlsToFetch[$lang->code] = $baseUrl.'/'.$lang->code;
            }
        } else {
            // SINGLE LANGUAGE: Fetch the first published language as the active one
            $defaultLang = Language::where('status', 'Publish')->first();

            if (! $defaultLang) {
                $this->error('Single language mode is active, but no published default language was found.');
                // Fallback to a hardcoded URL and code if nothing is defined
                $urlsToFetch['offline'] = $baseUrl.'/';
                $this->warn('Falling back to root URL and naming file "offline.html".');
            } else {
                // URL format: https://baseurl/
                $urlsToFetch[$defaultLang->code] = $baseUrl.'/';
                $this->info('Single language mode is active. Fetching content for: '.$defaultLang->code);
            }
        }

        // 3. Loop through URLs, Fetch Content, and Save Files
        $exitCode = 0;
        foreach ($urlsToFetch as $langCode => $url) {
            // Standardize language code to lowercase for filenames
            $langCode = strtolower($langCode);

            // Determine the final filename
            // Use 'offline.html' only if the fallback was triggered, otherwise use {code}.html
            $filename = ($langCode === 'offline') ? 'offline.html' : "$langCode.html";

            try {
                $this->info("Fetching $url and saving to $filename ...");

                // Use a short timeout for generation to prevent hanging
                $response = Http::timeout(30)->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // The offline notice HTML block
                    $offlineNotice = <<<'HTML'
<div style="position:fixed;bottom:0;width:100%;background:#fff3cd;color:#856404;text-align:center;padding:8px;z-index:9999;font-size:13px;border-top:1px solid #ffeeba;">
    ⚠️ Please wait, trying to retrieve the latest data from the server. <br>
    <strong>You are currently viewing a cached version. The content may be outdated.</strong>
</div>
</body>
HTML;

                    // Inject the notice just before the closing </body> tag
                    $html = str_replace('</body>', $offlineNotice, $html);

                    // Save the file to the public path
                    File::put(public_path($filename), $html);
                    $this->info("Successfully saved $filename ✅");
                } else {
                    $this->error("Failed to fetch $url. HTTP Status: ".$response->status());
                    $exitCode = 1;
                }
            } catch (\Exception $e) {
                $this->error("Error fetching $url: ".$e->getMessage());
                $exitCode = 1;
            }
        }

        return $exitCode;
    }
}
