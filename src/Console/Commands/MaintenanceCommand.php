<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Uiaciel\SuryaCms\Models\Setting;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'suryacms:maintenance
                            {--status : Show maintenance status}
                            {--check : Check maintenance configuration}
                            {--on : Enable maintenance mode}
                            {--off : Disable maintenance mode}';

    /**
     * The console command description.
     */
    protected $description = 'Manage SuryaCMS Maintenance Mode';

    public function handle(): int
    {
        $setting = Setting::first();

        if (! $setting) {
            $this->error('Settings record not found.');

            return self::FAILURE;
        }

        if ($this->option('status')) {
            return $this->showStatus($setting);
        }

        if ($this->option('on')) {
            return $this->enableMaintenance($setting);
        }

        if ($this->option('off')) {
            return $this->disableMaintenance($setting);
        }

        if ($this->option('check')) {
            return $this->checkMaintenance($setting);
        }

        $this->info('Usage:');
        $this->line('  php artisan suryacms:maintenance --status');
        $this->line('  php artisan suryacms:maintenance --check');
        $this->line('  php artisan suryacms:maintenance --on');
        $this->line('  php artisan suryacms:maintenance --off');

        return self::SUCCESS;
    }

    protected function showStatus(Setting $setting): int
    {
        $this->newLine();

        $this->info('SuryaCMS Maintenance');
        $this->line(str_repeat('-', 40));

        $this->line('Status     : '.($setting->site_maintenance ? 'ON' : 'OFF'));
        $this->line('Theme      : '.($setting->active_theme ?? 'default'));
        $this->line('HTTP Code  : 503');

        return self::SUCCESS;
    }

    protected function enableMaintenance(Setting $setting): int
    {
        if ($setting->site_maintenance) {
            $this->warn('Maintenance mode is already ON.');

            return self::SUCCESS;
        }

        $setting->site_maintenance = true;
        $setting->save();

        $this->info('✔ Maintenance mode has been enabled.');

        return self::SUCCESS;
    }

    protected function disableMaintenance(Setting $setting): int
    {
        if (! $setting->site_maintenance) {
            $this->warn('Maintenance mode is already OFF.');

            return self::SUCCESS;
        }

        $setting->site_maintenance = false;
        $setting->save();

        $this->info('✔ Maintenance mode has been disabled.');

        return self::SUCCESS;
    }

    protected function checkMaintenance(Setting $setting): int
    {
        $theme = $setting->active_theme ?? 'default';

        $blade = resource_path(
            "views/frontend/{$theme}/page/maintenance.blade.php"
        );

        $this->newLine();

        $this->info('SuryaCMS Maintenance Checker');
        $this->line(str_repeat('─', 60));

        $this->newLine();
        $this->comment('Settings');
        $this->line(str_repeat('─', 60));

        $this->check('Settings record', $setting !== null);
        $this->check('site_maintenance', isset($setting->site_maintenance));
        $this->check('Active theme', ! empty($theme), $theme);

        $this->newLine();
        $this->comment('Theme');
        $this->line(str_repeat('─', 60));

        $this->check(
            'Maintenance Blade',
            File::exists($blade),
            "frontend/{$theme}/page/maintenance.blade.php"
        );

        $this->newLine();
        $this->comment('Routes');
        $this->line(str_repeat('─', 60));

        $this->checkRoute('/login');
        $this->checkRoute('/admin');

        $this->newLine();
        $this->comment('Configuration');
        $this->line(str_repeat('─', 60));

        $this->check(
            'Session Driver',
            true,
            config('session.driver')
        );

        $this->check(
            'Default Guard',
            true,
            config('auth.defaults.guard')
        );

        $this->newLine();

        if (File::exists($blade)) {
            $this->info('✔ Overall Status : READY');
        } else {
            $this->error('✖ Overall Status : FAILED');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function check(string $label, bool $passed, ?string $value = null): void
    {
        $icon = $passed ? '✔' : '✖';

        if ($value) {
            $this->line(sprintf(
                '%s %-22s : %s',
                $icon,
                $label,
                $value
            ));
        } else {
            $this->line(sprintf(
                '%s %s',
                $icon,
                $label
            ));
        }
    }

    protected function checkRoute(string $uri): void
    {
        $exists = collect(Route::getRoutes())
            ->contains(fn ($route) => '/'.ltrim($route->uri(), '/') === $uri);

        $this->check("Route {$uri}", $exists);
    }
}
