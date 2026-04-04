<div style="display: flex; flex-direction: column; height: 100vh; background-color: #0f172a; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <header style="all: initial; display: flex; align-items: center; justify-content: space-between; height: 60px; background-color: #1e293b; padding: 0 20px; box-sizing: border-box; border-bottom: 1px solid #334155; font-family: inherit; z-index: 1000;">

        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="/admin" style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; background-color: #334155; color: #ffffff; border-radius: 8px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.backgroundColor='#ef4444'" onmouseout="this.style.backgroundColor='#334155'">
                <i class="fas fa-arrow-left" style="font-size: 14px;"></i>
            </a>
            <h1 style="color: #f8fafc; font-size: 14px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Homepage Editor</h1>
        </div>

        <div style="flex-grow: 1; max-width: 450px; padding: 0 30px;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="fas fa-edit" style="position: absolute; left: 12px; color: #64748b; font-size: 12px;"></i>
                <input type="text" wire:model.defer="title" placeholder="Judul Halaman..."
                    style="width: 100%; background-color: #0f172a; border: 1px solid #334155; color: #f8fafc; padding: 8px 12px 8px 35px; border-radius: 6px; font-size: 13px; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#334155'">
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="display: flex; background-color: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 2px;">
                <a href="/" target="_blank" style="color: #94a3b8; padding: 6px 10px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#f8fafc'" onmouseout="this.style.color='#94a3b8'"><i class="fas fa-eye"></i></a>
                <button type="button" wire:click="exportPage" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 6px 10px; transition: 0.2s;" onmouseover="this.style.color='#f8fafc'" onmouseout="this.style.color='#94a3b8'"><i class="fas fa-download"></i></button>
            </div>

            <button type="submit" form="pageForm" style="background-color: #4f46e5; color: #ffffff; border: none; padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.backgroundColor='#6366f1'" onmouseout="this.style.backgroundColor='#4f46e5'">
                <i class="fas fa-cloud-upload-alt"></i> Publish
            </button>
        </div>
    </header>

    <form id="pageForm" wire:submit.prevent="savePage" style="display: none;">
        <input type="text" id="htmldata" wire:model="html">
        <input type="text" id="cssdata" wire:model="css">
        <input type="text" wire:model="slug">
        <select wire:model="status"><option value="Publish">Publish</option><option value="Draft">Draft</option></select>
    </form>

    @if (session()->has('success'))
    <div id="alert-success" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); background-color: #10b981; color: white; padding: 12px 24px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); z-index: 1100; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <span style="cursor: pointer; margin-left: 10px; opacity: 0.8;" onclick="this.parentElement.remove()">×</span>
    </div>
    <script>setTimeout(() => document.getElementById('alert-success')?.remove(), 3000);</script>
    @endif

    <div wire:ignore id="gjs-editor-container" style="flex-grow: 1; position: relative; background-color: #2c2c2c;">
            <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
            <script src="https://unpkg.com/grapesjs"></script>
        <div id="gjs" style="height: 100% !important;">
             {!! $this->renderPageHtml($html) !!}
        </div>
    </div>

    @push('scripts')
        @include('suryacms::livewire.admin.scripts')
    @endpush

</div>
