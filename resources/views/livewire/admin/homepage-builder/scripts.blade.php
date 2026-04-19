@php
    $theme = \Uiaciel\SuryaCms\Models\Setting::find(1)->active_theme ?? config('frontend.active');
    $themePath = resource_path("views/frontend/{$theme}/theme.php");
        if (!file_exists($themePath)) {
            $themePath = base_path("packages/uiaciel/suryacms/resources/views/frontend/{$theme}/theme.php");
        }
    $themeData = file_exists($themePath) ? include $themePath : [];
    $path_theme = is_array($themeData) && isset($themeData['info']['path']) ? $themeData['info']['path'] : '';
    $themeAssets = is_array($themeData) && isset($themeData['assets']) ? $themeData['assets'] : ['styles' => [], 'scripts' => []];
    $customBlocks = is_array($themeData) && isset($themeData['custom_blocks']) ? $themeData['custom_blocks'] : [];
    $sections = is_array($themeData) && isset($themeData['sections']) ? $themeData['sections'] : [];

    $themeStyle = is_array($themeData) && isset($themeData['info']['style']) ? $themeData['info']['style'] : 'bootstrap';
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const path_theme = @json($path_theme);
        const themeAssets = @json($themeAssets);
        const customBlocks = @json($customBlocks);
        const sections = @json($sections);
        const themeStyle = @json($themeStyle);

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

        const shortcodeMap = {
            ...shortcodePreviews
        };

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
                styles: themeAssets.styles.map(style => {
                    // Jika sudah URL lengkap (http/https) atau path absolute (/)
                    if (style.startsWith('http') || style.startsWith('/')) {
                        return style;
                    }
                    // Jika path relatif, prefix dengan theme
                    return `/frontend/${path_theme}/${style}`;
                }),

                scripts: themeAssets.scripts.map(script => {
                    // Jika sudah URL lengkap (http/https)
                    if (script.startsWith('http')) {
                        return script;
                    }
                    // Jika path relatif, prefix dengan theme
                    return `/frontend/${path_theme}/${script}`;
                }),
            },
            styleManager: {
                clearProperties: 1,
            },
        };

        const editor = grapesjs.init(config);

        let isInitialLoad = true;

        editor.on('load', () => {
            if (isInitialLoad) {
                isInitialLoad = false;

                @if ($html)
                    editor.setComponents(@js($html));
                @endif

                @if ($css)
                    editor.setStyle(@js($css));
                @endif

                // Transform shortcodes setelah komponen diset
                setTimeout(() => {
                    let html = editor.getHtml();
                    if (/\[\[(announcement|gallery|blog|youtube|slider|contact|report|stock)\]\]/.test(html) && !html.includes('data-gjs-type="shortcode"')) {
                        const transformed = html.replace(/\[\[(announcement|gallery|blog|youtube|slider|contact|report|stock)\]\]/g, (match) => {
                            const preview = shortcodeMap[match] || '';
                            return `<div data-gjs-type="shortcode" data-shortcode="${match}">${preview}</div>`;
                        });
                        editor.setComponents(transformed);
                    }
                }, 100);
            }
        });

        editor.on('update', () => {
            const html = editor.getHtml();
            const css = editor.getCss();

            const htmlInput = document.getElementById('htmldata');
            const cssInput = document.getElementById('cssdata');

            htmlInput.value = html;
            cssInput.value = css;

            htmlInput.dispatchEvent(new Event('input', { bubbles: true }));
            cssInput.dispatchEvent(new Event('input', { bubbles: true }));
        });

        customBlocks.forEach(block => {
        const shortcode = block.content.trim();
        const previewHTML = shortcodeMap[shortcode] || '';

            editor.BlockManager.add(block.id, {
                label: block.label,
                category: block.category,
                content: {
                    type: "shortcode",
                    attributes: {
                        "data-shortcode": shortcode,
                    },
                    content: previewHTML,
                },
            });
        });

        editor.DomComponents.addType("shortcode", {
            isComponent: (el) => el && el.getAttribute && el.getAttribute("data-gjs-type") === "shortcode",
            model: {
                defaults: {
                    tagName: "div",
                    droppable: false,
                    editable: false,
                    copyable: false,
                    removable: true,
                    traits: [],
                },
                toHTML() {
                    // Simpan shortcode dalam format murni, akan dikonversi saat ditampilkan
                    const shortcode = this.getAttributes()["data-shortcode"] || "";
                    return shortcode;
                },
                setContent(content) {
                    // Prevent content changes
                    return this;
                },
            },
            view: {},
        });

        sections.forEach(blocksection => {
            editor.BlockManager.add(blocksection.id, {
                label: blocksection.label,
                category: blocksection.category,
                content: blocksection.content,
            });
        });

        function addBootstrapBlocks(editor) {
            editor.Blocks.add("youtube-video-bootstrap", {
                label: `
                    <div style="text-align:center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                            <rect width="16" height="16" fill="#eaeaea" rx="2" />
                            <polygon points="6,5 11,8 6,11" fill="#555" />
                        </svg>
                        <div style="margin-top:10px;">YouTube Video</div>
                    </div>
                    `,

                category: "Media",
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

            editor.Blocks.add("image-box", {
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
                category: "Media",
                content: `
                        <div class="text-center p-3">
                            <img src="https://dummyimage.com/400x250" alt="Image Box" class="img-fluid rounded mb-3 shadow-sm">
                            <h4 class="fw-bold mb-2">Title Here</h4>
                            <p class="text-muted">Add a short description here to explain your feature or service.</p>
                        </div>
                        `,
            });

            editor.Blocks.add("gallery-grid", {
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
                category: "Media",
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

            editor.Blocks.add("image", {
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
                category: "Media",
                content: `
                        <div class="text-center">
                            <img src="https://dummyimage.com/800x500" alt="Image" class="img-fluid rounded shadow-sm mb-3">
                        </div>
                        `,
            });

            editor.Blocks.add("section", {
                label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="1" y="3" width="14" height="10" fill="#ccc" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">Section</div>
                        </div>
                        `,
                category: "Layout",
                media: "",
                content: `
                        <section class="container py-5">
                        <h2>Section Title</h2>

                        </section>
                        `,
            });

            editor.Blocks.add("spacer", {
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
                category: "Layout",
                content: `<div class="my-5"></div>`,
                traits: [
                    {
                        type: "number",
                        label: "Height (px)",
                        name: "height",
                        changeProp: 1,
                    },
                ],
                init() {
                    this.on("change:height", (model) => {
                        const val = model.get("height") || 80;
                        model.set("content", `<div style="height:${val}px"></div>`);
                    });
                },
            });

            editor.Blocks.add("row-1col", {
                label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="2" y="3" width="12" height="10" fill="#ccc" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">1 Column</div>
                        </div>
                        `,
                category: "Column",
                media: "",
                content: `
                        <div class="row">
                            <div class="col-12">
                                <p>1 Column content here...</p>
                            </div>
                        </div>
                        `,
            });

            editor.Blocks.add("row-2col", {
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
                category: "Column",
                media: "",
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

            editor.Blocks.add("row-3col", {
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
                category: "Column",
                media: "",
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

            editor.Blocks.add("row-4col", {
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
                category: "Column",
                media: "",
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
            editor.Blocks.add("row-sidebar-left", {
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
                category: "Column",
                media: "",
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
            editor.Blocks.add("row-sidebar-right", {
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
                category: "Column",
                media: "",
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

            editor.DomComponents.addType("youtube-bootstrap-embed", {
                model: {
                    defaults: {
                        tagName: "div",
                        classes: ["ratio", "ratio-16x9"],
                        components: [
                            {
                                tagName: "iframe",
                                attributes: {
                                    src: "https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID",
                                    title: "YouTube video player",
                                    frameborder: "0",
                                    allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share",
                                    allowfullscreen: true,
                                },
                                style: {
                                    position: "absolute",
                                    top: "0",
                                    left: "0",
                                    width: "100%",
                                    height: "100%",
                                },
                            },
                        ],
                        traits: [
                            {
                                type: "text",
                                label: "Video ID",
                                name: "videoId",
                            },
                            {
                                type: "select",
                                label: "Ratio",
                                name: "ratio",
                                options: [
                                    {
                                        value: "ratio-16x9",
                                        name: "16:9",
                                    },
                                    {
                                        value: "ratio-4x3",
                                        name: "4:3",
                                    },
                                    {
                                        value: "ratio-1x1",
                                        name: "1:1",
                                    },
                                    {
                                        value: "ratio-21x9",
                                        name: "21:9",
                                    },
                                ],
                            },
                        ],
                    },
                    init() {
                        this.on("change:videoId", this.updateVideo);
                        this.on("change:ratio", this.updateRatio);
                    },
                    updateVideo() {
                        const videoId = this.get("videoId");
                        const iframe = this.components().models[0];
                        if (iframe) {
                            iframe.set("attributes", {
                                src: `https://www.youtube.com/embed/${videoId}`,
                            });
                        }
                    },
                    updateRatio() {
                        const ratio = this.get("ratio");
                        this.get("classes").forEach((cls) => {
                            if (cls.startsWith("ratio-")) {
                                this.removeClass(cls);
                            }
                        });
                        this.addClass(ratio);
                    },
                },
            });

            editor.Blocks.add("button", {
                label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="3" y="7" width="10" height="2" fill="#555" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">Button</div>
                        </div>
                        `,

                category: "Components",

                content: {
                    type: "button",
                    content: "Click Me",
                    classes: ["btn", "btn-primary"],
                    attributes: { href: "#", type: "button" },
                },
            });

            // --- Customize Button Component Traits ---
            editor.DomComponents.addType("button", {
                model: {
                    defaults: {
                        traits: [
                            {
                                name: "content",
                                label: "Text",
                                type: "text",
                                changeProp: 1,
                            },
                            {
                                name: "href",
                                label: "URL",
                                type: "text",
                                placeholder: "https://example.com",
                                changeProp: 1,
                            },
                            {
                                name: "target",
                                label: "Target",
                                type: "select",
                                options: [
                                    { id: "_self", name: "Same tab" },
                                    { id: "_blank", name: "New tab" },
                                ],
                                changeProp: 1,
                            },
                            {
                                name: "class",
                                label: "Style",
                                type: "select",
                                options: [
                                    { id: "btn btn-primary", name: "Primary" },
                                    { id: "btn btn-secondary", name: "Secondary" },
                                    { id: "btn btn-outline-primary", name: "Outline" },
                                    { id: "btn btn-success", name: "Success" },
                                    { id: "btn btn-danger", name: "Danger" },
                                ],
                                changeProp: 1,
                            },
                        ],
                    },
                },
            });

            editor.Blocks.add("link", {
                label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">A</text>
                            </svg>
                            <div style="margin-top:10px;">Link</div>
                        </div>`,
                category: "Components",

                content: {
                    type: "link",
                    content: "Sample Link",
                    attributes: { href: "#", target: "_self" },
                },
            });

            // --- Customize Link Component Traits ---
            editor.DomComponents.addType("link", {
                model: {
                    defaults: {
                        traits: [
                            {
                                name: "content",
                                label: "Text",
                                type: "text",
                                changeProp: 1,
                            },
                            {
                                name: "href",
                                label: "URL",
                                type: "text",
                                placeholder: "https://example.com",
                                changeProp: 1,
                            },
                            {
                                name: "target",
                                label: "Target",
                                type: "select",
                                options: [
                                    { id: "_self", name: "Same tab" },
                                    { id: "_blank", name: "New tab" },
                                ],
                                changeProp: 1,
                            },
                            {
                                name: "class",
                                label: "Style",
                                type: "select",
                                options: [
                                    { id: "link-primary", name: "Primary" },
                                    { id: "link-secondary", name: "Secondary" },
                                    { id: "text-decoration-none", name: "No underline" },
                                ],
                                changeProp: 1,
                            },
                        ],
                    },
                },
            });

            // Block untuk Card Bootstrap Lengkap
            editor.Blocks.add("bootstrap-card-full", {
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
                category: "Components",
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

            editor.BlockManager.add("counter-section", {
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
                category: "Components",
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

            editor.DomComponents.addType("counter-number", {
                model: {
                    defaults: {
                        // Ini akan mengganti elemen h2 secara otomatis
                        tagName: "h2",
                        classes: ["text-success", "counter"],
                        // Traits yang akan muncul di panel settings
                        traits: [
                            {
                                type: "number",
                                label: "Jumlah",
                                name: "data-target",
                                min: 0,
                            },
                        ],
                        // Fungsi init() akan memantau perubahan pada traits
                        init() {
                            this.on("change:data-target", this.updateContent);
                        },
                        updateContent() {
                            // Ambil nilai data-target dari traits
                            const dataTarget = this.get("data-target");
                            // Perbarui konten teks elemen <h2>
                            this.set("content", dataTarget);
                        },
                    },
                },
            });
        }

        function addTailwindBlocks(editor) {
                // ====================================
                // MEDIA BLOCKS
                // ====================================
                editor.Blocks.add("youtube-video-tailwind", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <polygon points="6,5 11,8 6,11" fill="#555" />
                            </svg>
                            <div style="margin-top:10px;">YouTube Video</div>
                        </div>
                    `,
                    category: "Media",
                    content: `
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe
                                src="https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                class="w-full h-full"
                            ></iframe>
                        </div>
                    `,
                });

                editor.Blocks.add("image-box", {
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
                    category: "Media",
                    content: `
                        <div class="text-center p-4">
                            <img src="https://dummyimage.com/400x250" alt="Image Box" class="w-full rounded-lg mb-3 shadow-sm">
                            <h4 class="font-bold text-xl mb-2">Title Here</h4>
                            <p class="text-gray-500">Add a short description here to explain your feature or service.</p>
                        </div>
                    `,
                });

                editor.Blocks.add("gallery-grid", {
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
                    category: "Media",
                    content: `
                        <div class="container mx-auto py-3 px-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div>
                                    <img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm" alt="Gallery Image 1">
                                </div>
                                <div>
                                    <img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm" alt="Gallery Image 2">
                                </div>
                                <div>
                                    <img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm" alt="Gallery Image 3">
                                </div>
                            </div>
                        </div>
                    `,
                });

                editor.Blocks.add("image", {
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
                    category: "Media",
                    content: `
                        <div class="text-center">
                            <img src="https://dummyimage.com/800x500" alt="Image" class="w-full rounded-lg shadow-sm mb-3">
                        </div>
                    `,
                });

                // ====================================
                // LAYOUT BLOCKS
                // ====================================
                editor.Blocks.add("section", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="1" y="3" width="14" height="10" fill="#ccc" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">Section</div>
                        </div>
                    `,
                    category: "Layout",
                    media: "",
                    content: `
                        <section class="container mx-auto py-12 px-4">
                            <h2 class="text-3xl font-bold">Section Title</h2>
                        </section>
                    `,
                });

                editor.Blocks.add("spacer", {
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
                    category: "Layout",
                    content: `<div class="my-12"></div>`,
                    traits: [
                        {
                            type: "number",
                            label: "Height (px)",
                            name: "height",
                            changeProp: 1,
                        },
                    ],
                    init() {
                        this.on("change:height", (model) => {
                            const val = model.get("height") || 80;
                            model.set("content", `<div style="height:${val}px"></div>`);
                        });
                    },
                });

                // ====================================
                // COLUMN BLOCKS
                // ====================================
                editor.Blocks.add("row-1col", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="2" y="3" width="12" height="10" fill="#ccc" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">1 Column</div>
                        </div>
                    `,
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1">
                            <div>
                                <p>1 Column content here...</p>
                            </div>
                        </div>
                    `,
                });

                editor.Blocks.add("row-2col", {
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
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p>Left column content...</p>
                            </div>
                            <div>
                                <p>Right column content...</p>
                            </div>
                        </div>
                    `,
                });

                editor.Blocks.add("row-3col", {
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
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p>Column 1</p>
                            </div>
                            <div>
                                <p>Column 2</p>
                            </div>
                            <div>
                                <p>Column 3</p>
                            </div>
                        </div>
                    `,
                });

                editor.Blocks.add("row-4col", {
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
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p>Col 1</p>
                            </div>
                            <div>
                                <p>Col 2</p>
                            </div>
                            <div>
                                <p>Col 3</p>
                            </div>
                            <div>
                                <p>Col 4</p>
                            </div>
                        </div>
                    `,
                });

                editor.Blocks.add("row-sidebar-left", {
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
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <aside class="md:col-span-1 bg-gray-100 p-4 rounded">
                                <p>Sidebar content...</p>
                            </aside>
                            <main class="md:col-span-2">
                                <p>Main content area...</p>
                            </main>
                        </div>
                    `,
                });

                editor.Blocks.add("row-sidebar-right", {
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
                    category: "Column",
                    media: "",
                    content: `
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <main class="md:col-span-2">
                                <p>Main content area...</p>
                            </main>
                            <aside class="md:col-span-1 bg-gray-100 p-4 rounded">
                                <p>Sidebar content...</p>
                            </aside>
                        </div>
                    `,
                });

                // ====================================
                // COMPONENTS
                // ====================================
                editor.DomComponents.addType("youtube-tailwind-embed", {
                    model: {
                        defaults: {
                            tagName: "div",
                            classes: ["aspect-w-16", "aspect-h-9"],
                            components: [
                                {
                                    tagName: "iframe",
                                    attributes: {
                                        src: "https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID",
                                        title: "YouTube video player",
                                        frameborder: "0",
                                        allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share",
                                        allowfullscreen: true,
                                    },
                                    style: {
                                        position: "absolute",
                                        top: "0",
                                        left: "0",
                                        width: "100%",
                                        height: "100%",
                                    },
                                },
                            ],
                            traits: [
                                {
                                    type: "text",
                                    label: "Video ID",
                                    name: "videoId",
                                },
                                {
                                    type: "select",
                                    label: "Ratio",
                                    name: "ratio",
                                    options: [
                                        { value: "aspect-w-16 aspect-h-9", name: "16:9" },
                                        { value: "aspect-w-4 aspect-h-3", name: "4:3" },
                                        { value: "aspect-w-1 aspect-h-1", name: "1:1" },
                                        { value: "aspect-w-21 aspect-h-9", name: "21:9" },
                                    ],
                                },
                            ],
                        },
                        init() {
                            this.on("change:videoId", this.updateVideo);
                            this.on("change:ratio", this.updateRatio);
                        },
                        updateVideo() {
                            const videoId = this.get("videoId");
                            const iframe = this.components().models[0];
                            if (iframe) {
                                iframe.set("attributes", {
                                    src: `https://www.youtube.com/embed/${videoId}`,
                                });
                            }
                        },
                        updateRatio() {
                            const ratio = this.get("ratio");
                            this.get("classes").forEach((cls) => {
                                if (cls.startsWith("aspect-")) {
                                    this.removeClass(cls);
                                }
                            });
                            ratio.split(" ").forEach(c => this.addClass(c));
                        },
                    },
                });

                editor.Blocks.add("button", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <rect x="3" y="7" width="10" height="2" fill="#555" rx="1" />
                            </svg>
                            <div style="margin-top:10px;">Button</div>
                        </div>
                    `,
                    category: "Components",
                    content: {
                        type: "button",
                        content: "Click Me",
                        classes: ["bg-blue-600", "hover:bg-blue-700", "text-white", "font-bold", "py-2", "px-4", "rounded"],
                        attributes: { href: "#", type: "button" },
                    },
                });

                editor.DomComponents.addType("button", {
                    model: {
                        defaults: {
                            traits: [
                                { name: "content", label: "Text", type: "text", changeProp: 1 },
                                { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                                {
                                    name: "target",
                                    label: "Target",
                                    type: "select",
                                    options: [
                                        { id: "_self", name: "Same tab" },
                                        { id: "_blank", name: "New tab" },
                                    ],
                                    changeProp: 1,
                                },
                                {
                                    name: "class",
                                    label: "Style",
                                    type: "select",
                                    options: [
                                        { id: "bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded", name: "Primary" },
                                        { id: "bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded", name: "Secondary" },
                                        { id: "bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded", name: "Outline" },
                                        { id: "bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded", name: "Success" },
                                        { id: "bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded", name: "Danger" },
                                    ],
                                    changeProp: 1,
                                },
                            ],
                        },
                    },
                });

                editor.Blocks.add("link", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <text x="3" y="11" font-size="8" font-family="Arial" fill="#555">A</text>
                            </svg>
                            <div style="margin-top:10px;">Link</div>
                        </div>`,
                    category: "Components",
                    content: {
                        type: "link",
                        content: "Sample Link",
                        attributes: { href: "#", target: "_self" },
                        classes: ["text-blue-600", "hover:text-blue-800", "underline"],
                    },
                });

                editor.DomComponents.addType("link", {
                    model: {
                        defaults: {
                            traits: [
                                { name: "content", label: "Text", type: "text", changeProp: 1 },
                                { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                                {
                                    name: "target",
                                    label: "Target",
                                    type: "select",
                                    options: [
                                        { id: "_self", name: "Same tab" },
                                        { id: "_blank", name: "New tab" },
                                    ],
                                    changeProp: 1,
                                },
                                {
                                    name: "class",
                                    label: "Style",
                                    type: "select",
                                    options: [
                                        { id: "text-blue-600 hover:text-blue-800 underline", name: "Primary" },
                                        { id: "text-gray-600 hover:text-gray-800 underline", name: "Secondary" },
                                        { id: "text-blue-600 hover:text-blue-800 no-underline", name: "No underline" },
                                    ],
                                    changeProp: 1,
                                },
                            ],
                        },
                    },
                });

                editor.Blocks.add("tailwind-card-full", {
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
                    category: "Components",
                    content: `
                        <div class="max-w-xs shadow-md border-0 text-center p-4 bg-white rounded-lg">
                            <img src="https://dummyimage.com/300x200" class="w-full rounded-lg" alt="Product Image">
                            <div class="pt-4">
                                <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full mb-2">Kategori</span>
                                <h5 class="text-lg font-semibold mb-2">Nama Produk</h5>
                                <p class="mb-1 text-gray-500 line-through">Rp 150.000</p>
                                <p class="text-xl font-bold text-green-600 mb-3">Rp 99.000</p>
                                <a href="" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="bi bi-whatsapp me-2"></i> Pesan via WhatsApp
                                </a>
                            </div>
                        </div>
                    `,
                });

                editor.BlockManager.add("counter-section", {
                    label: `
                        <div style="text-align:center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16">
                                <rect width="16" height="16" fill="#eaeaea" rx="2" />
                                <path d="M8 1.5a6.5 6.5 0 100 13 6.5 0 000-13zM8 13A4.5 0 118 4a4.5 0 010 9z" fill="#555"/>
                                <path d="M8 4.5a.5.5 0 01.5.5v3a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z" fill="#777"/>
                            </svg>
                            <div style="margin-top:10px;">Counter Section</div>
                        </div>
                    `,
                    category: "Components",
                    content: `
                        <section class="py-12 bg-gray-100 text-center">
                            <div class="container mx-auto px-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="p-6 bg-white rounded-xl shadow">
                                        <h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="44">0</h2>
                                        <p class="font-bold text-gray-800" data-gjs-editable="true">Pengajar/GTK</p>
                                    </div>
                                    <div class="p-6 bg-white rounded-xl shadow">
                                        <h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="13">0</h2>
                                        <p class="font-bold text-gray-800" data-gjs-editable="true">Staff/PTK</p>
                                    </div>
                                    <div class="p-6 bg-white rounded-xl shadow">
                                        <h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="546">0</h2>
                                        <p class="font-bold text-gray-800" data-gjs-editable="true">Santri</p>
                                    </div>
                                    <div class="p-6 bg-white rounded-xl shadow">
                                        <h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="0">0</h2>
                                        <p class="font-bold text-gray-800" data-gjs-editable="true">Alumni</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    `,
                });

                editor.DomComponents.addType("counter-number", {
                    model: {
                        defaults: {
                            tagName: "h2",
                            classes: ["text-green-600", "counter", "text-4xl", "font-bold"],
                            traits: [
                                {
                                    type: "number",
                                    label: "Jumlah",
                                    name: "data-target",
                                    min: 0,
                                },
                            ],
                            init() {
                                this.on("change:data-target", this.updateContent);
                            },
                            updateContent() {
                                const dataTarget = this.get("data-target");
                                this.set("content", dataTarget);
                            },
                        },
                    },
                });
            }

        if (themeStyle === 'tailwindcss') {
            addTailwindBlocks(editor);
        } else {
            addBootstrapBlocks(editor);
        }

    });
</script>

@php
    $theme = \Uiaciel\SuryaCms\Models\Setting::find(1)->active_theme ?? config('frontend.active');
    $themePath = resource_path("views/frontend/{$theme}/theme.php");
        if (!file_exists($themePath)) {
            $themePath = base_path("packages/uiaciel/suryacms/resources/views/frontend/{$theme}/theme.php");
        }
    $themeData = file_exists($themePath) ? include $themePath : [];
    $path_theme = is_array($themeData) && isset($themeData['info']['path']) ? $themeData['info']['path'] : '';
    $themeAssets = is_array($themeData) && isset($themeData['assets']) ? $themeData['assets'] : ['styles' => [], 'scripts' => []];
    $customBlocks = is_array($themeData) && isset($themeData['custom_blocks']) ? $themeData['custom_blocks'] : [];
    $sections = is_array($themeData) && isset($themeData['sections']) ? $themeData['sections'] : [];

    $themeStyle = is_array($themeData) && isset($themeData['info']['style']) ? $themeData['info']['style'] : 'bootstrap';
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const path_theme = @json($path_theme);
        const themeAssets = @json($themeAssets);
        const customBlocks = @json($customBlocks);
        const sections = @json($sections);
        const themeStyle = @json($themeStyle);

        const shortcodePreviews = {
            '[[gallery]]': `
            <div class="row text-center p-4">
                <div class="col-lg-3 col-md-3"><img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" /><h6>Gallery Preview</h6></div>
                <div class="col-lg-3 col-md-3"><img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" /><h6>Gallery Preview</h6></div>
                <div class="col-lg-3 col-md-3"><img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" /><h6>Gallery Preview</h6></div>
                <div class="col-lg-3 col-md-3"><img src="https://dummyimage.com/150x100?text=Gallery" class="img-fluid mb-2" /><h6>Gallery Preview</h6></div>
            </div>`,
            '[[blog]]': `<div class="p-3 border bg-light"><img src="https://dummyimage.com/300x150?text=Blog+Post" class="img-fluid mb-2" /><h6>Blog Posts Preview</h6><p class="small text-muted">Daftar artikel terbaru</p></div>`,
            '[[youtube]]': `<div class="text-center p-3 border bg-light"><img src="https://dummyimage.com/320x180?text=YouTube+Video" class="img-fluid mb-2" /><h6>YouTube Preview</h6></div>`,
            '[[slider]]': `<section id="slider"><div style="background:#ddd;padding:80px;text-align:center;color:#555;">Slider Preview</div></section>`
        };

        const shortcodeMap = { ...shortcodePreviews };
        customBlocks.forEach(block => {
            if (block.content.startsWith('[[') && block.preview) {
                shortcodeMap[block.content] = block.preview;
            }
        });

        // ======================================================
        // GRAPESJS CONFIG - ELEMENTOR-LIKE SETUP
        // ======================================================
        const config = {
            container: '#gjs',
            fromElement: false,
            height: '100%',
            width: 'auto',
            storageManager: false,
            noticeOnUnload: false,

            canvas: {
                shadowRender: true,
                styles: themeAssets.styles.map(style => {
                    if (style.startsWith('http') || style.startsWith('/')) return style;
                    return `/frontend/${path_theme}/${style}`;
                }),
                scripts: themeAssets.scripts.map(script => {
                    if (script.startsWith('http')) return script;
                    return `/frontend/${path_theme}/${script}`;
                }),
            },

            // ── Device Manager ─────────────────────────────────
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px', widthMedia: '992px' },
                    { name: 'Mobile', width: '375px', widthMedia: '480px' },
                ],
            },

            // ── Panels (Elementor-like: left panel + top toolbar) ─
            panels: {
                defaults: [
                    // Left sidebar views tabs
                    {
                        id: 'views',
                        buttons: [
                            {
                                id: 'open-blocks',
                                className: 'gjs-pn-btn fa fa-th-large',
                                command: 'open-blocks',
                                togglable: false,
                                active: true,
                                attributes: { title: 'Blok / Elemen' },
                            },
                            {
                                id: 'open-layers',
                                className: 'gjs-pn-btn fa fa-layer-group',
                                command: 'open-layers',
                                togglable: false,
                                attributes: { title: 'Layer Tree' },
                            },
                            {
                                id: 'open-styles',
                                className: 'gjs-pn-btn fa fa-paint-brush',
                                command: 'open-sm',
                                togglable: false,
                                attributes: { title: 'Style Manager' },
                            },
                            {
                                id: 'open-traits',
                                className: 'gjs-pn-btn fa fa-sliders-h',
                                command: 'open-tm',
                                togglable: false,
                                attributes: { title: 'Element Settings' },
                            },
                        ],
                    },
                    // Top-right toolbar
                    {
                        id: 'options',
                        buttons: [
                            {
                                id: 'toggle-fullscreen',
                                className: 'gjs-pn-btn fa fa-expand',
                                command: 'core:fullscreen',
                                togglable: true,
                                attributes: { title: 'Fullscreen' },
                            },
                            {
                                id: 'export-template',
                                className: 'gjs-pn-btn fa fa-code',
                                command: 'export-template',
                                attributes: { title: 'Lihat Kode' },
                            },
                        ],
                    },
                ],
            },

            // ── Style Manager Sectors ──────────────────────────
            styleManager: {
                clearProperties: true,
                sectors: [
                    {
                        name: 'Dimensi',
                        open: false,
                        buildProps: ['width', 'min-height', 'padding', 'margin'],
                        properties: [
                            { extend: 'padding', type: 'composite' },
                            { extend: 'margin', type: 'composite' },
                        ],
                    },
                    {
                        name: 'Typography',
                        open: false,
                        buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-transform'],
                    },
                    {
                        name: 'Dekorasi',
                        open: false,
                        buildProps: ['background-color', 'background', 'border-radius', 'border', 'box-shadow', 'opacity'],
                    },
                    {
                        name: 'Layout Extra',
                        open: false,
                        buildProps: ['position', 'top', 'right', 'left', 'bottom', 'z-index', 'display', 'flex-direction', 'align-items', 'justify-content', 'overflow'],
                    },
                    {
                        name: 'Transisi',
                        open: false,
                        buildProps: ['transition', 'transform'],
                    },
                ],
            },

            // ── Block Manager categories ────────────────────────

        };

        const editor = grapesjs.init(config);
        window._gjsEditor = editor; // Expose for Undo/Redo buttons

        let isInitialLoad = true;

        // ======================================================
        // LOAD CONTENT
        // ======================================================
        editor.on('load', () => {
            if (isInitialLoad) {
                isInitialLoad = false;

                @if ($html)
                    editor.setComponents(@js($html));
                @endif

                @if ($css)
                    editor.setStyle(@js($css));
                @endif

                // Transform shortcodes
                setTimeout(() => {
                    let html = editor.getHtml();
                    if (/\[\[(announcement|gallery|blog|youtube|slider|contact|report|stock)\]\]/.test(html) && !html.includes('data-gjs-type="shortcode"')) {
                        const transformed = html.replace(/\[\[(announcement|gallery|blog|youtube|slider|contact|report|stock)\]\]/g, (match) => {
                            const preview = shortcodeMap[match] || '';
                            return `<div data-gjs-type="shortcode" data-shortcode="${match}">${preview}</div>`;
                        });
                        editor.setComponents(transformed);
                    }
                }, 100);

                // Register custom blocks
                if (themeStyle === 'tailwindcss') {
                    addTailwindBlocks(editor);
                } else {
                    addBootstrapBlocks(editor);
                }

                // Register custom shortcode blocks from theme
                customBlocks.forEach(block => {
                    const shortcode = block.content.trim();
                    const previewHTML = shortcodeMap[shortcode] || '';
                    editor.BlockManager.add(block.id, {
                        label: block.label,
                        category: block.category,
                        content: { type: "shortcode", attributes: { "data-shortcode": shortcode }, content: previewHTML },
                    });
                });

                // Register sections from theme
                sections.forEach(blocksection => {
                    editor.BlockManager.add(blocksection.id, {
                        label: blocksection.label,
                        category: blocksection.category,
                        content: blocksection.content,
                    });
                });

                // Status bar: component count
                updateStatusBar(editor);

                // Show ready toast
                if (typeof showToast === 'function') {
                    showToast('success', 'Editor siap digunakan!');
                }
            }
        });

        // ======================================================
        // SYNC HTML/CSS TO FORM FIELDS
        // ======================================================
        editor.on('update', () => {
            const html = editor.getHtml();
            const css = editor.getCss();

            const htmlInput = document.getElementById('htmldata');
            const cssInput = document.getElementById('cssdata');

            if (htmlInput) { htmlInput.value = html; htmlInput.dispatchEvent(new Event('input', { bubbles: true })); }
            if (cssInput) { cssInput.value = css; cssInput.dispatchEvent(new Event('input', { bubbles: true })); }

            // Trigger unsaved state
            document.dispatchEvent(new CustomEvent('gjs-content-changed'));

            updateStatusBar(editor);
        });

        // ======================================================
        // STATUS BAR
        // ======================================================
        function updateStatusBar(editor) {
            const countEl = document.getElementById('status-component-count');
            const selectedEl = document.getElementById('editor-status-selected');
            if (countEl) {
                const count = editor.DomComponents.getComponents().length;
                countEl.textContent = count;
            }
        }

        editor.on('component:selected', (component) => {
            const el = document.getElementById('editor-status-selected');
            if (el && component) {
                const tag = component.get('tagName') || 'div';
                const type = component.get('type') || '';
                el.textContent = `Dipilih: <${tag}>${type ? ` (${type})` : ''}`;
            }
        });

        editor.on('component:deselected', () => {
            const el = document.getElementById('editor-status-selected');
            if (el) el.textContent = 'Tidak ada seleksi';
        });

        // ======================================================
        // DEVICE SWITCHER (from header buttons via CustomEvent)
        // ======================================================
        document.addEventListener('gjs-set-device', (e) => {
            const mode = e.detail;
            const deviceMap = { desktop: 'Desktop', tablet: 'Tablet', mobile: 'Mobile' };
            const deviceName = deviceMap[mode] || 'Desktop';
            editor.setDevice(deviceName);
        });

        // ======================================================
        // SHORTCODE COMPONENT TYPE
        // ======================================================
        editor.DomComponents.addType("shortcode", {
            isComponent: (el) => el && el.getAttribute && el.getAttribute("data-gjs-type") === "shortcode",
            model: {
                defaults: {
                    tagName: "div",
                    droppable: false,
                    editable: false,
                    copyable: false,
                    removable: true,
                    traits: [],
                },
                toHTML() {
                    const shortcode = this.getAttributes()["data-shortcode"] || "";
                    return shortcode;
                },
                setContent(content) { return this; },
            },
            view: {},
        });

        // ======================================================
        // UNIVERSAL COMPONENTS (both themes)
        // ======================================================
        editor.Blocks.add("tailwind-card-full", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M1 4a1 1 0 011-1h12a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zM2 5v6h12V5H2zm2 1h2v2H4V6zm4 0h4v2H8V6z" fill="#555"/><circle cx="12" cy="10" r="1" fill="#777"/></svg><div style="margin-top:10px;">Product Card</div></div>`,
            category: "Components",
            content: `<div class="max-w-xs shadow-md border-0 text-center p-4 bg-white rounded-lg"><img src="https://dummyimage.com/300x200" class="w-full rounded-lg" alt="Product Image"><div class="pt-4"><span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full mb-2">Kategori</span><h5 class="text-lg font-semibold mb-2">Nama Produk</h5><p class="mb-1 text-gray-500 line-through">Rp 150.000</p><p class="text-xl font-bold text-green-600 mb-3">Rp 99.000</p><a href="" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"><i class="bi bi-whatsapp me-2"></i> Pesan via WhatsApp</a></div></div>`,
        });

        editor.BlockManager.add("counter-section", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><circle cx="8" cy="8" r="5" fill="none" stroke="#555" stroke-width="1.5"/><path d="M8 5v3l2 1" stroke="#555" stroke-width="1" fill="none"/></svg><div style="margin-top:10px;">Counter Section</div></div>`,
            category: "Components",
            content: `<section class="py-12 bg-gray-100 text-center"><div class="container mx-auto px-4"><div class="grid grid-cols-1 md:grid-cols-4 gap-6"><div class="p-6 bg-white rounded-xl shadow"><h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="44">0</h2><p class="font-bold text-gray-800">Pengajar/GTK</p></div><div class="p-6 bg-white rounded-xl shadow"><h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="13">0</h2><p class="font-bold text-gray-800">Staff/PTK</p></div><div class="p-6 bg-white rounded-xl shadow"><h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="546">0</h2><p class="font-bold text-gray-800">Santri</p></div><div class="p-6 bg-white rounded-xl shadow"><h2 data-gjs-type="counter-number" class="text-4xl font-bold text-blue-600 counter" data-target="0">0</h2><p class="font-bold text-gray-800">Alumni</p></div></div></div></section>`,
        });

        editor.DomComponents.addType("counter-number", {
            model: {
                defaults: {
                    tagName: "h2",
                    traits: [{ type: "number", label: "Jumlah", name: "data-target", min: 0 }],
                    init() { this.on("change:data-target", this.updateContent); },
                    updateContent() { this.set("content", this.get("data-target")); },
                },
            },
        });

        editor.Blocks.add("link", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><text x="3" y="11" font-size="8" font-family="Arial" fill="#555">A</text></svg><div style="margin-top:10px;">Link</div></div>`,
            category: "Components",
            content: { type: "link", content: "Sample Link", attributes: { href: "#", target: "_self" }, classes: ["text-blue-600", "underline"] },
        });

        editor.DomComponents.addType("link", {
            model: {
                defaults: {
                    traits: [
                        { name: "content", label: "Text", type: "text", changeProp: 1 },
                        { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                        { name: "target", label: "Target", type: "select", options: [{ id: "_self", name: "Same tab" }, { id: "_blank", name: "New tab" }], changeProp: 1 },
                    ],
                },
            },
        });

    }); // end DOMContentLoaded

    // ============================================================
    // BOOTSTRAP BLOCKS
    // ============================================================
    function addBootstrapBlocks(editor) {
        // Media
        editor.Blocks.add("youtube-video-bootstrap", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><polygon points="6,5 11,8 6,11" fill="#555" /></svg><div style="margin-top:10px;">YouTube Video</div></div>`,
            category: "Media",
            content: `<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>`,
        });

        editor.Blocks.add("image-box", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M3 9l3-3 2 2 3-3 3 3v4H3V9z" fill="#555" /></svg><div style="margin-top:10px;">Image Box</div></div>`,
            category: "Media",
            content: `<div class="text-center p-3"><img src="https://dummyimage.com/400x250" alt="Image Box" class="img-fluid rounded mb-3 shadow-sm"><h4 class="fw-bold mb-2">Title Here</h4><p class="text-muted">Add a short description here.</p></div>`,
        });

        editor.Blocks.add("gallery-grid", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="4" height="4" fill="#555" /><rect x="7" y="3" width="4" height="4" fill="#777" /><rect x="2" y="8" width="4" height="4" fill="#999" /><rect x="7" y="8" width="4" height="4" fill="#555" /></svg><div style="margin-top:10px;">Gallery Grid</div></div>`,
            category: "Media",
            content: `<div class="container py-3"><div class="row g-3"><div class="col-6 col-md-4"><img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm"></div><div class="col-6 col-md-4"><img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm"></div><div class="col-6 col-md-4"><img src="https://dummyimage.com/400x300" class="img-fluid rounded shadow-sm"></div></div></div>`,
        });

        editor.Blocks.add("image", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M2 12l3-3 2 2 3-3 4 4v1H2v-1z" fill="#555" /><circle cx="5" cy="5" r="1.5" fill="#777" /></svg><div style="margin-top:10px;">Image</div></div>`,
            category: "Media",
            content: `<div class="text-center"><img src="https://dummyimage.com/800x500" alt="Image" class="img-fluid rounded shadow-sm mb-3"></div>`,
        });

        // Layout
        editor.Blocks.add("section", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1" y="3" width="14" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">Section</div></div>`,
            category: "Layout",
            content: `<section class="container py-5"><h2>Section Title</h2></section>`,
        });

        editor.Blocks.add("spacer", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M4 6h8v4H4z" fill="#ccc" /></svg><div style="margin-top:10px;">Spacer</div></div>`,
            category: "Layout",
            content: `<div class="my-5"></div>`,
        });

        // Columns
        editor.Blocks.add("row-1col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="12" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">1 Column</div></div>`,
            category: "Column",
            content: `<div class="row"><div class="col-12"><p>1 Column content here...</p></div></div>`,
        });

        editor.Blocks.add("row-2col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="5" height="10" fill="#ccc" rx="1" /><rect x="9" y="3" width="5" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">2 Columns</div></div>`,
            category: "Column",
            content: `<div class="row"><div class="col-md-6"><p>Left column content...</p></div><div class="col-md-6"><p>Right column content...</p></div></div>`,
        });

        editor.Blocks.add("row-3col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="4" height="10" fill="#ccc" rx="1" /><rect x="6" y="3" width="4" height="10" fill="#ccc" rx="1" /><rect x="10.5" y="3" width="4" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">3 Columns</div></div>`,
            category: "Column",
            content: `<div class="row"><div class="col-md-4"><p>Column 1</p></div><div class="col-md-4"><p>Column 2</p></div><div class="col-md-4"><p>Column 3</p></div></div>`,
        });

        editor.Blocks.add("row-4col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="5" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="9" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="13" y="3" width="2" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">4 Columns</div></div>`,
            category: "Column",
            content: `<div class="row text-center"><div class="col-md-3"><p>Col 1</p></div><div class="col-md-3"><p>Col 2</p></div><div class="col-md-3"><p>Col 3</p></div><div class="col-md-3"><p>Col 4</p></div></div>`,
        });

        editor.Blocks.add("row-sidebar-left", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="4" height="10" fill="#bbb" rx="1" /><rect x="6.5" y="3" width="8" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">Sidebar Left</div></div>`,
            category: "Column",
            content: `<div class="row"><aside class="col-md-4 bg-light p-3"><p>Sidebar content...</p></aside><main class="col-md-8"><p>Main content area...</p></main></div>`,
        });

        editor.Blocks.add("row-sidebar-right", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="8" height="10" fill="#ccc" rx="1" /><rect x="10.5" y="3" width="4" height="10" fill="#bbb" rx="1" /></svg><div style="margin-top:10px;">Sidebar Right</div></div>`,
            category: "Column",
            content: `<div class="row"><main class="col-md-8"><p>Main content area...</p></main><aside class="col-md-4 bg-light p-3"><p>Sidebar content...</p></aside></div>`,
        });

        // Components
        editor.Blocks.add("button", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="3" y="7" width="10" height="2" fill="#555" rx="1" /></svg><div style="margin-top:10px;">Button</div></div>`,
            category: "Components",
            content: { type: "button", content: "Click Me", classes: ["btn", "btn-primary"], attributes: { href: "#", type: "button" } },
        });

        editor.DomComponents.addType("button", {
            model: {
                defaults: {
                    traits: [
                        { name: "content", label: "Text", type: "text", changeProp: 1 },
                        { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                        { name: "target", label: "Target", type: "select", options: [{ id: "_self", name: "Same tab" }, { id: "_blank", name: "New tab" }], changeProp: 1 },
                        { name: "class", label: "Style", type: "select", options: [{ id: "btn btn-primary", name: "Primary" }, { id: "btn btn-secondary", name: "Secondary" }, { id: "btn btn-outline-primary", name: "Outline" }, { id: "btn btn-success", name: "Success" }, { id: "btn btn-danger", name: "Danger" }], changeProp: 1 },
                    ],
                },
            },
        });

        editor.Blocks.add("link-bs", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><text x="3" y="11" font-size="8" font-family="Arial" fill="#555">A</text></svg><div style="margin-top:10px;">Link</div></div>`,
            category: "Components",
            content: { type: "link", content: "Sample Link", attributes: { href: "#", target: "_self" } },
        });

        editor.DomComponents.addType("link", {
            model: {
                defaults: {
                    traits: [
                        { name: "content", label: "Text", type: "text", changeProp: 1 },
                        { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                        { name: "target", label: "Target", type: "select", options: [{ id: "_self", name: "Same tab" }, { id: "_blank", name: "New tab" }], changeProp: 1 },
                        { name: "class", label: "Style", type: "select", options: [{ id: "link-primary", name: "Primary" }, { id: "link-secondary", name: "Secondary" }, { id: "text-decoration-none", name: "No underline" }], changeProp: 1 },
                    ],
                },
            },
        });

        editor.Blocks.add("bootstrap-card-full", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M1 4a1 1 0 011-1h12a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zM2 5v6h12V5H2zm2 1h2v2H4V6zm4 0h4v2H8V6z" fill="#555"/></svg><div style="margin-top:10px;">Product Card</div></div>`,
            category: "Components",
            content: `<div class="card shadow-sm border-0 text-center p-3" style="max-width:300px;background:#fff;"><img src="https://dummyimage.com/300x200" class="card-img-top img-fluid rounded-3" alt="Product Image"><div class="card-body"><span class="badge bg-secondary mb-2">Kategori</span><h5 class="card-title mb-2">Nama Produk</h5><p class="mb-1 text-muted text-decoration-line-through">Rp 150.000</p><p class="fs-5 fw-bold text-success mb-3">Rp 99.000</p><a href="" class="btn btn-success w-100"><i class="bi bi-whatsapp me-2"></i> Pesan via WhatsApp</a></div></div>`,
        });

        editor.BlockManager.add("counter-section-bs", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><circle cx="8" cy="8" r="5" fill="none" stroke="#555" stroke-width="1.5"/></svg><div style="margin-top:10px;">Counter Section</div></div>`,
            category: "Components",
            content: `<section class="py-5 bg-light text-center"><div class="container"><div class="row g-4"><div class="col-md-3"><div class="p-4 bg-white rounded-5 shadow"><h2 data-gjs-type="counter-number" class="text-primary counter" data-target="44">0</h2><p class="fw-bold text-dark">Pengajar/GTK</p></div></div><div class="col-md-3"><div class="p-4 bg-white rounded-5 shadow"><h2 data-gjs-type="counter-number" class="text-primary counter" data-target="13">0</h2><p class="fw-bold text-dark">Staff/PTK</p></div></div><div class="col-md-3"><div class="p-4 bg-white rounded-5 shadow"><h2 data-gjs-type="counter-number" class="text-primary counter" data-target="546">0</h2><p class="fw-bold text-dark">Santri</p></div></div><div class="col-md-3"><div class="p-4 bg-white rounded-5 shadow"><h2 data-gjs-type="counter-number" class="text-primary counter" data-target="0">0</h2><p class="fw-bold text-dark">Alumni</p></div></div></div></div></section>`,
        });

        editor.DomComponents.addType("youtube-bootstrap-embed", {
            model: {
                defaults: {
                    tagName: "div", classes: ["ratio", "ratio-16x9"],
                    components: [{ tagName: "iframe", attributes: { src: "https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID", title: "YouTube video player", frameborder: "0", allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share", allowfullscreen: true }, style: { position: "absolute", top: "0", left: "0", width: "100%", height: "100%" } }],
                    traits: [
                        { type: "text", label: "Video ID", name: "videoId" },
                        { type: "select", label: "Ratio", name: "ratio", options: [{ value: "ratio-16x9", name: "16:9" }, { value: "ratio-4x3", name: "4:3" }, { value: "ratio-1x1", name: "1:1" }] },
                    ],
                },
                init() { this.on("change:videoId", this.updateVideo); this.on("change:ratio", this.updateRatio); },
                updateVideo() {
                    const videoId = this.get("videoId");
                    const iframe = this.components().models[0];
                    if (iframe) iframe.set("attributes", { src: `https://www.youtube.com/embed/${videoId}` });
                },
                updateRatio() {
                    const ratio = this.get("ratio");
                    this.get("classes").forEach((cls) => { if (cls.startsWith("ratio-")) this.removeClass(cls); });
                    this.addClass(ratio);
                },
            },
        });
    }

    // ============================================================
    // TAILWIND BLOCKS
    // ============================================================
    function addTailwindBlocks(editor) {
        editor.Blocks.add("youtube-video-tailwind", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><polygon points="6,5 11,8 6,11" fill="#555" /></svg><div style="margin-top:10px;">YouTube Video</div></div>`,
            category: "Media",
            content: `<div class="aspect-w-16 aspect-h-9"><iframe src="https://www.youtube.com/embed/YOUR_YOUTUBE_VIDEO_ID" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="w-full h-full"></iframe></div>`,
        });

        editor.Blocks.add("image-box", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M3 9l3-3 2 2 3-3 3 3v4H3V9z" fill="#555" /></svg><div style="margin-top:10px;">Image Box</div></div>`,
            category: "Media",
            content: `<div class="text-center p-4"><img src="https://dummyimage.com/400x250" alt="Image Box" class="w-full rounded-lg mb-3 shadow-sm"><h4 class="font-bold text-xl mb-2">Title Here</h4><p class="text-gray-500">Add a short description here.</p></div>`,
        });

        editor.Blocks.add("gallery-grid", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="4" height="4" fill="#555" /><rect x="7" y="3" width="4" height="4" fill="#777" /><rect x="2" y="8" width="4" height="4" fill="#999" /><rect x="7" y="8" width="4" height="4" fill="#555" /></svg><div style="margin-top:10px;">Gallery Grid</div></div>`,
            category: "Media",
            content: `<div class="container mx-auto py-3 px-4"><div class="grid grid-cols-2 md:grid-cols-3 gap-3"><div><img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm"></div><div><img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm"></div><div><img src="https://dummyimage.com/400x300" class="w-full rounded-lg shadow-sm"></div></div></div>`,
        });

        editor.Blocks.add("image", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M2 12l3-3 2 2 3-3 4 4v1H2v-1z" fill="#555" /><circle cx="5" cy="5" r="1.5" fill="#777" /></svg><div style="margin-top:10px;">Image</div></div>`,
            category: "Media",
            content: `<div class="text-center"><img src="https://dummyimage.com/800x500" alt="Image" class="w-full rounded-lg shadow-sm mb-3"></div>`,
        });

        // Layout
        editor.Blocks.add("section", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1" y="3" width="14" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">Section</div></div>`,
            category: "Layout",
            content: `<section class="container mx-auto py-12 px-4"><h2 class="text-3xl font-bold">Section Title</h2></section>`,
        });

        editor.Blocks.add("spacer", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><path d="M4 6h8v4H4z" fill="#ccc" /></svg><div style="margin-top:10px;">Spacer</div></div>`,
            category: "Layout",
            content: `<div class="my-12"></div>`,
        });

        // Columns
        editor.Blocks.add("row-1col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="12" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">1 Column</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1"><div><p>1 Column content here...</p></div></div>`,
        });

        editor.Blocks.add("row-2col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="2" y="3" width="5" height="10" fill="#ccc" rx="1" /><rect x="9" y="3" width="5" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">2 Columns</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><p>Left column...</p></div><div><p>Right column...</p></div></div>`,
        });

        editor.Blocks.add("row-3col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="4" height="10" fill="#ccc" rx="1" /><rect x="6" y="3" width="4" height="10" fill="#ccc" rx="1" /><rect x="10.5" y="3" width="4" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">3 Columns</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1 md:grid-cols-3 gap-4"><div><p>Column 1</p></div><div><p>Column 2</p></div><div><p>Column 3</p></div></div>`,
        });

        editor.Blocks.add("row-4col", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="5" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="9" y="3" width="3" height="10" fill="#ccc" rx="1" /><rect x="13" y="3" width="2" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">4 Columns</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center"><div><p>Col 1</p></div><div><p>Col 2</p></div><div><p>Col 3</p></div><div><p>Col 4</p></div></div>`,
        });

        editor.Blocks.add("row-sidebar-left", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="4" height="10" fill="#bbb" rx="1" /><rect x="6.5" y="3" width="8" height="10" fill="#ccc" rx="1" /></svg><div style="margin-top:10px;">Sidebar Left</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1 md:grid-cols-4 gap-4"><aside class="md:col-span-1 bg-gray-100 p-4"><p>Sidebar content...</p></aside><main class="md:col-span-3"><p>Main content area...</p></main></div>`,
        });

        editor.Blocks.add("row-sidebar-right", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="1.5" y="3" width="8" height="10" fill="#ccc" rx="1" /><rect x="10.5" y="3" width="4" height="10" fill="#bbb" rx="1" /></svg><div style="margin-top:10px;">Sidebar Right</div></div>`,
            category: "Column",
            content: `<div class="grid grid-cols-1 md:grid-cols-4 gap-4"><main class="md:col-span-3"><p>Main content area...</p></main><aside class="md:col-span-1 bg-gray-100 p-4"><p>Sidebar content...</p></aside></div>`,
        });

        // Components
        editor.Blocks.add("button", {
            label: `<div style="text-align:center"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" viewBox="0 0 16 16"><rect width="16" height="16" fill="#eaeaea" rx="2" /><rect x="3" y="7" width="10" height="2" fill="#555" rx="1" /></svg><div style="margin-top:10px;">Button</div></div>`,
            category: "Components",
            content: { type: "button", content: "Click Me", classes: ["bg-blue-600", "hover:bg-blue-700", "text-white", "font-bold", "py-2", "px-4", "rounded"], attributes: { href: "#", type: "button" } },
        });

        editor.DomComponents.addType("button", {
            model: {
                defaults: {
                    traits: [
                        { name: "content", label: "Text", type: "text", changeProp: 1 },
                        { name: "href", label: "URL", type: "text", placeholder: "https://example.com", changeProp: 1 },
                        { name: "target", label: "Target", type: "select", options: [{ id: "_self", name: "Same tab" }, { id: "_blank", name: "New tab" }], changeProp: 1 },
                        { name: "class", label: "Style", type: "select", options: [{ id: "bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded", name: "Primary" }, { id: "bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded", name: "Secondary" }, { id: "border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-2 px-4 rounded", name: "Outline" }, { id: "bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded", name: "Success" }, { id: "bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded", name: "Danger" }], changeProp: 1 },
                    ],
                },
            },
        });

        editor.DomComponents.addType("counter-number", {
            model: {
                defaults: {
                    tagName: "h2",
                    classes: ["text-green-600", "counter", "text-4xl", "font-bold"],
                    traits: [{ type: "number", label: "Jumlah", name: "data-target", min: 0 }],
                    init() { this.on("change:data-target", this.updateContent); },
                    updateContent() { this.set("content", this.get("data-target")); },
                },
            },
        });
    }
</script>
