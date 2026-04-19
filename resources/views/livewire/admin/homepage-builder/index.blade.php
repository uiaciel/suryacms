<div
    style="display: flex; flex-direction: column; height: 100vh; background-color: #0a0f1e; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"
    x-data="{
        showExportMenu: false,
        showImportMenu: false,
        deviceMode: 'desktop',
        isSaving: false,
        unsavedChanges: false,

        setDevice(mode) {
            this.deviceMode = mode;
            document.dispatchEvent(new CustomEvent('gjs-set-device', { detail: mode }));
        }
    }"
    @click.outside="showExportMenu = false; showImportMenu = false"
    @gjs-content-changed.window="unsavedChanges = true"
x-coak>

    {{-- ===================== TOAST SYSTEM ===================== --}}
    <div id="toast-container" style="position: fixed; top: 72px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    {{-- ===================== LOADING OVERLAY ===================== --}}
    <div id="global-loading" style="display: none; position: fixed; inset: 0; background: rgba(10,15,30,0.75); backdrop-filter: blur(4px); z-index: 9998; align-items: center; justify-content: center; flex-direction: column; gap: 16px;">
        <div style="width: 48px; height: 48px; border: 3px solid #334155; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
        <p id="loading-text" style="color: #94a3b8; font-size: 14px; font-weight: 500; margin: 0;">Memproses...</p>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.5; } }

        .toast-item {
            pointer-events: all;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            min-width: 260px;
            max-width: 360px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            animation: slideInRight 0.3s ease;
            border-left: 4px solid transparent;
            backdrop-filter: blur(8px);
        }
        .toast-success { background: #0f2a1f; border-color: #10b981; color: #6ee7b7; }
        .toast-error   { background: #2a0f0f; border-color: #ef4444; color: #fca5a5; }
        .toast-info    { background: #0f1a2a; border-color: #6366f1; color: #a5b4fc; }
        .toast-warning { background: #2a1f0f; border-color: #f59e0b; color: #fcd34d; }
        .toast-loading { background: #1a1f2e; border-color: #475569; color: #94a3b8; }

        /* Editor header */
        .editor-header-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.15s;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .editor-header-btn:hover { color: #f8fafc; background: rgba(255,255,255,0.08); }
        .editor-header-btn.active { color: #6366f1; background: rgba(99,102,241,0.15); }
        .device-btn { border-radius: 4px; padding: 5px 8px; }
        .device-btn.active { color: #6366f1; background: rgba(99,102,241,0.2); }

        /* GrapesJS override for Elementor-like look */
        #gjs-editor-container .gjs-cv-canvas { background: #f0f2f5 !important; }
        #gjs-editor-container .gjs-frame-wrapper { box-shadow: 0 4px 32px rgba(0,0,0,0.35); border-radius: 8px; overflow: hidden; }

        /* Left panel tabs */
        .gjs-pn-views-container { background: #1e293b !important; border-left: 1px solid #334155 !important; }
        .gjs-pn-views { background: #0f172a !important; border-bottom: 1px solid #334155 !important; }
        .gjs-pn-btn { color: #64748b !important; transition: all 0.15s !important; }
        .gjs-pn-btn:hover, .gjs-pn-btn.gjs-pn-active { color: #a5b4fc !important; border-bottom-color: #6366f1 !important; }

        /* Block panel */
        .gjs-block-categories { background: #1e293b !important; }
        .gjs-block-category .gjs-title { background: #0f172a !important; color: #94a3b8 !important; font-size: 11px !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
        .gjs-block { background: #1e293b !important; border: 1px solid #334155 !important; color: #94a3b8 !important; border-radius: 6px !important; transition: all 0.15s !important; }
        .gjs-block:hover { border-color: #6366f1 !important; color: #a5b4fc !important; transform: translateY(-1px) !important; box-shadow: 0 4px 12px rgba(99,102,241,0.2) !important; }
        .gjs-block-label { font-size: 11px !important; }

        /* Style manager */
        .gjs-sm-sector-title { background: #0f172a !important; color: #94a3b8 !important; border-bottom: 1px solid #334155 !important; font-size: 11px !important; letter-spacing: 1px !important; }
        .gjs-sm-sector, .gjs-sm-property { background: #1e293b !important; border-bottom: 1px solid #1e293b !important; }
        .gjs-sm-label { color: #64748b !important; font-size: 11px !important; }
        .gjs-sm-field { background: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; border-radius: 4px !important; }
        .gjs-sm-field:focus { border-color: #6366f1 !important; }

        /* Traits */
        .gjs-trt-trait .gjs-label { color: #64748b !important; font-size: 11px !important; }
        .gjs-trt-trait input, .gjs-trt-trait select { background: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; border-radius: 4px !important; }

        /* Layers */
        .gjs-layer { background: #1e293b !important; border-bottom: 1px solid #1a2540 !important; color: #94a3b8 !important; }
        .gjs-layer.gjs-selected { color: #a5b4fc !important; background: rgba(99,102,241,0.15) !important; }

        /* Toolbar */
        .gjs-toolbar { background: #4f46e5 !important; border-radius: 6px !important; box-shadow: 0 4px 12px rgba(79,70,229,0.4) !important; }
        .gjs-toolbar-item { color: #fff !important; }

        /* Right panel */
        .gjs-pn-panel { background: #1e293b !important; }

        /* Selector manager */
        .gjs-clm-tags-field { background: #0f172a !important; border-color: #334155 !important; }
        .gjs-clm-tag { background: #334155 !important; color: #94a3b8 !important; }

        /* Unsaved dot */
        .unsaved-dot {
            width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;
            display: inline-block; margin-left: 4px;
            animation: pulse 1.5s infinite;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
    </style>

    {{-- ===================== HEADER ===================== --}}
    <header style="all: initial; display: flex; align-items: center; justify-content: space-between; height: 56px; background: linear-gradient(135deg, #1e293b 0%, #162032 100%); padding: 0 16px; box-sizing: border-box; border-bottom: 1px solid #293548; font-family: inherit; z-index: 1000; flex-shrink: 0;">

        {{-- LEFT: Back + Logo + Title --}}
        <div style="display: flex; align-items: center; gap: 12px; min-width: 260px;">
            <a href="/admin" title="Back to Admin" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #293548; color: #94a3b8; border-radius: 7px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#ef4444'; this.style.color='#fff';" onmouseout="this.style.background='#293548'; this.style.color='#94a3b8';">
                <i class="fas fa-arrow-left" style="font-size: 12px;"></i>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-vector-square" style="color: #fff; font-size: 12px;"></i>
                </div>
                <span style="color: #f8fafc; font-size: 13px; font-weight: 700; letter-spacing: 0.3px;">Page Builder</span>
            </div>
            <div style="width: 1px; height: 24px; background: #293548;"></div>
            <div style="display: flex; align-items: center; gap: 6px; max-width: 220px;">
                <i class="fas fa-file-alt" style="color: #475569; font-size: 11px; flex-shrink: 0;"></i>
                <span style="color: #94a3b8; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $title }}</span>
                <span x-show="unsavedChanges" class="unsaved-dot" title="Unsaved changes"></span>
            </div>
        </div>

        {{-- CENTER: Device Toggle --}}
        <div style="display: flex; align-items: center; gap: 4px; background: #0f172a; border: 1px solid #293548; border-radius: 8px; padding: 3px;">
            <button type="button" class="device-btn editor-header-btn" :class="deviceMode === 'desktop' ? 'active' : ''" @click="setDevice('desktop')" title="Desktop">
                <i class="fas fa-desktop"></i>
            </button>
            <button type="button" class="device-btn editor-header-btn" :class="deviceMode === 'tablet' ? 'active' : ''" @click="setDevice('tablet')" title="Tablet">
                <i class="fas fa-tablet-alt"></i>
            </button>
            <button type="button" class="device-btn editor-header-btn" :class="deviceMode === 'mobile' ? 'active' : ''" @click="setDevice('mobile')" title="Mobile">
                <i class="fas fa-mobile-alt"></i>
            </button>
        </div>

        {{-- RIGHT: Actions --}}
        <div style="display: flex; align-items: center; gap: 8px; min-width: 260px; justify-content: flex-end;">

            {{-- Undo/Redo --}}
            <div style="display: flex; gap: 2px; background: #0f172a; border: 1px solid #293548; border-radius: 7px; padding: 2px;">
                <button type="button" id="btn-undo" class="editor-header-btn" title="Undo (Ctrl+Z)" onclick="window._gjsEditor && window._gjsEditor.UndoManager.undo()">
                    <i class="fas fa-undo" style="font-size: 11px;"></i>
                </button>
                <button type="button" id="btn-redo" class="editor-header-btn" title="Redo (Ctrl+Y)" onclick="window._gjsEditor && window._gjsEditor.UndoManager.redo()">
                    <i class="fas fa-redo" style="font-size: 11px;"></i>
                </button>
            </div>

            {{-- Preview --}}
            <a href="/" target="_blank" class="editor-header-btn" title="Preview Page" style="text-decoration: none; background: #0f172a; border: 1px solid #293548; border-radius: 7px;">
                <i class="fas fa-eye" style="font-size: 11px;"></i>
                <span style="font-size: 12px;">Preview</span>
            </a>

            {{-- Export Dropdown --}}
            <div style="position: relative;">
                <button type="button" class="editor-header-btn" style="background: #0f172a; border: 1px solid #293548; border-radius: 7px;"
                    @click="showExportMenu = !showExportMenu; showImportMenu = false" title="Export">
                    <i class="fas fa-download" style="font-size: 11px;"></i>
                    <span style="font-size: 12px;">Export</span>
                    <i class="fas fa-chevron-down" style="font-size: 9px; color: #64748b;"></i>
                </button>
                <div x-show="showExportMenu" x-cloak
                    style="position: absolute; top: calc(100% + 8px); right: 0; background: #1e293b; border: 1px solid #334155; border-radius: 10px; min-width: 210px; box-shadow: 0 12px 28px rgba(0,0,0,0.4); z-index: 2000; overflow: hidden;">
                    <div style="padding: 6px 0;">
                        <button type="button"
                            wire:click="exportPageJson"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan export JSON...', 'fa-spinner fa-spin')"
                            wire:loading.attr="disabled"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span style="width: 28px; height: 28px; background: rgba(99,102,241,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-file-code" style="color: #6366f1; font-size: 12px;"></i>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Export JSON</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">HTML & CSS sebagai JSON</div>
                            </div>
                        </button>
                        <button type="button"
                            wire:click="exportAllPages"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan backup semua halaman...', 'fa-spinner fa-spin')"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span style="width: 28px; height: 28px; background: rgba(16,185,129,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-database" style="color: #10b981; font-size: 12px;"></i>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Backup Semua Pages</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Export Excel semua halaman</div>
                            </div>
                        </button>
                        <button type="button"
                            wire:click="exportPage"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan export Excel...', 'fa-spinner fa-spin')"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span style="width: 28px; height: 28px; background: rgba(99,102,241,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-table" style="color: #818cf8; font-size: 12px;"></i>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Export Excel</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Export halaman ini ke XLSX</div>
                            </div>
                        </button>
                        <div style="margin: 4px 10px; border-top: 1px solid #293548;"></div>
                        <button type="button"
                            id="btn-copy-html"
                            onclick="copyHtmlToClipboard()"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span style="width: 28px; height: 28px; background: rgba(245,158,11,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-copy" style="color: #f59e0b; font-size: 12px;"></i>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Copy HTML</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Salin HTML ke clipboard</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Import Dropdown --}}
            <div style="position: relative;">
                <button type="button" class="editor-header-btn" style="background: #0f172a; border: 1px solid #293548; border-radius: 7px;"
                    @click="showImportMenu = !showImportMenu; showExportMenu = false" title="Import">
                    <i class="fas fa-upload" style="font-size: 11px;"></i>
                    <span style="font-size: 12px;">Import</span>
                    <i class="fas fa-chevron-down" style="font-size: 9px; color: #64748b;"></i>
                </button>
                <div x-show="showImportMenu" x-cloak
                    style="position: absolute; top: calc(100% + 8px); right: 0; background: #1e293b; border: 1px solid #334155; border-radius: 10px; min-width: 210px; box-shadow: 0 12px 28px rgba(0,0,0,0.4); z-index: 2000; overflow: hidden;">
                    <div style="padding: 8px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px; background: rgba(16,185,129,0.08); border: 1px dashed #10b981; border-radius: 8px; transition: 0.15s;"
                            onmouseover="this.style.background='rgba(16,185,129,0.15)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                            <input type="file" wire:model="fileUpload" accept=".json" style="display: none;"
                                wire:change="importPageJson"
                                @change="showImportMenu = false; showLoadingOverlay('Mengimpor JSON...'); showToast('loading', 'Mengimpor halaman dari JSON...')">
                            <i class="fas fa-file-import" style="color: #10b981; font-size: 18px; flex-shrink: 0;"></i>
                            <div>
                                <div style="color: #f1f5f9; font-size: 13px; font-weight: 600;">Import dari JSON</div>
                                <div style="color: #64748b; font-size: 11px; margin-top: 2px;">Max 5MB • format .json</div>
                            </div>
                        </label>
                        <p style="color: #475569; font-size: 11px; margin: 8px 4px 2px; text-align: center;">
                            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                            Data halaman saat ini akan digantikan
                        </p>
                    </div>
                </div>
            </div>

            <div style="width: 1px; height: 24px; background: #293548;"></div>

            {{-- Publish Button --}}
            <button type="button"
                :disabled="isSaving"
                @click="isSaving = true; unsavedChanges = false; $nextTick(() => document.getElementById('pageForm').dispatchEvent(new Event('submit', {bubbles: true, cancelable: true})))"
                style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border: none; padding: 8px 20px; border-radius: 7px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(99,102,241,0.4);"
                onmouseover="if(!this.disabled){ this.style.boxShadow='0 4px 16px rgba(99,102,241,0.6)'; this.style.transform='translateY(-1px)'; }"
                onmouseout="this.style.boxShadow='0 2px 8px rgba(99,102,241,0.4)'; this.style.transform='translateY(0)';">
                <span x-show="!isSaving"><i class="fas fa-cloud-upload-alt"></i> Publish</span>
                <span x-show="isSaving" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-circle-notch fa-spin" style="font-size: 12px;"></i> Menyimpan...
                </span>
            </button>
        </div>
    </header>

    {{-- ===================== HIDDEN FORM ===================== --}}
    <form id="pageForm" wire:submit.prevent="savePage" style="display: none;">
        <textarea id="htmldata" wire:model="html"></textarea>
        <textarea id="cssdata" wire:model="css"></textarea>
        <input type="text" wire:model="slug">
        <select wire:model="status"><option value="Publish">Publish</option><option value="Draft">Draft</option></select>
    </form>

    {{-- ===================== SUCCESS / ERROR FLASH ===================== --}}
    @if (session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('success', '{{ session('success') }}');
        });
    </script>
    @endif

    {{-- ===================== MAIN EDITOR AREA ===================== --}}
    <div wire:ignore id="gjs-editor-container" style="flex-grow: 1; position: relative; overflow: hidden;">
        <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
        <script src="https://unpkg.com/grapesjs"></script>
        <div id="gjs" style="height: 100% !important;">
            {!! $this->renderPageHtml($html) !!}
        </div>
    </div>

    {{-- ===================== STATUS BAR ===================== --}}
    <div style="height: 26px; background: #0a0f1e; border-top: 1px solid #1a2540; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; flex-shrink: 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span id="editor-status-components" style="color: #475569; font-size: 11px; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-layer-group" style="font-size: 10px;"></i> <span id="status-component-count">0</span> komponen
            </span>
            <span style="color: #1e293b;">|</span>
            <span id="editor-status-selected" style="color: #475569; font-size: 11px;">Tidak ada seleksi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="color: #293548; font-size: 11px; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-keyboard" style="font-size: 10px;"></i>
                Ctrl+Z Undo &bull; Ctrl+S Simpan
            </span>
            <span x-show="unsavedChanges" style="color: #f59e0b; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                <i class="fas fa-circle" style="font-size: 7px;"></i> Perubahan belum disimpan
            </span>
            <span x-show="!unsavedChanges" style="color: #10b981; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                <i class="fas fa-check-circle" style="font-size: 10px;"></i> Tersimpan
            </span>
        </div>
    </div>

    @push('scripts')
    <script>
    // ===================== TOAST SYSTEM =====================
    function showToast(type, message, iconClass) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-triangle',
            loading: 'fa-circle-notch fa-spin',
        };
        const icon = iconClass || icons[type] || 'fa-info-circle';
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        toast.innerHTML = `
            <i class="fas ${icon}" style="flex-shrink: 0; font-size: 14px;"></i>
            <span style="flex: 1;">${message}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;opacity:0.6;font-size:14px;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                <i class="fas fa-times"></i>
            </button>`;
        container.appendChild(toast);

        const duration = type === 'loading' ? 8000 : (type === 'error' ? 6000 : 3500);
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);

        return toast;
    }

    function showLoadingOverlay(text) {
        const overlay = document.getElementById('global-loading');
        const textEl = document.getElementById('loading-text');
        if (overlay) { overlay.style.display = 'flex'; if (textEl && text) textEl.textContent = text; }
    }
    function hideLoadingOverlay() {
        const overlay = document.getElementById('global-loading');
        if (overlay) overlay.style.display = 'none';
    }

    function copyHtmlToClipboard() {
        const html = document.getElementById('htmldata')?.value || '';
        navigator.clipboard.writeText(html).then(() => {
            showToast('success', 'HTML berhasil disalin ke clipboard!');
        }).catch(() => {
            showToast('error', 'Gagal menyalin HTML. Coba lagi.');
        });
        document.getElementById('showExportMenu') && (document.getElementById('showExportMenu').style.display = 'none');
    }

    // ===================== LIVEWIRE EVENTS =====================
    function registerLivewireEvents() {
        if (!window.Livewire) return;

        Livewire.on('swal', (data) => {
            hideLoadingOverlay();
            const eventData = Array.isArray(data) ? data[0] : data;
            const type = eventData.icon === 'success' ? 'success' : (eventData.icon === 'error' ? 'error' : 'info');
            showToast(type, eventData.text || eventData.title);

            // Reset publish button state
            if (window.Alpine) {
                const rootEl = document.querySelector('[x-data]');
                if (rootEl && rootEl._x_dataStack) {
                    try { rootEl._x_dataStack[0].isSaving = false; } catch(e) {}
                }
            }
        });

        Livewire.on('reload-page', (data) => {
            const delay = Array.isArray(data) ? (data[0]?.delay || 1500) : (data?.delay || 1500);
            showToast('info', 'Halaman akan dimuat ulang...');
            setTimeout(() => window.location.reload(), delay);
        });
    }

    document.addEventListener('livewire:navigated', registerLivewireEvents);
    document.addEventListener('DOMContentLoaded', function() {
        registerLivewireEvents();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('pageForm').dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));
                showToast('loading', 'Menyimpan halaman...');
            }
        });
    });

    if (window.Livewire) registerLivewireEvents();
    </script>
    @include('suryacms::livewire.admin.homepage-builder.scripts')
    @endpush

</div>
