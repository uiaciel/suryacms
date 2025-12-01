<div>


    <ul class="list-group list-group-flush small">

        <!-- Backup Item: Posts -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class=" me-2">Data Posts:</strong>
                <span class="text-muted">{{ $postscount }} Posts</span> <!-- Mock Data -->

            </div>
            <button wire:click="exportPost" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportPost" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-file-earmark-text me-1"></i> Export
            </button>
        </li>

        <!-- Backup Item: Pages -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class=" me-2">Data Pages:</strong>
                <span class="text-muted">{{ $pagescount }} Pages</span> <!-- Mock Data -->

            </div>
            <button wire:click="exportPage" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportPage" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-file-earmark-break me-1"></i> Export
            </button>
        </li>

        <!-- Backup Item: Messages -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class="">Messages:</strong>
            </div>
            <button wire:click="exportInbox" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportInbox" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-inbox me-1"></i> Export
            </button>
        </li>

        <!-- Backup Item: Gallery -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class="">Data Gallery:</strong>
            </div>
            <button wire:click="exportGallery" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportGallery" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-images me-1"></i> Export
            </button>
        </li>

        <!-- Backup Item: Menu -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class="">Data Menu:</strong>
            </div>
            <button wire:click="exportMenu" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportMenu" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-list-ul me-1"></i> Export
            </button>
        </li>

        <!-- Backup Item: Settings -->
        <li class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <strong class="">Settings:</strong>
            </div>
            <button wire:click="exportSetting" wire:loading.attr="disabled" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <span wire:loading wire:target="exportSetting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bi bi-sliders me-1"></i> Export
            </button>
        </li>

    </ul>

</div>
