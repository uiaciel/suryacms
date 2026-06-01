<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;

class SetupPackagesRepository extends Command
{
    protected $signature = 'suryacms:setup-packages';

    protected $description = 'Setup packages repository path for SuryaCMS modules';

    public function handle()
    {
        $composerFile = base_path('composer.json');

        if (!file_exists($composerFile)) {
            $this->error('composer.json tidak ditemukan.');
            return self::FAILURE;
        }

        $composer = json_decode(
            file_get_contents($composerFile),
            true
        );

        $repositoryExists = false;

        if (isset($composer['repositories'])) {

            foreach ($composer['repositories'] as $repository) {

                if (
                    ($repository['type'] ?? null) === 'path'
                    && ($repository['url'] ?? null) === 'packages/uiaciel/*'
                ) {
                    $repositoryExists = true;
                    break;
                }
            }
        }

        if (!$repositoryExists) {

            $composer['repositories'][] = [
                'type' => 'path',
                'url'  => 'packages/uiaciel/*',
            ];

            file_put_contents(
                $composerFile,
                json_encode(
                    $composer,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            $this->info(
                'Repository packages/uiaciel/* berhasil ditambahkan.'
            );
        } else {
            $this->info(
                'Repository packages/uiaciel/* sudah tersedia.'
            );
        }

        if (!is_dir(base_path('packages/uiaciel'))) {

            mkdir(
                base_path('packages/uiaciel'),
                0755,
                true
            );

            $this->info(
                'Folder packages/uiaciel berhasil dibuat.'
            );
        }

        $this->info('Setup selesai.');

        return self::SUCCESS;
    }
}
