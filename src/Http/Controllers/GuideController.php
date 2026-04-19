<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Routing\Controller;
use Uiaciel\SuryaCms\Services\PackageGuideService;

class GuideController extends Controller
{
    /**
     * Show guide index with all packages
     */
    public function index()
    {
        $corePackages = PackageGuideService::getCorePackages();
        $addonPackages = PackageGuideService::getAddonPackages();

        return view('suryacms::admin.guide.index', [
            'corePackages' => $corePackages,
            'addonPackages' => $addonPackages,
        ]);
    }

    /**
     * Show guide for a specific package
     */
    public function show(string $package)
    {
        if (!PackageGuideService::hasGuide($package)) {
            abort(404, "Guide for package '{$package}' not found");
        }

        $guide = PackageGuideService::getPackageGuide($package);
        $allPackages = PackageGuideService::getInstalledPackages();

        return view('suryacms::admin.guide.show', [
            'package' => $package,
            'guide' => $guide,
            'allPackages' => $allPackages,
        ]);
    }
}
