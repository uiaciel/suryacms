<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use ReflectionFunction;

class HelperLists extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'suryacms:helper';

    /**
     * The console command description.
     */
    protected $description = 'Display available SuryaCMS helper functions';

    public function handle(): int
    {
        $helperFile =  dirname(__DIR__, 2) . '/helpers.php';

        if (! file_exists($helperFile)) {
            $this->error('helpers.php not found.');
            return self::FAILURE;
        }

        $content = file_get_contents($helperFile);

        preg_match_all(
            '/\/\*\*(.*?)\*\/\s*function\s+([a-zA-Z0-9_]+)\s*\(/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $this->info('Available SuryaCMS Helpers');
        $this->newLine();

        foreach ($matches as $match) {

            $description = collect(explode("\n", $match[1]))
                ->map(fn ($line) => trim($line, " *"))
                ->reject(fn ($line) => $line === '' || str_starts_with($line, '@'))
                ->first();

            $helper = $match[2];

            $this->line(sprintf(
                " %-30s %s",
                $helper . '()',
                $description ?: '-'
            ));
        }

        return self::SUCCESS;
    }
}