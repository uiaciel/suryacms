<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Setting;

class FileCheck extends Component
{
    public array $files = [];

    public array $serverInfo = [];

    public string $active_theme = 'default';

    public function mount(): void
    {
        $setting = Setting::find(1);
        $this->active_theme = $setting->active_theme ?? 'default';

        $this->files = [
            [
                'label' => 'EN Page (en.html)',
                'path' => public_path('en.html'),
            ],
            [
                'label' => 'ID Page (id.html)',
                'path' => public_path('id.html'),
            ],
            [
                'label' => 'Sitemap (sitemap.xml)',
                'path' => public_path('sitemap.xml'),
            ],
            [
                'label' => 'Robots.txt',
                'path' => public_path('robots.txt'),
            ],
            [
                'label' => 'Theme Config (theme.php)',
                'path' => base_path('resources/views/frontend/'.$this->active_theme.'/theme.php'),
            ],
            [
                'label' => 'Storage Link (public/storage)',
                'path' => public_path('storage'),
            ],
            [
                'label' => 'Log File (laravel.log)',
                'path' => storage_path('logs/laravel.log'),
            ],
            [
                'label' => 'Env File (.env)',
                'path' => base_path('.env'),
            ],
        ];

        // ✅ PENTING: isi serverInfo saat mount
        $this->serverInfo = $this->getServerInfo();
    }

    /* ============================================================
     |  SERVER INFO AGGREGATOR
     ============================================================ */

    public function getServerInfo(): array
    {
        return [
            'php' => $this->getPhpInfo(),
            'server' => $this->getWebServerInfo(),
            'image_support' => $this->getImageSupportInfo(),
            'storage' => $this->getStorageInfo(),
            'database' => $this->getDatabaseInfo(),
            'laravel' => $this->getLaravelInfo(),
            'extensions' => $this->getPhpExtensions(),
        ];
    }

    /* ============================================================
     |  PHP INFO
     ============================================================ */

    private function getPhpInfo(): array
    {
        return [
            'version' => PHP_VERSION,
            'version_id' => PHP_VERSION_ID,
            'sapi' => php_sapi_name(),
            'os' => PHP_OS,
            'os_family' => PHP_OS_FAMILY,
            'architecture' => php_uname('m'),
            'int_size' => PHP_INT_SIZE * 8 .' bits',
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).' MB',
            'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2).' MB',
            'max_execution_time' => ini_get('max_execution_time').' s',
            'max_input_time' => ini_get('max_input_time').' s',
            'max_input_vars' => ini_get('max_input_vars'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'timezone' => date_default_timezone_get(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'display_errors' => ini_get('display_errors') ? 'On' : 'Off',
            'error_reporting' => ini_get('error_reporting'),
            'opcache_enabled' => $this->isOpcacheEnabled() ? 'Enabled' : 'Disabled',
        ];
    }

    private function isOpcacheEnabled(): bool
    {
        if (! function_exists('opcache_get_status')) {
            return false;
        }
        $status = @opcache_get_status(false);

        return ! empty($status['opcache_enabled']);
    }

    /* ============================================================
     |  WEB SERVER INFO
     ============================================================ */

    private function getWebServerInfo(): array
    {
        return [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
            'hostname' => gethostname() ?: 'Unknown',
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'https' => (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'Yes' : 'No',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'user' => $this->getServerUser(),
        ];
    }

    private function getServerUser(): string
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $info = posix_getpwuid(posix_geteuid());

            return $info['name'] ?? 'Unknown';
        }

        return get_current_user() ?: 'Unknown';
    }

    /* ============================================================
     |  IMAGE SUPPORT (GD / Imagick / WebP)
     ============================================================ */

    private function getImageSupportInfo(): array
    {
        $gdInfo = function_exists('gd_info') ? gd_info() : [];
        $hasImagick = extension_loaded('imagick');

        $imagickFormats = [];
        if ($hasImagick) {
            try {
                $imagick = new \Imagick;
                $imagickFormats = $imagick->queryFormats();
            } catch (\Exception $e) {
                $imagickFormats = [];
            }
        }

        return [
            'gd' => [
                'loaded' => extension_loaded('gd'),
                'version' => $gdInfo['GD Version'] ?? 'N/A',
                'webp_support' => ! empty($gdInfo['WebP Support']),
                'jpeg_support' => ! empty($gdInfo['JPEG Support']),
                'png_support' => ! empty($gdInfo['PNG Support']),
                'gif_read' => ! empty($gdInfo['GIF Read Support']),
                'gif_create' => ! empty($gdInfo['GIF Create Support']),
                'avif_support' => ! empty($gdInfo['AVIF Support']),
                'bmp_support' => ! empty($gdInfo['BMP Support']),
                'free_type' => ! empty($gdInfo['FreeType Support']),
            ],
            'imagick' => [
                'loaded' => $hasImagick,
                'version' => $hasImagick
                    ? (string) (\Imagick::getVersion()['versionString'] ?? 'N/A')
                    : 'N/A',
                'webp' => in_array('WEBP', $imagickFormats),
                'jpeg' => in_array('JPEG', $imagickFormats),
                'png' => in_array('PNG', $imagickFormats),
                'gif' => in_array('GIF', $imagickFormats),
                'avif' => in_array('AVIF', $imagickFormats),
                'pdf' => in_array('PDF', $imagickFormats),
            ],
            'functions' => [
                'imagewebp' => function_exists('imagewebp'),
                'imagecreatefromwebp' => function_exists('imagecreatefromwebp'),
                'imagecreatefromjpeg' => function_exists('imagecreatefromjpeg'),
                'imagecreatefrompng' => function_exists('imagecreatefrompng'),
                'imagecreatefromgif' => function_exists('imagecreatefromgif'),
                'imagepalettetotruecolor' => function_exists('imagepalettetotruecolor'),
                'imagettftext' => function_exists('imagettftext'),
            ],
        ];
    }

    /* ============================================================
     |  STORAGE INFO
     ============================================================ */

    private function getStorageInfo(): array
    {
        $publicPath = Storage::disk('public')->path('');
        $galleriesPath = Storage::disk('public')->path('galleries');
        $publicLinkPath = public_path('storage');

        $basePath = base_path();
        $freeSpace = @disk_free_space($basePath);
        $totalSpace = @disk_total_space($basePath);

        return [
            'public_disk_path' => $publicPath,
            'public_writable' => is_dir($publicPath) && is_writable($publicPath),
            'galleries_writable' => is_dir($galleriesPath) && is_writable($galleriesPath),
            'storage_link_exists' => file_exists($publicLinkPath),
            'storage_link_target' => is_link($publicLinkPath) ? readlink($publicLinkPath) : null,
            'disk_free' => $freeSpace ? $this->formatBytes($freeSpace) : 'N/A',
            'disk_total' => $totalSpace ? $this->formatBytes($totalSpace) : 'N/A',
            'disk_used_percent' => ($freeSpace && $totalSpace)
                ? round((1 - $freeSpace / $totalSpace) * 100, 1).'%'
                : 'N/A',
        ];
    }

    /* ============================================================
     |  DATABASE INFO
     ============================================================ */

    private function getDatabaseInfo(): array
    {
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            return [
                'driver' => $driver,
                'version' => $connection->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'database' => $connection->getDatabaseName(),
                'host' => config('database.connections.'.config('database.default').'.host', 'N/A'),
                'connected' => true,
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('database.default'),
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /* ============================================================
     |  LARAVEL INFO
     ============================================================ */

    private function getLaravelInfo(): array
    {
        return [
            'version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'On' : 'Off',
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'filesystem_driver' => config('filesystems.default'),
        ];
    }

    /* ============================================================
     |  PHP EXTENSIONS
     ============================================================ */

    private function getPhpExtensions(): array
    {
        $required = [
            'gd' => 'Image manipulation (WebP conversion)',
            'imagick' => 'Advanced image manipulation (alternative)',
            'fileinfo' => 'File MIME type detection',
            'mbstring' => 'Multibyte string handling',
            'openssl' => 'Encryption & HTTPS',
            'pdo' => 'Database abstraction',
            'pdo_mysql' => 'MySQL database driver',
            'pdo_sqlite' => 'SQLite database driver',
            'pdo_pgsql' => 'PostgreSQL database driver',
            'curl' => 'HTTP requests',
            'json' => 'JSON encoding/decoding',
            'zip' => 'Archive handling',
            'xml' => 'XML parsing',
            'bcmath' => 'Arbitrary precision math',
            'ctype' => 'Character type checking',
            'tokenizer' => 'PHP tokenizer (Laravel)',
            'xmlwriter' => 'XML writing',
            'simplexml' => 'SimpleXML parsing',
            'dom' => 'DOM manipulation',
            'exif' => 'Image EXIF metadata',
        ];

        $result = [];
        foreach ($required as $ext => $description) {
            $result[$ext] = [
                'loaded' => extension_loaded($ext),
                'description' => $description,
            ];
        }

        return $result;
    }

    /* ============================================================
     |  FILE STATUS CHECKER
     ============================================================ */

    public function getStatus(string $path): array
    {
        if (file_exists($path)) {
            return [
                'status' => 'Ada',
                'size' => $this->formatBytes(filesize($path)),
                'modified' => date('d M Y H:i', filemtime($path)),
            ];
        }

        return [
            'status' => 'Tidak Ada',
            'size' => '-',
            'modified' => '-',
        ];
    }

    /* ============================================================
     |  UTILITY: FORMAT BYTES
     ============================================================ */

    public function formatBytes($bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max((int) $bytes, 0);
        $pow = $bytes > 0 ? floor(log($bytes) / log(1024)) : 0;
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /* ============================================================
     |  REFRESH ACTION
     ============================================================ */

    public function refreshServerInfo(): void
    {
        $this->serverInfo = $this->getServerInfo();
        $this->dispatch('notify', type: 'success', message: 'Server info refreshed.');
    }

    /* ============================================================
     |  RENDER
     ============================================================ */

    public function render()
    {
        return view('suryacms::livewire.admin.file-check')->layout('suryacms::layouts.app');
    }
}
