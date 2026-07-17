<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <!-- Backup Item: Posts -->
        <button wire:click="exportPost" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-blue-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Posts Data</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $postscount }} Items</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-blue-500 transition-colors" wire:loading.remove
                wire:target="exportPost"></i>
            <i class="fas fa-spinner animate-spin text-blue-500" wire:loading wire:target="exportPost"></i>
        </button>

        <!-- Backup Item: Pages -->
        <button wire:click="exportPage" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-indigo-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fas fa-file-word"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Pages Data</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $pagescount }} Items</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-indigo-500 transition-colors" wire:loading.remove
                wire:target="exportPage"></i>
            <i class="fas fa-spinner animate-spin text-indigo-500" wire:loading wire:target="exportPage"></i>
        </button>

        <!-- Backup Item: Messages -->
        <button wire:click="exportInbox" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-inbox"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Messages</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Inbox Export</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-emerald-500 transition-colors" wire:loading.remove
                wire:target="exportInbox"></i>
            <i class="fas fa-spinner animate-spin text-emerald-500" wire:loading wire:target="exportInbox"></i>
        </button>

        <!-- Backup Item: Settings -->
        <button wire:click="exportSetting" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-orange-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Settings</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Config Backup</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-orange-500 transition-colors" wire:loading.remove
                wire:target="exportSetting"></i>
            <i class="fas fa-spinner animate-spin text-orange-500" wire:loading wire:target="exportSetting"></i>
        </button>

        <button wire:click="exportGallery" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-amber-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Gallery</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Media Backup</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-amber-500 transition-colors" wire:loading.remove
                wire:target="exportGallery"></i>
            <i class="fas fa-spinner animate-spin text-amber-500" wire:loading wire:target="exportGallery"></i>
        </button>

        <button wire:click="exportMenu" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-rose-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <i class="fas fa-bars"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Menus</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Navigation Data</p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-rose-500 transition-colors" wire:loading.remove
                wire:target="exportMenu"></i>
            <i class="fas fa-spinner animate-spin text-rose-500" wire:loading wire:target="exportMenu"></i>
        </button>

        <button wire:click="exportFullBackup" wire:loading.attr="disabled"
            class="group flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-slate-200 hover:shadow-md transition-all duration-200 text-left">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-600 group-hover:bg-slate-600 group-hover:text-white transition-colors">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Full Backup</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Backup database, file, tema, dan .env
                    </p>
                </div>
            </div>
            <i class="fas fa-download text-gray-300 group-hover:text-slate-500 transition-colors" wire:loading.remove
                wire:target="exportFullBackup"></i>
            <i class="fas fa-spinner animate-spin text-slate-500" wire:loading wire:target="exportFullBackup"></i>
        </button>

    </div>

    {{-- Backup Progress Panel --}}
    @if ($isBackingUp)
        <div wire:poll.1500ms="pollStatus" class="mt-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-2">
                <h3 class="text-sm font-bold text-slate-900">Proses Backup</h3>
                <p class="text-xs text-slate-500">{{ $backupMessage }}</p>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="bg-blue-600 h-3 rounded-full transition-all" style="width: {{ $backupProgress }}%"></div>
            </div>

            <div class="mt-2 text-xs text-slate-600">{{ $backupProgress }}% selesai</div>
        </div>
    @endif
    @if (!empty($backupFiles))
        <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Backup Penuh</h2>
                    <p class="text-xs text-slate-500">Download file backup yang sudah dibuat sebelumnya.</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach ($backupFiles as $file)
                    <div
                        class="flex flex-col gap-3 rounded-2xl border border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $file['name'] }}</p>
                            <p class="text-[11px] text-gray-500">{{ $file['updated_at'] }} · {{ $file['size'] }}</p>
                        </div>
                        <button wire:click="downloadBackup('{{ $file['name'] }}')" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                            <i class="fas fa-download text-[10px]"></i> Download
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
