<div class="editor-builder-shell"
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
    }" @click.outside="showExportMenu = false; showImportMenu = false"
    @gjs-content-changed.window="unsavedChanges = true" x-cloak>

    {{-- ===================== TOAST SYSTEM ===================== --}}
    <div id="toast-container"
        style="position: fixed; top: 72px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;">
    </div>

    {{-- ===================== LOADING OVERLAY ===================== --}}
    <div id="global-loading"
        style="display: none; position: fixed; inset: 0; background: rgba(10,15,30,0.75); backdrop-filter: blur(4px); z-index: 9998; align-items: center; justify-content: center; flex-direction: column; gap: 16px;">
        <div
            style="width: 48px; height: 48px; border: 3px solid #334155; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite;">
        </div>
        <p id="loading-text" style="color: #94a3b8; font-size: 14px; font-weight: 500; margin: 0;">Memproses...</p>
    </div>
    {{-- ===================== HEADER ===================== --}}
    <header
        style="all: initial; display: flex; align-items: center; justify-content: space-between; height: 64px; background: linear-gradient(135deg, #111827 0%, #0f172a 100%); padding: 0 18px; box-sizing: border-box; border-bottom: 1px solid rgba(148, 163, 184, 0.18); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18); font-family: inherit; z-index: 1000; flex-shrink: 0;">

        {{-- LEFT: Back + Logo + Title --}}
        <div style="display: flex; align-items: center; gap: 12px; min-width: 260px;">
            <a href="/{{ config('suryacms.admin_prefix') }}" title="Back to Admin"
                style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #293548; color: #94a3b8; border-radius: 7px; text-decoration: none; transition: all 0.2s;"
                onmouseover="this.style.background='#ef4444'; this.style.color='#fff';"
                onmouseout="this.style.background='#293548'; this.style.color='#94a3b8';">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div
                    style="width: 28px; height: 28px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v4h-7zM13 10h7v10h-7zM4 13h7v7H4z"/></svg>
                </div>
                <span style="color: #f8fafc; font-size: 13px; font-weight: 700; letter-spacing: 0.3px;">Page
                    Builder</span>
            </div>
            <div style="width: 1px; height: 24px; background: #293548;"></div>
            <div style="display: flex; align-items: center; gap: 6px; max-width: 220px;">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="#475569" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/></svg>
                <span
                    style="color: #94a3b8; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $title }}</span>
                <span x-show="unsavedChanges" class="unsaved-dot" title="Unsaved changes"></span>
            </div>
        </div>

        {{-- CENTER: Device Toggle --}}
        <div
            style="display: flex; align-items: center; gap: 4px; background: #0f172a; border: 1px solid #293548; border-radius: 8px; padding: 3px;">
            <button type="button" class="device-btn editor-header-btn"
                :class="deviceMode === 'desktop' ? 'active' : ''" @click="setDevice('desktop')" title="Desktop">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg>
            </button>
            <button type="button" class="device-btn editor-header-btn"
                :class="deviceMode === 'tablet' ? 'active' : ''" @click="setDevice('tablet')" title="Tablet">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
            </button>
            <button type="button" class="device-btn editor-header-btn"
                :class="deviceMode === 'mobile' ? 'active' : ''" @click="setDevice('mobile')" title="Mobile">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
            </button>
        </div>

        {{-- RIGHT: Actions --}}
        <div style="display: flex; align-items: center; gap: 8px; min-width: 260px; justify-content: flex-end;">

            {{-- Undo/Redo --}}
            <div
                style="display: flex; gap: 2px; background: #0f172a; border: 1px solid #293548; border-radius: 7px; padding: 2px;">
                <button type="button" id="btn-undo" class="editor-header-btn" title="Undo (Ctrl+Z)"
                    onclick="window._gjsEditor && window._gjsEditor.UndoManager.undo()">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 14L4 9l5-5"/><path d="M20 20v-2a6 6 0 0 0-6-6H4"/></svg>
                </button>
                <button type="button" id="btn-redo" class="editor-header-btn" title="Redo (Ctrl+Y)"
                    onclick="window._gjsEditor && window._gjsEditor.UndoManager.redo()">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 14l5-5-5-5"/><path d="M4 20v-2a6 6 0 0 1 6-6h10"/></svg>
                </button>
            </div>

            {{-- Preview --}}
            <a href="/" target="_blank" class="editor-header-btn" title="Preview Page"
                style="text-decoration: none; background: #0f172a; border: 1px solid #293548; border-radius: 7px;">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <span style="font-size: 12px;">Preview</span>
            </a>

            {{-- Export Dropdown --}}
            <div style="position: relative;">
                <button type="button" class="editor-header-btn"
                    style="background: #0f172a; border: 1px solid #293548; border-radius: 7px;"
                    @click="showExportMenu = !showExportMenu; showImportMenu = false" title="Export">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="M7 18l5 5 5-5"/><path d="M4 21h16"/></svg>
                    <span style="font-size: 12px;">Export</span>
                    <svg viewBox="0 0 24 24" width="9" height="9" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="showExportMenu" x-cloak
                    style="position: absolute; top: calc(100% + 8px); right: 0; background: #1e293b; border: 1px solid #334155; border-radius: 10px; min-width: 210px; box-shadow: 0 12px 28px rgba(0,0,0,0.4); z-index: 2000; overflow: hidden;">
                    <div style="padding: 6px 0;">
                        <button type="button" wire:click="exportPageJson"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan export JSON...')"
                            wire:loading.attr="disabled"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span
                                style="width: 28px; height: 28px; background: rgba(99,102,241,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#6366f1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/></svg>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Export JSON</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">HTML & CSS sebagai JSON
                                </div>
                            </div>
                        </button>
                        <button type="button" wire:click="exportAllPages"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan backup semua halaman...')"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span
                                style="width: 28px; height: 28px; background: rgba(16,185,129,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="7" ry="3"/><path d="M5 12c0 1.7 3.1 3 7 3s7-1.3 7-3"/><path d="M5 17c0 1.7 3.1 3 7 3s7-1.3 7-3"/></svg>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Backup Semua Pages</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Export Excel semua
                                    halaman</div>
                            </div>
                        </button>
                        <button type="button" wire:click="exportPage"
                            @click="showExportMenu = false; showToast('info', 'Menyiapkan export Excel...')"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span
                                style="width: 28px; height: 28px; background: rgba(99,102,241,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6l6 6v12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M9 3v6h6"/><path d="M8 15h8M8 19h8"/></svg>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Export Excel</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Export halaman ini ke
                                    XLSX</div>
                            </div>
                        </button>
                        <div style="margin: 4px 10px; border-top: 1px solid #293548;"></div>
                        <button type="button" id="btn-copy-html" onclick="copyHtmlToClipboard()"
                            style="display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: #cbd5e1; padding: 10px 14px; cursor: pointer; font-size: 13px; transition: 0.15s; text-align: left;"
                            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='none'">
                            <span
                                style="width: 28px; height: 28px; background: rgba(245,158,11,0.15); border-radius: 6px; display:flex; align-items:center; justify-content:center;">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#f59e0b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </span>
                            <div>
                                <div style="font-weight: 600; color: #f1f5f9;">Copy HTML</div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Salin HTML ke clipboard
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Import Dropdown --}}
            <div style="position: relative;">
                <button type="button" class="editor-header-btn"
                    style="background: #0f172a; border: 1px solid #293548; border-radius: 7px;"
                    @click="showImportMenu = !showImportMenu; showExportMenu = false" title="Import">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="M7 18l5 5 5-5"/><path d="M4 21h16"/></svg>
                    <span style="font-size: 12px;">Import</span>
                    <svg viewBox="0 0 24 24" width="9" height="9" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="showImportMenu" x-cloak
                    style="position: absolute; top: calc(100% + 8px); right: 0; background: #1e293b; border: 1px solid #334155; border-radius: 10px; min-width: 210px; box-shadow: 0 12px 28px rgba(0,0,0,0.4); z-index: 2000; overflow: hidden;">
                    <div style="padding: 8px;">
                        <label
                            style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px; background: rgba(16,185,129,0.08); border: 1px dashed #10b981; border-radius: 8px; transition: 0.15s;"
                            onmouseover="this.style.background='rgba(16,185,129,0.15)'"
                            onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                            <input type="file" wire:model="fileUpload" accept=".json" style="display: none;"
                                wire:change="importPageJson"
                                @change="showImportMenu = false; showLoadingOverlay('Mengimpor JSON...'); showToast('loading', 'Mengimpor halaman dari JSON...')">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 18v-8"/><path d="M9 15l3-3 3 3"/></svg>
                            <div>
                                <div style="color: #f1f5f9; font-size: 13px; font-weight: 600;">Import dari JSON</div>
                                <div style="color: #64748b; font-size: 11px; margin-top: 2px;">Max 5MB • format .json
                                </div>
                            </div>
                        </label>
                        <p style="color: #475569; font-size: 11px; margin: 8px 4px 2px; text-align: center;">
                            <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:4px; vertical-align:middle;"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Data halaman saat ini akan digantikan
                        </p>
                    </div>
                </div>
            </div>

            <div style="width: 1px; height: 24px; background: #293548;"></div>

            {{-- Publish Button --}}
            <button type="button" :disabled="isSaving"
                @click="isSaving = true; unsavedChanges = false; $nextTick(() => document.getElementById('pageForm').dispatchEvent(new Event('submit', {bubbles: true, cancelable: true})))"
                style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border: none; padding: 8px 20px; border-radius: 7px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(99,102,241,0.4);"
                onmouseover="if(!this.disabled){ this.style.boxShadow='0 4px 16px rgba(99,102,241,0.6)'; this.style.transform='translateY(-1px)'; }"
                onmouseout="this.style.boxShadow='0 2px 8px rgba(99,102,241,0.4)'; this.style.transform='translateY(0)';">
                <span x-show="!isSaving"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17v2a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-2"/><path d="M12 4v9"/><path d="M8.5 15.5L12 19l3.5-3.5"/><path d="M5 8l7-5 7 5"/></svg> Publish</span>
                <span x-show="isSaving" style="display: flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="animation: spin 0.8s linear infinite;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Menyimpan...
                </span>
            </button>
        </div>
    </header>

    {{-- ===================== HIDDEN FORM ===================== --}}
    <form id="pageForm" wire:submit.prevent="savePage" style="display: none;">
        <textarea id="htmldata" wire:model="html"></textarea>
        <textarea id="cssdata" wire:model="css"></textarea>
        <input type="text" wire:model="slug">
        <select wire:model="status">
            <option value="Publish">Publish</option>
            <option value="Draft">Draft</option>
        </select>
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
    <div wire:ignore id="gjs-editor-container" style="flex-grow: 1; position: relative; overflow: hidden; background: linear-gradient(180deg, #e2e8f0 0%, #f8fafc 100%); padding: 14px 14px 10px; box-sizing: border-box;">
        <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
        <script src="https://unpkg.com/grapesjs"></script>
        <div id="gjs" style="height: 100% !important; background: #ffffff; border: 1px solid rgba(148, 163, 184, 0.3); border-radius: 14px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12); overflow: hidden;">
            {!! $this->renderPageHtml($html) !!}
        </div>
    </div>

    {{-- ===================== STATUS BAR ===================== --}}
    <div
        style="height: 32px; background: linear-gradient(180deg, #0f172a 0%, #0b1220 100%); border-top: 1px solid rgba(148, 163, 184, 0.18); display: flex; align-items: center; justify-content: space-between; padding: 0 16px; flex-shrink: 0; box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span id="editor-status-components"
                style="color: #475569; font-size: 11px; display: flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M7 12h10M9 17h6"/><path d="M4 7v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7"/></svg> <span
                    id="status-component-count">0</span> komponen
            </span>
            <span style="color: #1e293b;">|</span>
            <span id="editor-status-selected" style="color: #475569; font-size: 11px;">Tidak ada seleksi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="color: #293548; font-size: 11px; display: flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9V5h6l2 3h10v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><path d="M7 9h10"/></svg>
                Ctrl+Z Undo &bull; Ctrl+S Simpan
            </span>
            <span x-show="unsavedChanges"
                style="color: #f59e0b; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="7" height="7" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg> Perubahan belum disimpan
            </span>
            <span x-show="!unsavedChanges"
                style="color: #10b981; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg> Tersimpan
            </span>
        </div>
    </div>

    @push('styles')

    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: 100% !important;
            background: #0b1120;
        }
        .editor-builder-shell {
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #0b1120;
            color: #e2e8f0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

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
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            animation: slideInRight 0.3s ease;
            border-left: 4px solid transparent;
            backdrop-filter: blur(8px);
        }

        .toast-success {
            background: #0f2a1f;
            border-color: #10b981;
            color: #6ee7b7;
        }

        .toast-error {
            background: #2a0f0f;
            border-color: #ef4444;
            color: #fca5a5;
        }

        .toast-info {
            background: #0f1a2a;
            border-color: #6366f1;
            color: #a5b4fc;
        }

        .toast-warning {
            background: #2a1f0f;
            border-color: #f59e0b;
            color: #fcd34d;
        }

        .toast-loading {
            background: #1a1f2e;
            border-color: #475569;
            color: #94a3b8;
        }

        /* Editor header */
        .editor-header-btn {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #cbd5e1;
            cursor: pointer;
            padding: 7px 11px;
            border-radius: 8px;
            transition: all 0.15s ease;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        .editor-header-btn:hover {
            color: #f8fafc;
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(99, 102, 241, 0.45);
            transform: translateY(-1px);
        }

        .editor-header-btn.active {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.15);
        }

        .device-btn {
            border-radius: 6px;
            padding: 7px 9px;
            min-width: 34px;
            justify-content: center;
            border-color: transparent;
            background: transparent;
        }

        .device-btn.active {
            color: #a5b4fc;
            background: rgba(99, 102, 241, 0.18);
            border-color: rgba(99, 102, 241, 0.32);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
        }

        /* GrapesJS override for Elementor-like look */
        #gjs-editor-container .gjs-cv-canvas {
            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%) !important;
        }

        #gjs-editor-container .gjs-frame-wrapper {
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        #gjs-editor-container .gjs-cv-canvas canvas {
            border-radius: 14px;
        }

        /* Left panel tabs */
        .gjs-pn-views-container {
            background: #111827 !important;
            border-left: 1px solid rgba(148, 163, 184, 0.18) !important;
        }

        .gjs-pn-views {
            background: #0f172a !important;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18) !important;
        }

        .gjs-pn-btn {
            color: #94a3b8 !important;
            transition: all 0.15s !important;
            padding: 10px 12px !important;
        }

        .gjs-pn-btn:hover,
        .gjs-pn-btn.gjs-pn-active {
            color: #e2e8f0 !important;
            background: rgba(99, 102, 241, 0.08) !important;
            border-bottom-color: #818cf8 !important;
        }

        /* Block panel */
        .gjs-block-categories {
            background: #1e293b !important;
        }

        .gjs-block-category .gjs-title {
            background: #0f172a !important;
            color: #94a3b8 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
        }

        .gjs-block {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%) !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
            color: #cbd5e1 !important;
            border-radius: 8px !important;
            transition: all 0.15s !important;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        .gjs-block:hover {
            border-color: #6366f1 !important;
            color: #a5b4fc !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2) !important;
        }

        .gjs-block-label {
            font-size: 11px !important;
        }

        /* Style manager */
        .gjs-sm-sector-title {
            background: #0f172a !important;
            color: #94a3b8 !important;
            border-bottom: 1px solid #334155 !important;
            font-size: 11px !important;
            letter-spacing: 1px !important;
        }

        .gjs-sm-sector,
        .gjs-sm-property {
            background: #1e293b !important;
            border-bottom: 1px solid #1e293b !important;
        }

        .gjs-sm-label {
            color: #64748b !important;
            font-size: 11px !important;
        }

        .gjs-sm-field {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            border-radius: 4px !important;
        }

        .gjs-sm-field:focus {
            border-color: #6366f1 !important;
        }

        /* Traits */
        .gjs-trt-trait .gjs-label {
            color: #64748b !important;
            font-size: 11px !important;
        }

        .gjs-trt-trait input,
        .gjs-trt-trait select {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
            border-radius: 4px !important;
        }

        /* Layers */
        .gjs-layer {
            background: #1e293b !important;
            border-bottom: 1px solid #1a2540 !important;
            color: #94a3b8 !important;
        }

        .gjs-layer.gjs-selected {
            color: #a5b4fc !important;
            background: rgba(99, 102, 241, 0.15) !important;
        }

        /* Toolbar */
        .gjs-toolbar {
            background: #4f46e5 !important;
            border-radius: 6px !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4) !important;
        }

        .gjs-toolbar-item {
            color: #fff !important;
        }

        /* Right panel */
        .gjs-pn-panel {
            background: #1e293b !important;
        }

        /* Selector manager */
        .gjs-clm-tags-field {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        .gjs-clm-tag {
            background: #334155 !important;
            color: #94a3b8 !important;
        }

        /* Unsaved dot */
        .unsaved-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #f59e0b;
            display: inline-block;
            margin-left: 4px;
            animation: pulse 1.5s infinite;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>

    @endpush
    
    @push('scripts')
        <script>
            function svgIcon(name, size = 14, stroke = 'currentColor') {
                const icons = {
                    success: '<path d="M20 6L9 17l-5-5"/>',
                    error: '<path d="M18 6L6 18M6 6l12 12"/>',
                    info: '<circle cx="12" cy="12" r="9"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
                    warning: '<path d="M12 3l9 16H3L12 3z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
                    loading: '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>',
                    close: '<path d="M6 6l12 12M18 6L6 18"/>'
                };

                const path = icons[name] || icons.info;
                return `
                    <svg viewBox="0 0 24 24" width="${size}" height="${size}" fill="none" stroke="${stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0; display:block;">
                        ${path}
                    </svg>
                `;
            }

            // ===================== TOAST SYSTEM =====================
            function showToast(type, message, iconClass) {
                const icons = {
                    success: svgIcon('success', 14),
                    error: svgIcon('error', 14),
                    info: svgIcon('info', 14),
                    warning: svgIcon('warning', 14),
                    loading: svgIcon('loading', 14),
                };
                const icon = icons[type] || icons.info;
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `toast-item toast-${type}`;
                toast.innerHTML = `
            ${icon}
            <span style="flex: 1;">${message}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;opacity:0.6;font-size:14px;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                ${svgIcon('close', 12)}
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
                if (overlay) {
                    overlay.style.display = 'flex';
                    if (textEl && text) textEl.textContent = text;
                }
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
                    const type = eventData.icon === 'success' ? 'success' : (eventData.icon === 'error' ? 'error' :
                        'info');
                    showToast(type, eventData.text || eventData.title);

                    // Reset publish button state
                    if (window.Alpine) {
                        const rootEl = document.querySelector('[x-data]');
                        if (rootEl && rootEl._x_dataStack) {
                            try {
                                rootEl._x_dataStack[0].isSaving = false;
                            } catch (e) {}
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
                        document.getElementById('pageForm').dispatchEvent(new Event('submit', {
                            bubbles: true,
                            cancelable: true
                        }));
                        showToast('loading', 'Menyimpan halaman...');
                    }
                });
            });

            if (window.Livewire) registerLivewireEvents();
        </script>
        @include('suryacms::livewire.admin.homepage-builder.scripts')
    @endpush

</div>
