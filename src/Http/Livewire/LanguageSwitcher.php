<?php

namespace Uiaciel\SuryaCms\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Language;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $languages = [];
    public $isMultilingual = false;

    public function mount(): void
    {
        $this->currentLocale = current_locale();
        $this->isMultilingual = is_multilingual();

        if ($this->isMultilingual) {
            $this->languages = $this->getAvailableLanguages();
        }
    }

    public function switchLanguage(string $langCode): void
    {
        if (!in_array($langCode, active_languages())) {
            return;
        }

        $this->currentLocale = $langCode;

        // Update session locale
        session()->put('locale', $langCode);

        // Redirect to the switched language URL
        $switchUrl = switch_lang_url($langCode);
        $this->dispatch('navigate', url: $switchUrl);

        // Redirect using JavaScript fallback
        $this->redirect($switchUrl, navigate: true);
    }

    /**
     * Get available active languages
     */
    protected function getAvailableLanguages(): array
    {
        return Cache::remember('available_languages_for_switcher', 3600, function () {
            return Language::where('status', 'Publish')
                ->select(['id', 'code', 'name'])
                ->orderBy('name')
                ->get()
                ->toArray();
        });
    }

    public function render()
    {
        return view('suryacms::livewire.language-switcher');
    }
}
