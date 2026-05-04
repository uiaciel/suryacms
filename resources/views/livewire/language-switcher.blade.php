<div class="language-switcher">
    @if($isMultilingual && !empty($languages))
        <div class="flex items-center gap-2">
            @foreach($languages as $language)
                <button
                    wire:click="switchLanguage('{{ $language['code'] }}')"
                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200
                        {{ $currentLocale === $language['code']
                            ? 'bg-primary text-white'
                            : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}"
                    title="Switch to {{ $language['name'] }}"
                    wire:loading.attr="disabled"
                >
                    <span class="uppercase">{{ strtoupper($language['code']) }}</span>
                </button>
            @endforeach
        </div>
    @endif
</div>

<style>
    .language-switcher {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .language-switcher button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .language-switcher button[wire\:loading\.attr] {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
