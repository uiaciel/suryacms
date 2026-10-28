<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme Information
    |--------------------------------------------------------------------------
    |
    | Metadata tema. Digunakan oleh ThemeManager untuk identifikasi,
    | versioning, dan tampilan di halaman pemilihan tema.
    |
    |   name          Nama tampilan tema
    |   path          Nama folder tema (resources/views/frontend/{path})
    |   version       Versi tema (semver)
    |   style         Framework CSS utama: tailwindcss | bootstrap | custom
    |   author        Nama pembuat tema
    |   url           URL author / repository
    |   license       Lisensi tema
    |   description   Deskripsi singkat tema
    |   theme_preview Path gambar preview (relatif ke public/)
    |
    */

    'info' => [
        'name'          => 'Kreasi',
        'path'          => 'kreasi',
        'version'       => '1.0.0',
        'style'         => 'tailwindcss',
        'author'        => 'Theme Generator',
        'url'           => 'https://kreasitekmedia.com',
        'license'       => 'MIT',
        'description'   => 'Tema Kreasi yang di-generate otomatis.',
        'theme_preview' => '/frontend/kreasi/theme_preview.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | Daftar file CSS dan JS yang di-load di layout tema.
    |
    | Format:
    |
    |   'styles'  => [
    |       'css/styles.css',
    |       'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css',
    |   ],
    |   'scripts' => [
    |       'js/app.js',
    |       'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/js/all.js',
    |   ],
    |
    */

    'assets' => [
        'styles'  => [
            'css/styles.css',
        ],
        'scripts' => [
            'js/app.js',
        ],
    ],

    'custom_blocks' => [

    // =====================================================================
    // BLOG
    // =====================================================================
        [
            'id'       => 'blog-block',
            'label'    => 'Blog',
            'category' => 'Plugin',
            'content'  => '[[blog]]',
            'preview'  => '
                <div style="padding:16px;font-family:sans-serif;">
                    <h2 style="margin:0 0 12px;">Blog</h2>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <li style="padding:12px 0;border-bottom:1px solid #eee;">
                            <h3 style="margin:0 0 4px;font-size:16px;">Judul Artikel Pertama</h3>
                            <small style="color:#888;">24 Mei 2026 · News</small>
                            <p style="margin:6px 0 0;color:#555;font-size:14px;">Ringkasan singkat artikel akan ditampilkan di sini...</p>
                        </li>
                        <li style="padding:12px 0;border-bottom:1px solid #eee;">
                            <h3 style="margin:0 0 4px;font-size:16px;">Judul Artikel Kedua</h3>
                            <small style="color:#888;">24 Juli 2026 · Tutorial</small>
                            <p style="margin:6px 0 0;color:#555;font-size:14px;">Ringkasan singkat artikel akan ditampilkan di sini...</p>
                        </li>
                        <li style="padding:12px 0;">
                            <h3 style="margin:0 0 4px;font-size:16px;">Judul Artikel Ketiga</h3>
                            <small style="color:#888;">6 September 2026 · Update</small>
                            <p style="margin:6px 0 0;color:#555;font-size:14px;">Ringkasan singkat artikel akan ditampilkan di sini...</p>
                        </li>
                    </ul>
                </div>
            ',
        ],

    // =====================================================================
    // GALLERY
    // =====================================================================
        [
            'id'       => 'gallery-block',
            'label'    => 'Gallery',
            'category' => 'Plugin',
            'content'  => '[[gallery]]',
            'preview'  => '
                <div style="padding:16px;font-family:sans-serif;">
                    <h2 style="margin:0 0 12px;">Gallery</h2>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=1" alt="Gallery 1" style="width:100%;border-radius:4px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=2" alt="Gallery 2" style="width:100%;border-radius:4px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=3" alt="Gallery 3" style="width:100%;border-radius:4px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=4" alt="Gallery 4" style="width:100%;border-radius:4px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=5" alt="Gallery 5" style="width:100%;border-radius:4px;">
                        <img src="https://dummyimage.com/300x300/cccccc/666&text=6" alt="Gallery 6" style="width:100%;border-radius:4px;">
                    </div>
                </div>
            ',
        ],

    // =====================================================================
    // SLIDER
    // =====================================================================
        [
            'id'       => 'slider-block',
            'label'    => 'Slider',
            'category' => 'Plugin',
            'content'  => '[[slider]]',
            'preview'  => '
                <div style="padding:16px;font-family:sans-serif;">
                    <div style="position:relative;border-radius:8px;overflow:hidden;">
                        <img src="https://dummyimage.com/1200x400/0a4fa8/ffffff&text=Slide+1" alt="Slide 1" style="width:100%;display:block;">
                        <div style="position:absolute;bottom:0;left:0;right:0;padding:16px;background:linear-gradient(transparent,rgba(0,0,0,.7));color:#fff;">
                            <h3 style="margin:0 0 4px;">Judul Slide</h3>
                            <p style="margin:0;font-size:14px;opacity:.85;">Deskripsi slide akan ditampilkan di sini</p>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;justify-content:center;margin-top:10px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#0a4fa8;"></span>
                        <span style="width:8px;height:8px;border-radius:50%;background:#ccc;"></span>
                        <span style="width:8px;height:8px;border-radius:50%;background:#ccc;"></span>
                    </div>
                </div>
            ',
        ],

    // =====================================================================
    // YOUTUBE
    // =====================================================================
        [
            'id'       => 'youtube-block',
            'label'    => 'YouTube',
            'category' => 'Plugin',
            'content'  => '[[youtube]]',
            'preview'  => '
                <div style="padding:16px;font-family:sans-serif;">
                    <h2 style="margin:0 0 12px;">Video</h2>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                        <article>
                            <img src="https://dummyimage.com/480x270/cccccc/666&text=Video+1" alt="Video 1" style="width:100%;border-radius:4px;">
                            <h3 style="margin:8px 0 4px;font-size:15px;">Judul Video Pertama</h3>
                            <small style="color:#888;">24 Mei 2026</small>
                        </article>
                        <article>
                            <img src="https://dummyimage.com/480x270/cccccc/666&text=Video+2" alt="Video 2" style="width:100%;border-radius:4px;">
                            <h3 style="margin:8px 0 4px;font-size:15px;">Judul Video Kedua</h3>
                            <small style="color:#888;">24 Juli 2026</small>
                        </article>
                    </div>
                </div>
            ',
        ],

    ],

];
