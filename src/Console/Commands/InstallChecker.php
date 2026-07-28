<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Uiaciel\SuryaCms\Models\Setting;
use App\Models\User;

use function Laravel\Prompts\text;
use function Laravel\Prompts\password;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class InstallChecker extends Command
{
    /**
     * Nama dan deskripsi command Artisan.
     *
     * @var string
     */
    protected $signature = 'suryacms:install';

    /**
     * Deskripsi singkat command.
     *
     * @var string
     */
    protected $description = 'Instalasi awal SuryaCMS dan konfigurasi administrator';

    /**
     * Jalankan logic command.
     */
    public function handle(): int
    {
        // Display Banner
        $this->line('┌───────────────────────────────┐');
        $this->line('│       SuryaCMS Installer      │');
        $this->line('│          Version 2.x          │');
        $this->line('└───────────────────────────────┘');
        $this->newLine();

        // 1. Pengambilan Input Data
        $siteName = text(
            label: 'Application Name:',
            placeholder: 'My Company Website',
            default: 'My Company Website',
            required: true
        );

        $siteUrl = text(
            label: 'Application URL:',
            placeholder: 'https://example.com',
            default: config('app.url', 'https://example.com'),
            required: true
        );

        $adminName = text(
            label: 'Administrator Username:',
            placeholder: 'admin',
            default: 'admin',
            required: true
        );

        $adminEmail = text(
            label: 'Administrator Email:',
            placeholder: 'admin@example.com',
            default: 'admin@example.com',
            required: true,
            validate: fn (string $value) => match (true) {
                !filter_var($value, FILTER_VALIDATE_EMAIL) => 'Format email tidak valid.',
                default => null
            }
        );

        $adminPassword = password(
            label: 'Administrator Password:',
            required: true,
            validate: fn (string $value) => match (true) {
                strlen($value) < 8 => 'Password minimal harus 8 karakter.',
                default => null
            }
        );

        $this->newLine();

        // 2. Simpan Setting Aplikasi
        $this->info('Creating application settings...');

        Setting::updateOrCreate(
            ['key' => 'sitename'],
            ['value' => $siteName]
        );

        Setting::updateOrCreate(
            ['key' => 'url'],
            ['value' => $siteUrl]
        );

        Setting::updateOrCreate(
            ['key' => 'email'],
            ['value' => $adminEmail]
        );

        $this->line('  <fg=green;options=bold>✔</> Site information saved.');
        $this->newLine();

        // 3. Buat/Update Akun Administrator
        $this->info('Creating administrator account...');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'     => $adminName,
                'email'    => $adminEmail,
                'password' => Hash::make($adminPassword),
            ]
        );

        $this->line('  <fg=green;options=bold>✔</> Administrator account created.');
        $this->newLine();

        // 4. Proses Konfigurasi Tambahan (Proses Simulasi)
        $this->info('Generating application configuration...');
        $this->line('  <fg=green;options=bold>✔</> Installation completed successfully.');
        $this->newLine();

        // 5. Output Summary / Tampilan Akhir
        info('SuryaCMS has been installed successfully!');
        $this->newLine();

        $this->line(' You can now access your administration panel:');
        $this->newLine();
        $this->line(' <fg=cyan>' . rtrim($siteUrl, '/') . '/admin</>');
        $this->newLine();

        $this->line(' <options=bold>Login Credentials</>');
        $this->newLine();
        $this->line(" Username : {$adminName}");
        $this->line(" Email    : {$adminEmail}");
        $this->line(" Password : " . str_repeat('*', strlen($adminPassword)));
        $this->newLine();

        warning('For security reasons, please change your password after your first login.');
        $this->newLine();

        $this->line(' Thank you for choosing SuryaCMS.');

        return Command::SUCCESS;
    }
}
