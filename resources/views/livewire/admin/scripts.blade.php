@php
$theme = \Uiaciel\SuryaCms\Models\Setting::find(1)->active_theme ?? config('frontend.active');

// Priority 1: Check published theme
$themePath = resource_path("views/frontend/{$theme}/theme.php");
if (!file_exists($themePath)) {
    // Priority 2: Fallback to package theme
    $themePath = base_path("packages/uiaciel/suryacms/resources/views/frontend/{$theme}/theme.php");
}

$themeData = file_exists($themePath) ? include $themePath : [];
$path_theme = is_array($themeData) && isset($themeData['info']['path']) ? $themeData['info']['path'] : '';
$themeAssets =
is_array($themeData) && isset($themeData['assets']) ? $themeData['assets'] : ['styles' => [], 'scripts' => []];
$customBlocks = is_array($themeData) && isset($themeData['custom_blocks']) ? $themeData['custom_blocks'] : [];
$sections = is_array($themeData) && isset($themeData['sections']) ? $themeData['sections'] : [];
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const path_theme = @json($path_theme);
        const themeAssets = @json($themeAssets);
        const customBlocks = @json($customBlocks);
        const sections = @json($sections);

        const shortcodePreviews = {
            '[[gallery]]': `
            <div class="row text-center p-4">
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
                <div class="col-lg-3 col-md-3">
                    <img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" />
                    <h6>Gallery Preview</h6>
                </div>
            </div>

            `,
            '[[blog]]': `
            <div class="p-3 border bg-light">
                <img src="https://dummyimage.com/300x150?text=Blog+Post" class="img-fluid mb-2" />
                <h6>Blog Posts Preview</h6>
                <p class="small text-muted">Daftar artikel terbaru</p>
            </div>
            `,
            '[[youtube]]': `
            <div class="text-center p-3 border bg-light">
                <img src="https://dummyimage.com/320x180?text=YouTube+Video" class="img-fluid mb-2" />
                <h6>YouTube Preview</h6>
            </div>
            `,

            '[[slider]]': `
            <section id="slider">
                <div class="carousel slide" data-bs-ride="carousel" id="carouselExampleControls">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img alt="..." class="d-block w-100" loading="lazy" src="https://dummyimage.com/1024x700">

                        </div>
                    </div>
                    <button class="carousel-control-prev" data-bs-slide="prev" data-bs-target="#carouselExampleControls" type="button">
                        <span aria-hidden="true" class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" data-bs-slide="next" data-bs-target="#carouselExampleControls" type="button">
                        <span aria-hidden="true" class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </section>
            `
        };

        // Buat peta pratinjau dinamis yang memprioritaskan preview dari theme.php
        const shortcodeMap = {
            ...shortcodePreviews
        }; // Fallback dari shortcodePreviews
        customBlocks.forEach(block => {
            if (block.content.startsWith('[[') && block.preview) {
                shortcodeMap[block.content] = block.preview;
            }
        });

        const config = {
            container: '#gjs',
            fromElement: false,
            height: '100%',
            width: 'auto',
            storageManager: false,
            canvas: {
                shadowRender: true,
                styles: themeAssets.styles.map(style => `/frontend/${path_theme}/${style}`),
                scripts: themeAssets.scripts.map(script => `/frontend/${path_theme}/${script}`),
            },
            styleManager: {
                clearProperties: 1,
            },
        };

        const editor = grapesjs.init(config);

        editor.on('load', () => {
            @if ($html)
                editor.setComponents(@js($html));
                editor.setComponents(@json($html));
            @endif

            @if ($css)
                editor.setStyle(@js($css));
            @endif
        });

        editor.on('update', () => {
            const html = editor.getHtml();
            const css = editor.getCss();

            const htmlInput = document.getElementById('htmldata');
            const cssInput = document.getElementById('cssdata');

            htmlInput.value = html;
            cssInput.value = css;

            htmlInput.dispatchEvent(new Event('input'));
            cssInput.dispatchEvent(new Event('input'));
        });

        // Saat editor selesai load, ubah shortcode murni jadi preview wrapper
        editor.on('load', () => {
        let html = editor.getHtml();
        if (/\[\[(gallery|blog|youtube|slider|contact)\]\]/.test(html) && !html.includes(
        'data-gjs-type="shortcode"')) {
        const transformed = html.replace(/\[\[(gallery|blog|youtube|slider|contact)\]\]/g, (match) => {
        const preview = shortcodeMap[match] || ''; // Gunakan peta pratinjau
        return `<div data-gjs-type="shortcode" data-shortcode="${match}">${preview}</div>`;
        });

        editor.setComponents(transformed);
        }
        });

        customBlocks.forEach(block => {
        const shortcode = block.content.trim();
        const previewHTML = shortcodeMap[shortcode] || '';

        editor.BlockManager.add(block.id, {
        label: block.label,
        category: block.category,
        content: {
        type: 'shortcode',
        attributes: {
        'data-shortcode': shortcode
        },
        content: previewHTML, // Perbaiki: Gunakan HTML pratinjau sebagai konten awal
        }
        });
        });

        // Perbaikan utama: Sesuaikan toHTML dan view
        editor.DomComponents.addType('shortcode', {
        isComponent: el => el && el.getAttribute && el.getAttribute('data-gjs-type') ===
        'shortcode',
        model: {
        defaults: {
        tagName: 'div',
        droppable: false,
        editable: false,
        copyable: false,
        removable: true,
        traits: [], // Pastikan traits kosong jika tidak digunakan
        },
        toHTML() {
        // Ketika disimpan, kembalikan ke shortcode murni
        const shortcode = this.getAttributes()['data-shortcode'] || '';
        return shortcode;
        }
        },
        view: {
        // Karena konten sudah diset di BlockManager, view tidak perlu mengubah innerHTML
        }
        });

        sections.forEach(blocksection => {
            editor.BlockManager.add(blocksection.id, {
                label: blocksection.label,
                category: blocksection.category,
                content: blocksection.content,
            });
        })

        // ====================================
        // HEADING 1
        // ====================================
    editor.Blocks.add('list-item', {
    label: `
    <div style="text-align:center">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
            <rect width="16" height="16" fill="#eaeaea" rx="2" />
            <circle cx="4" cy="6" r="1" fill="#555" />
            <rect x="6" y="5.5" width="7" height="1" fill="#777" />
            <circle cx="4" cy="9" r="1" fill="#555" />
            <rect x="6" y="8.5" width="7" height="1" fill="#777" />
        </svg>
        <div style="margin-top:10px;">List</div>
    </div>
    `,
    category: 'Typography',
    content: `
    <ul class="mb-3">
        <li>First list item</li>
        <li>Second list item</li>
        <li>Third list item</li>
    </ul>
    `,
    });

        editor.Blocks.add('paragraph', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="3" y="5" width="10" height="1" fill="#555" />
                <rect x="3" y="8" width="8" height="1" fill="#777" />
                <rect x="3" y="11" width="9" height="1" fill="#999" />
            </svg>
            <div style="margin-top:10px;">Text / Paragraph</div>
        </div>
        `,
        category: 'Typography',
        content: `<p class="mb-3">This is a paragraph block. You can edit this text directly.</p>`,
        });

        editor.Blocks.add('heading-1', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 1</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h1 class="fw-bold mb-3">Your Heading Here</h1>`,
        });

        // ====================================
        // HEADING 2
        // ====================================
        editor.Blocks.add('heading-2', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 2</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h2 class="fw-bold mb-3">Your Heading Here</h2>`,
        });

        // ====================================
        // HEADING 3
        // ====================================
        editor.Blocks.add('heading-3', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 3</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h3 class="fw-semibold mb-3">Your Heading Here</h3>`,
        });

        // ====================================
        // HEADING 4
        // ====================================
        editor.Blocks.add('heading-4', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 4</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h4 class="fw-semibold mb-3">Your Heading Here</h4>`,
        });

        // ====================================
        // HEADING 5
        // ====================================
        editor.Blocks.add('heading-5', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 5</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h5 class="fw-medium mb-3">Your Heading Here</h5>`,
        });

        // ====================================
        // HEADING 6
        // ====================================
        editor.Blocks.add('heading-6', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">H</text>
            </svg>
            <div style="margin-top:10px;">Heading 6</div>
        </div>
        `,
        category: 'Typography',
        media: '',
        content: `<h6 class="fw-normal mb-3 text-uppercase">Your Heading Here</h6>`,
        });

        editor.Blocks.add('youtube-video-bootstrap', {
             label: `
             <div style="text-align:center">
                 <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                     <rect width="16" height="16" fill="#eaeaea" rx="2" />
                     <polygon points="6,5 11,8 6,11" fill="#555" />
                 </svg>
                 <div style="margin-top:10px;">YouTube Video</div>
             </div>
             `,

            category: 'Media',
            content: `
        <div class="ratio ratio-16x9">
          <iframe
            src="https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
          ></iframe>
        </div>
      `,
        });

        editor.Blocks.add('image-box', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <path d="M3 9l3-3 2 2 3-3 3 3v4H3V9z" fill="#555" />
                <rect x="3" y="12" width="10" height="1" fill="#777" />
            </svg>
            <div style="margin-top:10px;">Image Box</div>
        </div>
        `,
        category: 'Media',
        content: `
        <div class="text-center p-3">
            <img src="https://dummyimage.com/400x250" alt="Image Box" class="img-fluid rounded mb-3 shadow-sm">
            <h4 class="fw-bold mb-2">Title Here</h4>
            <p class="text-muted">Add a short description here to explain your feature or service.</p>
        </div>
        `,
        });

        editor.Blocks.add('gallery-grid', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="2" y="3" width="4" height="4" fill="#555" />
                <rect x="7" y="3" width="4" height="4" fill="#777" />
                <rect x="2" y="8" width="4" height="4" fill="#999" />
                <rect x="7" y="8" width="4" height="4" fill="#555" />
            </svg>
            <div style="margin-top:10px;">Gallery Grid</div>
        </div>
        `,
        category: 'Media',
        content: `
        <div class="container py-3">
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm" alt="Gallery Image 1">
                </div>
                <div class="col-6 col-md-4">
                    <img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm" alt="Gallery Image 2">
                </div>
                <div class="col-6 col-md-4">
                    <img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm" alt="Gallery Image 3">
                </div>
            </div>
        </div>
        `,
        });

        editor.Blocks.add('image', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <path d="M2 12l3-3 2 2 3-3 4 4v1H2v-1z" fill="#555" />
                <circle cx="5" cy="5" r="1.5" fill="#777" />
            </svg>
            <div style="margin-top:10px;">Image</div>
        </div>
        `,
        category: 'Media',
        content: `
        <div class="text-center">
            <img src="https://dummyimage.com/800x500" alt="Image" class="img-fluid rounded shadow-sm mb-3">
        </div>
        `,
        });

        // ====================================
        // 1. SECTION
        // ====================================
        editor.Blocks.add('section', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="1" y="3" width="14" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">Section</div>
        </div>
        `,
        category: 'Layout',
        media: '',
        content: `
        <section class="container py-5">
        <h2>Section Title</h2>

        </section>
        `,
        });

        editor.Blocks.add('spacer', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <path d="M4 6h8v4H4z" fill="#ccc" />
                <path d="M8 3v2M8 11v2" stroke="#555" stroke-width="1" />
            </svg>
            <div style="margin-top:10px;">Spacer</div>
        </div>
        `,
        category: 'Layout',
        content: `<div class="my-5"></div>`,
        traits: [
        {
        type: 'number',
        label: 'Height (px)',
        name: 'height',
        changeProp: 1,
        },
        ],
        init() {
        this.on('change:height', (model) => {
        const val = model.get('height') || 80;
        model.set('content', `<div style="height:${val}px"></div>`);
        });
        },
        });

        // ====================================
        // 2. ROW - 1 COLUMN
        // ====================================
        editor.Blocks.add('row-1col', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="2" y="3" width="12" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">1 Column</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row">
            <div class="col-12">
                <p>1 Column content here...</p>
            </div>
        </div>
        `,
        });

        // ====================================
        // 3. ROW - 2 COLUMNS
        // ====================================
        editor.Blocks.add('row-2col', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="2" y="3" width="5" height="10" fill="#ccc" rx="1" />
                <rect x="9" y="3" width="5" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">2 Columns</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row">
            <div class="col-md-6">
                <p>Left column content...</p>
            </div>
            <div class="col-md-6">
                <p>Right column content...</p>
            </div>
        </div>
        `,
        });

        // ====================================
        // 4. ROW - 3 COLUMNS
        // ====================================
        editor.Blocks.add('row-3col', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="1.5" y="3" width="4" height="10" fill="#ccc" rx="1" />
                <rect x="6" y="3" width="4" height="10" fill="#ccc" rx="1" />
                <rect x="10.5" y="3" width="4" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">3 Columns</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row">
            <div class="col-md-4">
                <p>Column 1</p>
            </div>
            <div class="col-md-4">
                <p>Column 2</p>
            </div>
            <div class="col-md-4">
                <p>Column 3</p>
            </div>
        </div>
        `,
        });

        // ====================================
        // 5. ROW - 4 COLUMNS
        // ====================================
        editor.Blocks.add('row-4col', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="1" y="3" width="3" height="10" fill="#ccc" rx="1" />
                <rect x="5" y="3" width="3" height="10" fill="#ccc" rx="1" />
                <rect x="9" y="3" width="3" height="10" fill="#ccc" rx="1" />
                <rect x="13" y="3" width="2" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">4 Columns</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row text-center">
            <div class="col-md-3">
                <p>Col 1</p>
            </div>
            <div class="col-md-3">
                <p>Col 2</p>
            </div>
            <div class="col-md-3">
                <p>Col 3</p>
            </div>
            <div class="col-md-3">
                <p>Col 4</p>
            </div>
        </div>
        `,
        });

        // ====================================
        // 6. ROW - SIDEBAR LEFT
        // ====================================
        editor.Blocks.add('row-sidebar-left', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="1.5" y="3" width="4" height="10" fill="#bbb" rx="1" />
                <rect x="6.5" y="3" width="8" height="10" fill="#ccc" rx="1" />
            </svg>
            <div style="margin-top:10px;">Sidebar Left</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row">
            <aside class="col-md-4 bg-light p-3">
                <p>Sidebar content...</p>
            </aside>
            <main class="col-md-8">
                <p>Main content area...</p>
            </main>
        </div>
        `,
        });

        // ====================================
        // 7. ROW - SIDEBAR RIGHT
        // ====================================
        editor.Blocks.add('row-sidebar-right', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="1.5" y="3" width="8" height="10" fill="#ccc" rx="1" />
                <rect x="10.5" y="3" width="4" height="10" fill="#bbb" rx="1" />
            </svg>
            <div style="margin-top:10px;">Sidebar Right</div>
        </div>
        `,
        category: 'Column',
        media: '',
        content: `
        <div class="row">
            <main class="col-md-8">
                <p>Main content area...</p>
            </main>
            <aside class="col-md-4 bg-light p-3">
                <p>Sidebar content...</p>
            </aside>
        </div>
        `,
        });

        editor.DomComponents.addType('youtube-bootstrap-embed', {
            model: {
                defaults: {
                    tagName: 'div',
                    classes: ['ratio', 'ratio-16x9'],
                    components: [{
                        tagName: 'iframe',
                        attributes: {
                            src: 'https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID',
                            title: 'YouTube video player',
                            frameborder: '0',
                            allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
                            allowfullscreen: true,
                        },
                        style: {
                            position: 'absolute',
                            top: '0',
                            left: '0',
                            width: '100%',
                            height: '100%',
                        },
                    }],
                    traits: [{
                            type: 'text',
                            label: 'Video ID',
                            name: 'videoId',
                        },
                        {
                            type: 'select',
                            label: 'Ratio',
                            name: 'ratio',
                            options: [{
                                    value: 'ratio-16x9',
                                    name: '16:9'
                                },
                                {
                                    value: 'ratio-4x3',
                                    name: '4:3'
                                },
                                {
                                    value: 'ratio-1x1',
                                    name: '1:1'
                                },
                                {
                                    value: 'ratio-21x9',
                                    name: '21:9'
                                },
                            ],
                        },
                    ],
                },
                init() {
                    this.on('change:videoId', this.updateVideo);
                    this.on('change:ratio', this.updateRatio);
                },
                updateVideo() {
                    const videoId = this.get('videoId');
                    const iframe = this.components().models[0];
                    if (iframe) {
                        iframe.set('attributes', {
                            src: `https://www.youtube.com/embed/${videoId}`,
                        });
                    }
                },
                updateRatio() {
                    const ratio = this.get('ratio');
                    this.get('classes').forEach(cls => {
                        if (cls.startsWith('ratio-')) {
                            this.removeClass(cls);
                        }
                    });
                    this.addClass(ratio);
                },
            },
        });

        editor.Blocks.add('button', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <rect x="3" y="7" width="10" height="2" fill="#555" rx="1" />
            </svg>
            <div style="margin-top:10px;">Button</div>
        </div>
        `,

        category: 'Components',

        content: {
        type: 'button',
        content: 'Click Me',
        classes: ['btn', 'btn-primary'],
        attributes: { href: '#', type: 'button' },
        }
        });

        // --- Customize Button Component Traits ---
        editor.DomComponents.addType('button', {
        model: {
        defaults: {
        traits: [
        {
        name: 'content',
        label: 'Text',
        type: 'text',
        changeProp: 1,
        },
        {
        name: 'href',
        label: 'URL',
        type: 'text',
        placeholder: 'https://example.com',
        changeProp: 1,
        },
        {
        name: 'target',
        label: 'Target',
        type: 'select',
        options: [
        { id: '_self', name: 'Same tab' },
        { id: '_blank', name: 'New tab' },
        ],
        changeProp: 1,
        },
        {
        name: 'class',
        label: 'Style',
        type: 'select',
        options: [
        { id: 'btn btn-primary', name: 'Primary' },
        { id: 'btn btn-secondary', name: 'Secondary' },
        { id: 'btn btn-outline-primary', name: 'Outline' },
        { id: 'btn btn-success', name: 'Success' },
        { id: 'btn btn-danger', name: 'Danger' },
        ],
        changeProp: 1,
        }
        ]
        }
        }
        });

        editor.Blocks.add('link', {
        label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">A</text>
            </svg>
            <div style="margin-top:10px;">Link</div>
        </div>`,
        category: 'Components',

        content: {
        type: 'link',
        content: 'Sample Link',
        attributes: { href: '#', target: '_self' },
        }
        });

        // --- Customize Link Component Traits ---
        editor.DomComponents.addType('link', {
        model: {
        defaults: {
        traits: [
        {
        name: 'content',
        label: 'Text',
        type: 'text',
        changeProp: 1,
        },
        {
        name: 'href',
        label: 'URL',
        type: 'text',
        placeholder: 'https://example.com',
        changeProp: 1,
        },
        {
        name: 'target',
        label: 'Target',
        type: 'select',
        options: [
        { id: '_self', name: 'Same tab' },
        { id: '_blank', name: 'New tab' },
        ],
        changeProp: 1,
        },
        {
        name: 'class',
        label: 'Style',
        type: 'select',
        options: [
        { id: 'link-primary', name: 'Primary' },
        { id: 'link-secondary', name: 'Secondary' },
        { id: 'text-decoration-none', name: 'No underline' },
        ],
        changeProp: 1,
        },
        ]
        }
        }
        });

        // Block untuk Card Bootstrap Lengkap
        editor.Blocks.add('bootstrap-card-full', {
            label: `
        <div style="text-align:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                <path d="M1 4a1 1 0 011-1h12a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zM2 5v6h12V5H2zm2 1h2v2H4V6zm4 0h4v2H8V6z" fill="#555"/>
                <circle cx="12" cy="10" r="1" fill="#777"/>
            </svg>
            <div style="margin-top:10px;">Product Card</div>
        </div>
        `,
            category: 'Components',
            content: `
        <div class="card shadow-sm border-0 text-center p-3" style="max-width: 300px; background: #fff;">
            <img src="https://dummyimage.com/300x200" class="card-img-top img-fluid rounded-3" alt="Product Image">
            <div class="card-body">
                <span class="badge bg-secondary mb-2">Kategori</span>
                <h5 class="card-title mb-2">Nama Produk</h5>
                <p class="mb-1 text-muted text-decoration-line-through">Rp 150.000</p>
                <p class="fs-5 fw-bold text-success mb-3">Rp 99.000</p>
                <a href="" class="btn btn-success w-100">
                    <i class="bi bi-whatsapp me-2"></i> Pesan via WhatsApp
                </a>
            </div>
        </div>
        `,
        });

        editor.BlockManager.add('counter-section', {
            label: `
            <div style="text-align:center">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                    <rect width="16" height="16" fill="#eaeaea" rx="2" />
                    <path d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM8 13A4.5 4.5 0 118 4a4.5 4.5 0 010 9z" fill="#555"/>
                    <path d="M8 4.5a.5.5 0 01.5.5v3a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z" fill="#777"/>
                </svg>
                <div style="margin-top:10px;">Counter Section</div>
            </div>
            `,
            category: 'Components',
            content: `
            <section class="py-5 bg-light text-center">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="p-4 bg-white rounded rounded-5 shadow">
                                <h2 data-gjs-type="counter-number" class="text-primary counter" data-target="44">0</h2>
                                <p class="fw-bold text-dark" data-gjs-editable="true">Pengajar/GTK</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 bg-white rounded rounded-5 shadow">
                                <h2 data-gjs-type="counter-number" class="text-primary counter" data-target="13">0</h2>
                                <p class="fw-bold text-dark" data-gjs-editable="true">Staff/PTK</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 bg-white rounded rounded-5 shadow">
                                <h2 data-gjs-type="counter-number" class="text-primary counter" data-target="546">0</h2>
                                <p class="fw-bold text-dark" data-gjs-editable="true">Santri</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-4 bg-white rounded rounded-5 shadow">
                                <h2 data-gjs-type="counter-number" class="text-primary counter" data-target="0">0</h2>
                                <p class="fw-bold text-dark" data-gjs-editable="true">Alumni</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            `,
        });

        editor.DomComponents.addType('counter-number', {
            model: {
                defaults: {
                    // Ini akan mengganti elemen h2 secara otomatis
                    tagName: 'h2',
                    classes: ['text-success', 'counter'],
                    // Traits yang akan muncul di panel settings
                    traits: [{
                        type: 'number',
                        label: 'Jumlah',
                        name: 'data-target',
                        min: 0,
                    }],
                    // Fungsi init() akan memantau perubahan pada traits
                    init() {
                        this.on('change:data-target', this.updateContent);
                    },
                    updateContent() {
                        // Ambil nilai data-target dari traits
                        const dataTarget = this.get('data-target');
                        // Perbarui konten teks elemen <h2>
                        this.set('content', dataTarget);
                    }
                },
            },
        });

    });
</script>
