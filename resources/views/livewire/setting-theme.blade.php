<div class="container-fluid">

    <div class="container">
        <div class="d-flex justify-content-between  mb-3">
            <h3>Themes </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active text-lowercase"><a href="#">Theme</a></li>

                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">

            <div class="card shadow-sm p-4">
                <h5 class="mb-3 fw-bold">Upload & Install Theme</h5>

                <form wire:submit.prevent="uploadAndInstall" class="space-y-3">
                    <div>
                        <input type="file" wire:model="themeZip" class="form-control">
                        @error('themeZip') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div wire:loading wire:target="themeZip" class="text-muted">
                        ⏳ Mengunggah file...
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary mt-3">
                        <span wire:loading.remove>Upload & Install</span>
                        <span wire:loading>Installing...</span>
                    </button>
                </form>

                <script>
                    document.addEventListener('livewire:init', () => {
                        Livewire.on('alert', (event) => {
                            alert(event.message);
                        });
                    });

                </script>
            </div>

            </div>

        </div>

    </div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
</div>
