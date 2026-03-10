@extends('suryacms::layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-black">
                S
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900">About <span class="text-blue-500">SuryaCMS</span></h1>
                <p class="text-gray-500 text-sm mt-1">Content Management System for Modern Web</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <!-- Description Section -->
        <div class="p-6 lg:p-8 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">What is SuryaCMS?</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                SuryaCMS is a powerful, modern content management system built with Laravel. Designed to be simple yet feature-rich, it provides everything you need to manage your website content efficiently.
            </p>
            <p class="text-gray-600 leading-relaxed">
                With SuryaCMS, you can create and manage pages, posts, galleries, menus, and more with an intuitive and beautiful interface powered by Tailwind CSS.
            </p>
        </div>

        <!-- Features Section -->
        <div class="p-6 lg:p-8 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Feature 1 -->
                <div class="flex gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Page Management</h3>
                        <p class="text-sm text-gray-600 mt-1">Create and manage static pages with ease</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="flex gap-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Blog & Posts</h3>
                        <p class="text-sm text-gray-600 mt-1">Publish and organize blog content efficiently</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="flex gap-4 p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-images"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Gallery Management</h3>
                        <p class="text-sm text-gray-600 mt-1">Organize and manage image galleries</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="flex gap-4 p-4 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-bars"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Menu Builder</h3>
                        <p class="text-sm text-gray-600 mt-1">Build custom navigation menus visually</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="flex gap-4 p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Multilingual Support</h3>
                        <p class="text-sm text-gray-600 mt-1">Create content in multiple languages</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="flex gap-4 p-4 bg-cyan-50 rounded-lg border border-cyan-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-palette"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Theme System</h3>
                        <p class="text-sm text-gray-600 mt-1">Customize your site appearance with themes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technology Stack Section -->
        <div class="p-6 lg:p-8 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Built With Modern Technology</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-lg border border-red-100 text-center">
                    <div class="text-3xl mb-2">🚀</div>
                    <h3 class="font-semibold text-gray-900">Laravel</h3>
                    <p class="text-xs text-gray-600 mt-1">Framework</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-100 text-center">
                    <div class="text-3xl mb-2">🎨</div>
                    <h3 class="font-semibold text-gray-900">Tailwind</h3>
                    <p class="text-xs text-gray-600 mt-1">Styling</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-100 text-center">
                    <div class="text-3xl mb-2">⚡</div>
                    <h3 class="font-semibold text-gray-900">Livewire</h3>
                    <p class="text-xs text-gray-600 mt-1">Interactivity</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-lg border border-orange-100 text-center">
                    <div class="text-3xl mb-2">📱</div>
                    <h3 class="font-semibold text-gray-900">Alpine JS</h3>
                    <p class="text-xs text-gray-600 mt-1">Interactivity</p>
                </div>
            </div>
        </div>

        <!-- Version & Info Section -->
        <div class="p-6 lg:p-8 bg-gradient-to-br from-blue-50 to-blue-100 border-b border-blue-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Version & Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Current Version</p>
                    <p class="text-2xl font-bold text-blue-600">v2.0.0</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">Laravel Framework</p>
                    <p class="text-2xl font-bold text-blue-600">v12</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">PHP Version</p>
                    <p class="text-2xl font-bold text-blue-600">8.3+</p>
                </div>
            </div>
        </div>

        <!-- Support & Links Section -->
        <div class="p-6 lg:p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Need Help?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="https://github.com/uiaciel/suryacms" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition group">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white group-hover:bg-black transition">
                        <i class="fab fa-github"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">GitHub Repository</p>
                        <p class="text-xs text-gray-500">View source code and contribute</p>
                    </div>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 group-hover:text-gray-900 transition"></i>
                </a>

                <a href="https://laravel.com" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition group">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white group-hover:shadow-lg transition">
                        <i class="fab fa-laravel"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Laravel Documentation</p>
                        <p class="text-xs text-gray-500">Learn more about Laravel framework</p>
                    </div>
                    <i class="fas fa-arrow-right ml-auto text-gray-400 group-hover:text-gray-900 transition"></i>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 lg:px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <p>Made with <span class="text-red-500">❤️</span> by <span class="font-semibold">Uiaciel</span></p>
            </div>
            <div class="text-xs text-gray-500">
                © 2024 SuryaCMS. All rights reserved.
            </div>
        </div>
    </div>
</div>
@endsection
