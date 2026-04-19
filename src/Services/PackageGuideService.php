<?php

namespace Uiaciel\SuryaCms\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class PackageGuideService
{
    /**
     * Get all installed packages with their guide information
     *
     * @return array
     */
    public static function getInstalledPackages(): array
    {
        $packages = [];

        // Get packages from vendor directory
        $vendorPath = base_path('vendor/uiaciel');

        if (File::isDirectory($vendorPath)) {
            foreach (File::directories($vendorPath) as $packagePath) {
                $packageName = basename($packagePath);
                $guide = self::getPackageGuide($packageName);

                if ($guide) {
                    $packages[$packageName] = $guide;
                }
            }
        }

        return $packages;
    }

    /**
     * Get a specific package guide
     *
     * @param string $packageName
     * @return array|null
     */
    public static function getPackageGuide(string $packageName): ?array
    {
        $guideViewPath = "guide.{$packageName}";

        if (!View::exists($guideViewPath)) {
            return null;
        }

        // Try to get package info from composer.json
        $composerPath = base_path("vendor/uiaciel/{$packageName}/composer.json");
        $packageInfo = self::getPackageInfo($composerPath);

        return [
            'name' => $packageName,
            'display_name' => $packageInfo['display_name'] ?? ucfirst(str_replace('-', ' ', $packageName)),
            'description' => $packageInfo['description'] ?? null,
            'icon' => self::getPackageIcon($packageName),
            'route' => route('admin.guide.show', $packageName),
            'view' => $guideViewPath,
        ];
    }

    /**
     * Get package information from composer.json
     *
     * @param string $composerPath
     * @return array
     */
    private static function getPackageInfo(string $composerPath): array
    {
        if (!File::exists($composerPath)) {
            return [];
        }

        try {
            $content = json_decode(File::get($composerPath), true);
            return [
                'display_name' => $content['extra']['display_name'] ?? null,
                'description' => $content['description'] ?? null,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get icon for package based on name
     *
     * @param string $packageName
     * @return string
     */
    private static function getPackageIcon(string $packageName): string
    {
        $icons = [
            'suryacms' => 'fas fa-book',
            'newsletter' => 'fas fa-envelope',
            'corporation' => 'fas fa-building',
            'schooling' => 'fas fa-school',
            'themes' => 'fas fa-palette',
        ];

        return $icons[$packageName] ?? 'fas fa-cube';
    }

    /**
     * Check if a package guide exists
     *
     * @param string $packageName
     * @return bool
     */
    public static function hasGuide(string $packageName): bool
    {
        return View::exists("guide.{$packageName}");
    }

    /**
     * Get published guide files for a package
     *
     * @param string $packageName
     * @return array
     */
    public static function getPublishedGuideFiles(string $packageName): array
    {
        $guidePath = resource_path("views/vendor/guide/{$packageName}");

        if (!File::isDirectory($guidePath)) {
            return [];
        }

        $files = [];
        foreach (File::allFiles($guidePath) as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $file->getRealPath(),
                'size' => $file->getSize(),
            ];
        }

        return $files;
    }

    /**
     * Get core packages (suryacms and main packages)
     *
     * @return array
     */
    public static function getCorePackages(): array
    {
        $allPackages = self::getInstalledPackages();
        $core = ['suryacms', 'corporation', 'newsletter', 'schooling', 'themes'];

        return array_filter(
            $allPackages,
            fn($name) => in_array($name, $core),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get addon packages (non-core packages)
     *
     * @return array
     */
    public static function getAddonPackages(): array
    {
        $allPackages = self::getInstalledPackages();
        $core = ['suryacms', 'corporation', 'newsletter', 'schooling', 'themes'];

        return array_filter(
            $allPackages,
            fn($name) => !in_array($name, $core),
            ARRAY_FILTER_USE_KEY
        );
    }
}
