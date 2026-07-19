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

    </div>

    <div class="space-y-3 mt-4">

        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-[#1e2356] to-[#343a8d] px-6 py-5">
            <div>
                <h2 class="text-xl font-bold text-white tracking-wide">Full Backup & Restore Data</h2>
                <p class="text-xs text-slate-300 mt-0.5">Manage full-system configuration states, asset archives, and
                    structural restorations.</p>
            </div>
            <a href="/admin/system/restore"
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

            </a>
        </div>

        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Available Archive Backups
        </h3>

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <ul class="divide-y divide-slate-100">
                @foreach ($backupFiles as $file)
                    <li class="p-4 hover:bg-slate-50/80 flex items-center justify-between transition-colors">
                        <div class="flex items-center">
                            <input id="file-{{ $loop->index }}" name="backup_file" type="radio"
                                wire:model.live="selectedBackup" value="{{ $file['name'] }}"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 disabled:opacity-40">
                            <label for="file-{{ $loop->index }}"
                                class="ml-3 flex flex-col cursor-pointer select-none">
                                <span
                                    class="text-sm font-semibold text-slate-800 tracking-wide">{{ $file['name'] }}</span>
                                <span class="text-xs text-slate-400 mt-0.5 font-medium">
                                    Capacity: {{ $file['size'] }} <span class="mx-1 text-slate-300">|</span> Encoded:
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
</div>
