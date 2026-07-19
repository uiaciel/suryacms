<div class="min-h-screen bg-slate-50/50 p-4 sm:p-6 font-sans">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">

        <!-- Header Section -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-[#1e2356] to-[#343a8d] px-6 py-5">
            <div>
                <h2 class="text-xl font-bold text-white tracking-wide">Disaster Recovery & Backup Registry</h2>
                <p class="text-xs text-slate-300 mt-0.5">Manage full-system configuration states, asset archives, and
                    structural restorations.</p>
            </div>
            <button wire:click="generateBackup" @disabled($isBackingUp || $isRestoring)
                class="w-full sm:w-auto inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-emerald-700/20 active:scale-[0.98] transition-all disabled:opacity-50 disabled:pointer-events-none">
                <svg wire:loading.remove wire:target="generateBackup" class="h-4 w-4 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <svg wire:loading wire:target="generateBackup" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="generateBackup">Execute Full Backup</span>
                <span wire:loading wire:target="generateBackup">Initializing Process...</span>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- Notification Alerts -->
            @if (session()->has('message'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm"
                    role="alert">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            @error('restore')
                <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm"
                    role="alert">
                    <i class="fas fa-exclamation-triangle mt-0.5 text-rose-500"></i>
                    <div>
                        <strong class="font-bold block mb-0.5">System Restoration Failed</strong>
                        <span class="text-xs">{{ $message }}</span>
                    </div>
                </div>
            @enderror

            @error('backup')
                <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm"
                    role="alert">
                    <i class="fas fa-exclamation-triangle mt-0.5 text-rose-500"></i>
                    <div>
                        <strong class="font-bold block mb-0.5">Backup Generation Failed</strong>
                        <span class="text-xs">{{ $message }}</span>
                    </div>
                </div>
            @enderror

            <!-- Backup Progress Tracking Section -->
            @if ($isBackingUp)
                <div class="bg-blue-50/60 p-5 rounded-xl border border-blue-100 space-y-3"
                    wire:poll.1s="checkBackupProgress">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-blue-800 flex items-center gap-2">
                            <i class="fas fa-circle-notch animate-spin text-blue-500"></i> Packaging System Core
                        </h4>
                        <span class="text-sm font-bold text-blue-700">{{ $backupProgressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-blue-200/60 rounded-full h-3 overflow-hidden">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500 ease-out"
                            style="width: {{ max($backupProgressPercentage, 5) }}%"></div>
                    </div>
                    <p class="text-xs text-blue-600 font-medium italic">{{ $backupProgressStep }}</p>
                </div>
            @endif

            <!-- Environment Condition Status -->
            <div>
                @if ($isDatabaseEmpty)
                    <div class="bg-blue-50/60 border-l-4 border-blue-500 rounded-r-xl p-4">
                        <div class="flex gap-3">
                            <i class="fas fa-info-circle text-blue-500 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-sm font-bold text-blue-900 mb-0.5">Fresh Installation Detected</h5>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    The core database schema is currently uninitialized. You may safely proceed with a
                                    comprehensive structural deployment using an existing archive template below.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50/70 border-l-4 border-amber-500 rounded-r-xl p-4">
                        <div class="flex gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-sm font-bold text-amber-900 mb-0.5">Production Data Warning</h5>
                                <p class="text-xs text-amber-700 leading-relaxed">
                                    Active working sets exist within the current configuration. Initiating a full
                                    restoration cycle will systematically overwrite the current schemas, environmental
                                    assets, templates, and storage pools.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Backup File Registry Area -->
            @if (count($backupFiles) > 0)
                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Available Archive Snapshots
                    </h3>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <ul class="divide-y divide-slate-100">
                            @foreach ($backupFiles as $file)
                                <li
                                    class="p-4 hover:bg-slate-50/80 flex items-center justify-between transition-colors">
                                    <div class="flex items-center">
                                        <input id="file-{{ $loop->index }}" name="backup_file" type="radio"
                                            wire:model.live="selectedBackup" value="{{ $file['name'] }}"
                                            @disabled($isRestoring)
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 disabled:opacity-40">
                                        <label for="file-{{ $loop->index }}"
                                            class="ml-3 flex flex-col cursor-pointer select-none">
                                            <span
                                                class="text-sm font-semibold text-slate-800 tracking-wide">{{ $file['name'] }}</span>
                                            <span class="text-xs text-slate-400 mt-0.5 font-medium">
                                                Capacity: {{ $file['size'] }} <span
                                                    class="mx-1 text-slate-300">|</span> Encoded:
                                                {{ $file['modified'] }}
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.suryacms.admin.backup.download', ['filename' => $file['name']]) }}"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100/70">
                                            <i class="fas fa-download text-[10px]"></i> Download
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @error('selectedBackup')
                        <p class="text-rose-600 text-xs font-medium mt-1 flex items-center gap-1"><i
                                class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Snapshot Error Metadata Verification -->
                @if ($selectedBackup && !$backupMetadata)
                    <div class="bg-rose-50/70 border-l-4 border-rose-500 rounded-r-xl p-4">
                        <div class="flex gap-3">
                            <i class="fas fa-times-circle text-rose-500 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-sm font-bold text-rose-900 mb-0.5">Corrupted / Invalid Archive</h5>
                                <p class="text-xs text-rose-700 leading-relaxed">
                                    The core system metadata file (<code
                                        class="bg-rose-100/60 px-1 py-0.5 rounded text-[11px] font-mono">info.json</code>)
                                    is absent inside this bundle. The operational execution cannot proceed using this
                                    package.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Comparison View -->
                @if ($backupMetadata)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4 shadow-inner">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-shield-alt text-slate-400"></i> Structural Integrity Assessment
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200/60 shadow-sm">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wide text-slate-400 block mb-1">Active
                                    Core Version</span>
                                <div
                                    class="text-lg font-black tracking-tight {{ $currentSystemVersion === ($backupMetadata['suryacms_version'] ?? '') ? 'text-emerald-600' : 'text-indigo-600' }}">
                                    v{{ $currentSystemVersion }}
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-xl border border-slate-200/60 shadow-sm">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wide text-slate-400 block mb-1">Archive
                                    Blueprint Version</span>
                                <div
                                    class="text-lg font-black tracking-tight {{ $currentSystemVersion === ($backupMetadata['suryacms_version'] ?? '') ? 'text-emerald-600' : 'text-indigo-600' }}">
                                    v{{ $backupMetadata['suryacms_version'] ?? 'Unknown' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="pt-2 text-xs text-slate-600 font-medium grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-slate-200/60">
                            <div class="flex items-center gap-2"><i
                                    class="fab fa-laravel text-slate-400 w-4 text-center"></i> <span><strong>Framework
                                        Environment:</strong> Laravel
                                    {{ $backupMetadata['laravel_version'] ?? 'N/A' }}</span></div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-clock text-slate-400 w-4 text-center"></i> <span><strong>Snapshot
                                        Created At:</strong> {{ $backupMetadata['backup_timestamp'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Operational Restore Progress Tracking Section -->
                @if ($isRestoring)
                    <div class="bg-indigo-50/60 p-5 rounded-xl border border-indigo-100 space-y-3"
                        wire:poll.1s="checkProgress">
                        <div class="flex justify-between items-center">
                            <h4
                                class="text-xs font-bold uppercase tracking-wider text-indigo-800 flex items-center gap-2">
                                <i class="fas fa-circle-notch animate-spin text-indigo-500"></i> Executing Structural
                                Overwrite
                            </h4>
                            <span class="text-sm font-bold text-indigo-700">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-indigo-200/60 rounded-full h-3 overflow-hidden">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500 ease-out"
                                style="width: {{ max($progressPercentage, 5) }}%"></div>
                        </div>
                        <p class="text-xs text-indigo-600 font-medium italic">{{ $progressStep }}</p>
                    </div>
                @endif

                <!-- Execution Actions -->
                <div class="flex justify-end pt-2">
                    <button wire:click="startRestore" @disabled(!$selectedBackup || $isRestoring || !$backupMetadata)
                        class="w-full sm:w-auto text-sm font-bold py-3 px-8 rounded-xl text-white shadow-lg active:scale-[0.99] transition-all disabled:opacity-40 disabled:pointer-events-none {{ $isDatabaseEmpty ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/10' : 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/10' }}">
                        <span wire:loading.remove wire:target="startRestore"
                            class="flex items-center justify-center gap-2">
                            @if ($isDatabaseEmpty)
                                <i class="fas fa-download text-xs"></i> Restore Environment from Archive
                            @else
                                <i class="fas fa-exclamation-triangle text-xs"></i> Overwrite & Restore Environment
                            @endif
                        </span>
                        <span wire:loading wire:target="startRestore" class="flex items-center justify-center gap-2">
                            <i class="fas fa-spinner animate-spin text-xs"></i> Staging Snapshot Environment...
                        </span>
                    </button>
                </div>
            @else
                <!-- Empty Placeholder State -->
                <div class="text-center py-12 px-4 bg-slate-50/50 rounded-2xl border-2 border-slate-200 border-dashed">
                    <div
                        class="mx-auto h-12 w-12 text-slate-400 mb-4 flex items-center justify-center bg-white rounded-xl shadow-sm border border-slate-100">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 tracking-wide">No Active Archive Snapshots Detected
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                        Please transfer your bundle <code
                            class="bg-slate-200/60 px-1 py-0.5 rounded text-[10px] font-mono">.zip</code> structural
                        packages via FTP client straight into <code
                            class="bg-slate-200/60 px-1 py-0.5 rounded text-[10px] font-mono">storage/app/private/suryacms_backups</code>
                        directory.
                    </p>
                    <div class="mt-5">
                        <button wire:click="loadBackupFiles"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition-all active:scale-95">
                            <i class="fas fa-sync text-[10px]"></i> Refresh Local Directory
                        </button>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
