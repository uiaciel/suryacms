<div class="space-y-6">

    <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">

                <div>
                    <h1 class="text-xl font-extrabold text-slate-800">Server Informations</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-400">
                            <li><a href="/{{ config('suryacms.admin_prefix') }}"
                                    class="text-blue-500 hover:underline">Admin</a></li>
                            <li><i class="fas fa-chevron-right text-[9px]"></i></li>
                            <li class="font-medium text-gray-600">System</li>
                        </ol>
                    </nav>
                </div>
            </div>

        </div>

    {{-- ============================================================ --}}
    {{-- PHP INFORMATION --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <i class="fa-brands fa-php text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">PHP Environment</h4>
                    <p class="text-xs text-gray-500">Versi PHP, konfigurasi, dan resource limit</p>
                </div>
            </div>
            @php $php = $serverInfo['php']; @endphp
            <span class="text-xs font-bold px-2.5 py-1 rounded-full
                {{ version_compare($php['version'], '8.1.0', '>=') ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                PHP {{ $php['version'] }}
            </span>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ([
                'Version' => $php['version'],
                'SAPI' => $php['sapi'],
                'OS' => $php['os'] . ' (' . $php['architecture'] . ')',
                'Memory Limit' => $php['memory_limit'],
                'Memory Usage' => $php['memory_usage'],
                'Memory Peak' => $php['memory_peak'],
                'Max Execution Time' => $php['max_execution_time'],
                'Max Input Time' => $php['max_input_time'],
                'Max Input Vars' => $php['max_input_vars'],
                'Post Max Size' => $php['post_max_size'],
                'Upload Max Filesize' => $php['upload_max_filesize'],
                'Timezone' => $php['timezone'],
                'Display Errors' => $php['display_errors'],
                'OPcache' => $php['opcache_enabled'],
                'Server Time' => $php['server_time'],
            ] as $label => $value)
                <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-800 truncate" title="{{ $value }}">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- WEB SERVER INFORMATION --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
            <div class="p-2 bg-sky-100 text-sky-600 rounded-lg">
                <i class="fa-solid fa-server text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Web Server</h4>
                <p class="text-xs text-gray-500">Software server & environment host</p>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @php $srv = $serverInfo['server']; @endphp
            @foreach ([
                'Software' => $srv['software'],
                'Hostname' => $srv['hostname'],
                'Server IP' => $srv['server_ip'],
                'Port' => $srv['server_port'],
                'Protocol' => $srv['protocol'],
                'HTTPS' => $srv['https'],
                'User' => $srv['user'],
                'Doc Root' => $srv['document_root'],
            ] as $label => $value)
                <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-800 truncate" title="{{ $value }}">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- IMAGE SUPPORT (GD + IMAGICK) --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-pink-100 text-pink-600 rounded-lg">
                    <i class="fa-solid fa-image text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Image Processing Support</h4>
                    <p class="text-xs text-gray-500">GD & Imagick — dibutuhkan untuk konversi WebP</p>
                </div>
            </div>

            @php $img = $serverInfo['image_support']; @endphp
            @if ($img['gd']['webp_support'] || $img['imagick']['webp'])
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                    <i class="fas fa-check-circle mr-1"></i> WebP Ready
                </span>
            @else
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700 badge-pulse">
                    <i class="fas fa-times-circle mr-1"></i> WebP Unavailable
                </span>
            @endif
        </div>

        <div class="p-5 space-y-4">

            {{-- GD --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-sm font-bold text-gray-700">GD Library</h5>
                    @if ($img['gd']['loaded'])
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 uppercase">Loaded</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 uppercase">Not Loaded</span>
                    @endif
                </div>

                @if ($img['gd']['loaded'])
                    <p class="text-[10px] text-gray-400 font-mono mb-2">{{ $img['gd']['version'] }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach ([
                            'WebP' => $img['gd']['webp_support'],
                            'JPEG' => $img['gd']['jpeg_support'],
                            'PNG' => $img['gd']['png_support'],
                            'GIF Read' => $img['gd']['gif_read'],
                            'GIF Create' => $img['gd']['gif_create'],
                            'AVIF' => $img['gd']['avif_support'],
                            'BMP' => $img['gd']['bmp_support'],
                            'FreeType' => $img['gd']['free_type'],
                        ] as $feature => $supported)
                            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg border
                                {{ $supported ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-gray-50' }}">
                                <span class="text-[11px] font-semibold text-gray-700">{{ $feature }}</span>
                                @if ($supported)
                                    <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                                @else
                                    <i class="fas fa-times-circle text-gray-400 text-xs"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Imagick --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-sm font-bold text-gray-700">Imagick (ImageMagick)</h5>
                    @if ($img['imagick']['loaded'])
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 uppercase">Loaded</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 uppercase">Optional</span>
                    @endif
                </div>

                @if ($img['imagick']['loaded'])
                    <p class="text-[10px] text-gray-400 font-mono mb-2 truncate">{{ $img['imagick']['version'] }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                        @foreach ([
                            'WebP' => $img['imagick']['webp'],
                            'JPEG' => $img['imagick']['jpeg'],
                            'PNG' => $img['imagick']['png'],
                            'GIF' => $img['imagick']['gif'],
                            'AVIF' => $img['imagick']['avif'],
                            'PDF' => $img['imagick']['pdf'],
                        ] as $feature => $supported)
                            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg border
                                {{ $supported ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-gray-50' }}">
                                <span class="text-[11px] font-semibold text-gray-700">{{ $feature }}</span>
                                @if ($supported)
                                    <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                                @else
                                    <i class="fas fa-times-circle text-gray-400 text-xs"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Functions --}}
            <div>
                <h5 class="text-sm font-bold text-gray-700 mb-2">Required Functions</h5>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach ($img['functions'] as $fn => $available)
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg border
                            {{ $available ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                            <span class="text-[10px] font-mono font-semibold text-gray-700 truncate" title="{{ $fn }}">{{ $fn }}()</span>
                            @if ($available)
                                <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                            @else
                                <i class="fas fa-times-circle text-red-500 text-xs"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STORAGE --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
            <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                <i class="fa-solid fa-hard-drive text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Storage & Filesystem</h4>
                <p class="text-xs text-gray-500">Disk usage, permissions, dan symlink status</p>
            </div>
        </div>

        @php $st = $serverInfo['storage']; @endphp
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ([
                'Disk Free' => $st['disk_free'],
                'Disk Total' => $st['disk_total'],
                'Disk Used' => $st['disk_used_percent'],
                'Public Disk Path' => $st['public_disk_path'],
            ] as $label => $value)
                <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-800 truncate font-mono" title="{{ $value }}">{{ $value }}</span>
                </div>
            @endforeach

            @foreach ([
                'Public Writable' => $st['public_writable'],
                'Galleries Writable' => $st['galleries_writable'],
                'Storage Symlink' => $st['storage_link_exists'],
            ] as $label => $available)
                <div class="flex items-center justify-between p-3 rounded-xl border
                    {{ $available ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                    <span class="text-[11px] font-bold text-gray-700 uppercase tracking-wider">{{ $label }}</span>
                    @if ($available)
                        <i class="fas fa-check-circle text-emerald-600"></i>
                    @else
                        <i class="fas fa-times-circle text-red-500"></i>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DATABASE --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-teal-100 text-teal-600 rounded-lg">
                    <i class="fa-solid fa-database text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Database</h4>
                    <p class="text-xs text-gray-500">Koneksi & versi driver</p>
                </div>
            </div>
            @php $db = $serverInfo['database']; @endphp
            @if ($db['connected'] ?? false)
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                    <i class="fas fa-check-circle mr-1"></i> Connected
                </span>
            @else
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                    <i class="fas fa-times-circle mr-1"></i> Disconnected
                </span>
            @endif
        </div>

        @if ($db['connected'] ?? false)
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ([
                    'Driver' => $db['driver'],
                    'Version' => $db['version'],
                    'Database' => $db['database'],
                    'Host' => $db['host'],
                ] as $label => $value)
                    <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
                        <span class="text-sm font-semibold text-gray-800 truncate" title="{{ $value }}">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-5">
                <div class="p-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ $db['error'] ?? 'Tidak dapat terhubung ke database.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- LARAVEL --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
            <div class="p-2 bg-rose-100 text-rose-600 rounded-lg">
                <i class="fa-brands fa-laravel text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Laravel Application</h4>
                <p class="text-xs text-gray-500">Framework version & driver configuration</p>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @php $lv = $serverInfo['laravel']; @endphp
            @foreach ([
                'Version' => $lv['version'],
                'Environment' => $lv['environment'],
                'Debug Mode' => $lv['debug_mode'],
                'Timezone' => $lv['timezone'],
                'Locale' => $lv['locale'],
                'Cache Driver' => $lv['cache_driver'],
                'Session Driver' => $lv['session_driver'],
                'Queue Driver' => $lv['queue_driver'],
            ] as $label => $value)
                <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-800 truncate" title="{{ $value }}">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- PHP EXTENSIONS --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-violet-100 text-violet-600 rounded-lg">
                    <i class="fa-solid fa-puzzle-piece text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">PHP Extensions</h4>
                    <p class="text-xs text-gray-500">Ekstensi penting untuk aplikasi</p>
                </div>
            </div>

            @php
                $ext = $serverInfo['extensions'];
                $loadedCount = collect($ext)->where('loaded', true)->count();
                $totalCount = count($ext);
            @endphp
            <span class="text-xs font-bold px-2.5 py-1 rounded-full
                {{ $loadedCount === $totalCount ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                {{ $loadedCount }}/{{ $totalCount }} Loaded
            </span>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach ($ext as $name => $info)
                <div class="flex items-center justify-between px-3 py-2 rounded-xl border
                    {{ $info['loaded'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-red-200 bg-red-50/50' }}">
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-gray-800 font-mono">{{ $name }}</span>
                        <span class="text-[10px] text-gray-500 truncate" title="{{ $info['description'] }}">
                            {{ $info['description'] }}
                        </span>
                    </div>
                    @if ($info['loaded'])
                        <i class="fas fa-check-circle text-emerald-600 text-sm shrink-0"></i>
                    @else
                        <i class="fas fa-times-circle text-red-500 text-sm shrink-0"></i>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
