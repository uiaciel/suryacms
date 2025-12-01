<div>

    <button class="btn btn-warning rounded-0 rounded-start shadow-lg fixed-right-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
        <i class="bi bi-question-circle-fill me-2"></i>
    </button>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">Manage Data & Support</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <h6 class="text-primary fw-bold mb-3">Import & Export</h6>

            <div class="card mb-3">
                <div class="card-body">
                    <form wire:submit.prevent="dataImport">
                        <h5 class="card-title">Import Data</h5>

                        <div class="input-group mb-3">
                            <input type="file" class="form-control @error('importFile') is-invalid @enderror" wire:model="importFile" id="importFile" aria-describedby="button-addon2" accept=".xls,.xlsx,.csv">
                            <button class="btn btn-primary" type="submit" id="button-addon2" wire:loading.attr="disabled" wire:target="importFile">
                                <span wire:loading.remove wire:target="importFile">Upload</span>
                                <span wire:loading wire:target="importFile">Uploading</span>
                            </button>
                        </div>
                        <div class="form-text" id="importFile">File Format: xls/xlsx/csv</div>

                        @error('importFile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true" x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">

                            <div x-show="isUploading" class="progress mt-2">
                                <div class="progress-bar" role="progressbar" :style="`width: ${progress}%`" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div wire:loading wire:target="dataImport" class="mt-2 text-primary">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span class="ms-2">Processing Import...</span>
                        </div>
                    </form>

                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        <button wire:click="dataExport" class="btn btn-success" wire:loading.attr="disabled" wire:target="dataExport">
                            <span wire:loading.remove wire:target="dataExport">
                                <i class="fas fa-download"></i> Export Data
                            </span>
                            <span wire:loading wire:target="dataExport">
                                <i class="fas fa-spinner fa-spin"></i> Preparing...
                            </span>
                        </button>

                    </div>

                </div>
            </div>

            <h6 class="text-primary fw-bold mb-3">Help & Support</h6>
            <div class="d-grid gap-2">
                <a href="#" class="btn btn-outline-info">User Documentation</a>
                <a href="#" class="btn btn-outline-success">Contact Support (Live Chat)</a>
            </div>
        </div>
    </div>

</div>
