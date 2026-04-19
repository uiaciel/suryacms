@extends('suryacms::layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-black">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900">Package Guides</h1>
                    <p class="text-gray-500 text-sm mt-1">Documentation for installed packages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Packages Section -->
    @if($corePackages && count($corePackages) > 0)
    <div class="mb-12">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Core Packages</h2>
            <p class="text-gray-600">Main packages for core functionality</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($corePackages as $packageName => $package)
            <a href="{{ $package['route'] }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-300 transition overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-blue-600 text-xl group-hover:scale-110 transition">
                        <i class="{{ $package['icon'] }}"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mt-3 group-hover:text-blue-600 transition">
                        {{ $package['display_name'] }}
                    </h3>
                </div>

                <!-- Body -->
                <div class="p-6">
                    @if($package['description'])
                    <p class="text-gray-600 text-sm mb-4">{{ $package['description'] }}</p>
                    @endif

                    <div class="flex items-center gap-2 text-blue-600 font-medium text-sm group-hover:gap-3 transition">
                        <span>View Documentation</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Addon Packages Section -->
    @if($addonPackages && count($addonPackages) > 0)
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Additional Packages</h2>
            <p class="text-gray-600">Extended functionality packages</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($addonPackages as $packageName => $package)
            <a href="{{ $package['route'] }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-purple-300 transition overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-6 border-b border-gray-200">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-purple-600 text-xl group-hover:scale-110 transition">
                        <i class="{{ $package['icon'] }}"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mt-3 group-hover:text-purple-600 transition">
                        {{ $package['display_name'] }}
                    </h3>
                </div>

                <!-- Body -->
                <div class="p-6">
                    @if($package['description'])
                    <p class="text-gray-600 text-sm mb-4">{{ $package['description'] }}</p>
                    @endif

                    <div class="flex items-center gap-2 text-purple-600 font-medium text-sm group-hover:gap-3 transition">
                        <span>View Documentation</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if(!$corePackages || (count($corePackages) === 0 && (!$addonPackages || count($addonPackages) === 0)))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="text-6xl mb-4">📦</div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No Packages Found</h3>
        <p class="text-gray-600 mb-6">No installed packages with documentation available.</p>
    </div>
    @endif
</div>
@endsection
