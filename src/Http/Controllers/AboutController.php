<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Routing\Controller;
use Uiaciel\SuryaCms\Services\PackageGuideService;

class AboutController extends Controller
{
    public function index()
    {
        $packages = PackageGuideService::getInstalledPackages();

        return view('suryacms::admin.about', [
            'packages' => $packages,
        ]);
    }
}
