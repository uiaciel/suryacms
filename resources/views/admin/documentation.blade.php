@extends('suryacms::layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-black">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900">Documentation</h1>
                <p class="text-gray-500 text-sm mt-1">Complete Guide to Managing Your Content</p>
            </div>
        </div>
    </div>

    <!-- Table of Contents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:p-8 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Table of Contents</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="#posts" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-newspaper"></i>
                </div>
                <span class="font-medium text-gray-900">1. Creating Posts</span>
            </a>
            <a href="#pages" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-purple-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="font-medium text-gray-900">2. Creating Pages</span>
            </a>
            <a href="#gallery" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-images"></i>
                </div>
                <span class="font-medium text-gray-900">3. Upload Gallery</span>
            </a>
            <a href="#inbox" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-inbox"></i>
                </div>
                <span class="font-medium text-gray-900">4. Check Inbox</span>
            </a>
            <a href="#menu" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-bars"></i>
                </div>
                <span class="font-medium text-gray-900">5. Create Menu</span>
            </a>
            <a href="#settings" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-gray-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-cog"></i>
                </div>
                <span class="font-medium text-gray-900">6. Website Settings</span>
            </a>
            <a href="#theme" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-pink-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-palette"></i>
                </div>
                <span class="font-medium text-gray-900">7. Edit Theme</span>
            </a>
            <a href="#homepage" class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200">
                <div class="w-8 h-8 bg-cyan-500 rounded flex items-center justify-center text-white text-sm">
                    <i class="fas fa-home"></i>
                </div>
                <span class="font-medium text-gray-900">8. Homepage Builder</span>
            </a>
        </div>
    </div>

    <!-- Content Sections -->

    <!-- 1. Posts Section -->
    <section id="posts" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 lg:px-8 py-6 border-b border-blue-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">1. Creating Posts</h2>
            </div>
            <p class="text-gray-600 text-sm">Learn how to publish blog posts and media content</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Posts</span> → <span class="font-semibold">Create New Post</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Step-by-Step Guide</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Fill Basic Information</h4>
                            <p class="text-sm text-gray-600 mt-1">Enter the post title and select a category. The slug will be automatically generated from the title.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Add Cover Image</h4>
                            <p class="text-sm text-gray-600 mt-1">Upload a featured image for your post. This image will appear in blog listings and post previews.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Write Content</h4>
                            <p class="text-sm text-gray-600 mt-1">Use the rich text editor (TinyMCE) to write your post content. You can format text, add links, embed media, and more.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Publish Settings</h4>
                            <p class="text-sm text-gray-600 mt-1">Set the publication date and choose between Draft or Publish status. Drafts won't appear on the frontend.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">5</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Save & Publish</h4>
                            <p class="text-sm text-gray-600 mt-1">Click the "Save" button to publish or draft your post. You can edit it anytime.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">💡 Pro Tips</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Use descriptive titles for better SEO</li>
                    <li>• Add a high-quality featured image for better engagement</li>
                    <li>• Keep your content organized by category</li>
                    <li>• You can edit posts even after publishing</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- 2. Pages Section -->
    <section id="pages" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 lg:px-8 py-6 border-b border-purple-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">2. Creating Pages</h2>
            </div>
            <p class="text-gray-600 text-sm">Create static pages for important website content</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Pages</span> → <span class="font-semibold">Create New Page</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Differences from Posts</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-900">Feature</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-900">Pages</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-900">Posts</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 text-gray-700">Purpose</td>
                                <td class="px-4 py-2 text-gray-700">Static content (About, Contact)</td>
                                <td class="px-4 py-2 text-gray-700">Blog/News content</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-700">Categories</td>
                                <td class="px-4 py-2 text-gray-700">No categories</td>
                                <td class="px-4 py-2 text-gray-700">Organized by category</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-700">Date Display</td>
                                <td class="px-4 py-2 text-gray-700">No date shown</td>
                                <td class="px-4 py-2 text-gray-700">Displays publication date</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Common Page Examples</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <p class="font-semibold text-gray-900">About Us</p>
                        <p class="text-sm text-gray-600 mt-1">Information about your company/organization</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <p class="font-semibold text-gray-900">Contact</p>
                        <p class="text-sm text-gray-600 mt-1">Contact form and company information</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <p class="font-semibold text-gray-900">Privacy Policy</p>
                        <p class="text-sm text-gray-600 mt-1">Legal privacy information</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <p class="font-semibold text-gray-900">Terms of Service</p>
                        <p class="text-sm text-gray-600 mt-1">Website terms and conditions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Gallery Section -->
    <section id="gallery" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 lg:px-8 py-6 border-b border-green-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-images"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">3. Upload Gallery</h2>
            </div>
            <p class="text-gray-600 text-sm">Manage image galleries for your website</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Galleries</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">How to Upload Images</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Click Upload Button</h4>
                            <p class="text-sm text-gray-600 mt-1">Look for the upload button on the gallery page to start adding images.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Select Images</h4>
                            <p class="text-sm text-gray-600 mt-1">Choose one or multiple images from your computer. Supported formats: JPEG, PNG, GIF, SVG, WebP.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Add Description</h4>
                            <p class="text-sm text-gray-600 mt-1">Enter a description for each image to help with SEO and organization.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Confirm & Save</h4>
                            <p class="text-sm text-gray-600 mt-1">Images are automatically converted to WebP format for better performance.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">💡 Pro Tips</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Images are automatically converted to WebP format for faster loading</li>
                    <li>• Maximum file size: 20MB per image</li>
                    <li>• Use descriptive names for better organization</li>
                    <li>• You can edit or delete images anytime</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- 4. Inbox Section -->
    <section id="inbox" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 lg:px-8 py-6 border-b border-orange-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-inbox"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">4. Check Inbox</h2>
            </div>
            <p class="text-gray-600 text-sm">Manage messages from website visitors</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Contacts</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Managing Contact Messages</h3>
                <div class="space-y-4">
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">View Messages</h4>
                        <p class="text-sm text-gray-600 mt-1">See all messages submitted through your website's contact form in a table view.</p>
                    </div>
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Read Details</h4>
                        <p class="text-sm text-gray-600 mt-1">Click on any message to view full details including sender's name, email, and complete message.</p>
                    </div>
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Reply to Sender</h4>
                        <p class="text-sm text-gray-600 mt-1">Forward messages directly to your email for quick response if configured.</p>
                    </div>
                    <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Delete Messages</h4>
                        <p class="text-sm text-gray-600 mt-1">Remove or archive old messages to keep your inbox organized.</p>
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">💡 Pro Tips</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Check your inbox regularly for new messages</li>
                    <li>• Configure email notifications in Settings</li>
                    <li>• Export contacts for your mailing list</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- 5. Create Menu Section -->
    <section id="menu" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 lg:px-8 py-6 border-b border-red-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-bars"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">5. Create Menu</h2>
            </div>
            <p class="text-gray-600 text-sm">Build navigation menus for your website</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Menu</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Step-by-Step Guide</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Create New Menu</h4>
                            <p class="text-sm text-gray-600 mt-1">Click "Create New Menu" and give your menu a name (e.g., "Main Navigation").</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Add Menu Items</h4>
                            <p class="text-sm text-gray-600 mt-1">Add items by selecting from Pages, Posts, or Categories. You can also add custom links.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Organize Items</h4>
                            <p class="text-sm text-gray-600 mt-1">Drag and drop items to reorder them. Create parent-child relationships for dropdown menus.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Assign Position</h4>
                            <p class="text-sm text-gray-600 mt-1">Choose where the menu appears on your website (Header, Footer, etc.).</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">5</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Save & Preview</h4>
                            <p class="text-sm text-gray-600 mt-1">Save your menu and check how it looks on your website's frontend.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Menu Item Types</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 mb-2">Page Link</p>
                        <p class="text-sm text-gray-600">Links to static pages on your website</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 mb-2">Post Link</p>
                        <p class="text-sm text-gray-600">Links to blog posts</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 mb-2">Category Link</p>
                        <p class="text-sm text-gray-600">Links to post categories</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 mb-2">Custom Link</p>
                        <p class="text-sm text-gray-600">Links to external websites or URLs</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Website Settings Section -->
    <section id="settings" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 lg:px-8 py-6 border-b border-gray-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-cog"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">6. Website Settings</h2>
            </div>
            <p class="text-gray-600 text-sm">Configure your website's basic information</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Top Right Menu</span> → <span class="font-semibold">Settings</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Available Settings</h3>
                <div class="space-y-4">
                    <div class="border-l-4 border-gray-500 bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">General Information</h4>
                        <p class="text-sm text-gray-600 mt-1">Website name, tagline, description, and logo</p>
                    </div>
                    <div class="border-l-4 border-gray-500 bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Contact Information</h4>
                        <p class="text-sm text-gray-600 mt-1">Phone, email, address, and social media links</p>
                    </div>
                    <div class="border-l-4 border-gray-500 bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Language & Localization</h4>
                        <p class="text-sm text-gray-600 mt-1">Enable multilingual support and set default language</p>
                    </div>
                    <div class="border-l-4 border-gray-500 bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">SEO Settings</h4>
                        <p class="text-sm text-gray-600 mt-1">Meta descriptions, keywords, and robots.txt configuration</p>
                    </div>
                    <div class="border-l-4 border-gray-500 bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-gray-900">Maintenance Mode</h4>
                        <p class="text-sm text-gray-600 mt-1">Enable maintenance mode to restrict public access temporarily</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">⚠️ Important</h4>
                <p class="text-sm text-gray-600">Changes made in settings will affect your entire website. Always preview your site after making changes.</p>
            </div>
        </div>
    </section>

    <!-- 7. Edit Theme Section -->
    <section id="theme" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-pink-50 to-pink-100 px-6 lg:px-8 py-6 border-b border-pink-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-palette"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">7. Edit Theme</h2>
            </div>
            <p class="text-gray-600 text-sm">Customize your website's appearance</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Themes</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Theme Options</h3>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">🎨</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Theme Settings</h4>
                                <p class="text-sm text-gray-600 mt-1">Adjust colors, fonts, and layout options. Available settings depend on your current theme.</p>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">📝</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Theme Editor</h4>
                                <p class="text-sm text-gray-600 mt-1">Edit theme HTML, CSS, and JavaScript files directly. Useful for advanced customization.</p>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <div class="text-2xl">🔄</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Theme Converter</h4>
                                <p class="text-sm text-gray-600 mt-1">Convert Bootstrap themes to Tailwind CSS for modern styling and better performance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">⚠️ Warning</h4>
                <p class="text-sm text-gray-600">Be careful when editing theme files directly. Make a backup before making changes.</p>
            </div>
        </div>
    </section>

    <!-- 8. Homepage Builder Section -->
    <section id="homepage" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-cyan-50 to-cyan-100 px-6 lg:px-8 py-6 border-b border-cyan-200">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-home"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">8. Homepage Builder</h2>
            </div>
            <p class="text-gray-600 text-sm">Design your website's homepage visually</p>
        </div>
        <div class="p-6 lg:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Navigation</h3>
                <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700"><span class="font-semibold">Left Sidebar</span> → <span class="font-semibold">Dashboard</span> → <span class="font-semibold">Homepage Builder</span></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">How to Use Homepage Builder</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Access the Builder</h4>
                            <p class="text-sm text-gray-600 mt-1">Open the Homepage Builder tool from your admin dashboard.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Select Page Sections</h4>
                            <p class="text-sm text-gray-600 mt-1">Choose sections to add (Hero, Features, Gallery, Testimonials, CTA, etc.).</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Customize Content</h4>
                            <p class="text-sm text-gray-600 mt-1">Edit text, images, and other content for each section.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Arrange Sections</h4>
                            <p class="text-sm text-gray-600 mt-1">Drag and drop sections to reorder them as you like.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center text-sm font-bold">5</div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Preview & Publish</h4>
                            <p class="text-sm text-gray-600 mt-1">Preview your homepage before publishing it live.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Available Sections</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-image text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Hero Section</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-star text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Features</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-images text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Gallery</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-quote-left text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Testimonials</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-bullhorn text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Call to Action</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <i class="fas fa-code text-cyan-500"></i>
                        <span class="text-sm font-medium text-gray-900">Custom HTML</span>
                    </div>
                </div>
            </div>

            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">💡 Pro Tips</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Use high-quality images for the hero section</li>
                    <li>• Keep your homepage layout clean and organized</li>
                    <li>• Test on mobile devices to ensure responsive design</li>
                    <li>• Include a clear call-to-action button</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer Help Section -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 p-6 lg:p-8 text-center">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Need More Help?</h3>
        <p class="text-gray-600 text-sm mb-4">If you need additional assistance or have questions not answered here, please reach out to support.</p>
        <a href="{{ route('admin.about') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
            <i class="fas fa-info-circle"></i>
            About SuryaCMS
        </a>
    </div>
</div>
@endsection
