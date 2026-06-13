<template x-teleport="body">
    <div x-show="showVariableModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="showVariableModal = false" style="display:none">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" @click="showVariableModal = false"></div>

        {{-- Panel --}}
        <div x-show="showVariableModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2" x-data="{ activeTab: 'basic', loopTab: 'posts' }"
            class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl shadow-slate-200/80 border border-slate-200 overflow-hidden z-10 max-h-[90vh] flex flex-col"
            @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50 shrink-0">
                <h5 class="text-slate-800 font-bold flex items-center gap-2">
                    <i class="fas fa-book text-indigo-500"></i> Cheat Sheet Variable
                </h5>
                <button @click="showVariableModal = false"
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200 rounded-lg transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 overflow-y-auto flex-1">

                {{-- Main Tab Pills --}}
                <div class="flex gap-1 mb-5 p-1 bg-slate-100 rounded-xl w-fit">
                    <button @click="activeTab = 'header'"
                        :class="activeTab === 'header'
                            ?
                            'bg-white text-indigo-700 shadow-sm font-semibold' :
                            'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                        Header & Footer
                    </button>
                    <button @click="activeTab = 'basic'"
                        :class="activeTab === 'basic'
                            ?
                            'bg-white text-indigo-700 shadow-sm font-semibold' :
                            'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                        Basic Setting
                    </button>
                    <button @click="activeTab = 'social'"
                        :class="activeTab === 'social'
                            ?
                            'bg-white text-indigo-700 shadow-sm font-semibold' :
                            'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                        Social Media
                    </button>
                    <button @click="activeTab = 'loops'"
                        :class="activeTab === 'loops'
                            ?
                            'bg-white text-indigo-700 shadow-sm font-semibold' :
                            'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                        Looping Examples
                    </button>
                </div>

                <div x-show="activeTab === 'header'" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-x-1"
                    x-transition:enter-end="opacity-100 translate-x-0" style="display:none">
                    <p class="text-slate-400 text-xs mb-3">Variabel untuk header dan footer template</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-50">
                        @foreach ([['$header', 'Header'], ['$footer', 'Footer']] as [$var, $label])
                            <div
                                class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50/70 transition-colors group">
                                <div>
                                    <code class="text-indigo-600 text-xs font-mono">@{{ $var }}</code>
                                    <span class="text-slate-400 text-xs ml-2">— {{ $label }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TAB: Basic Setting --}}
                <div x-show="activeTab === 'basic'" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-x-1"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-slate-400 text-xs mb-3">Variabel setting dasar untuk template</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-50">
                        @foreach ([['$settings->sitename', 'Nama situs'], ['$settings->description', 'Deskripsi situs'], ['$settings->url', 'URL situs'], ['$settings->logo', 'Path logo'], ['$settings->images', 'Path gambar utama'], ['$settings->email', 'Email kontak'], ['$settings->address', 'Alamat'], ['$settings->phone', 'Nomor telepon']] as [$var, $label])
                            <div
                                class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50/70 transition-colors group">
                                <div>
                                    <code class="text-indigo-600 text-xs font-mono">@{{ $var }}</code>
                                    <span class="text-slate-400 text-xs ml-2">— {{ $label }}</span>
                                </div>
                                <button onclick="copyVar(this, '@{{ {
    {
        $var }} }}')"
                                    class="opacity-0 group-hover:opacity-100 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 text-[10px] font-semibold rounded-lg transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TAB: Social Media --}}
                <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-x-1"
                    x-transition:enter-end="opacity-100 translate-x-0" style="display:none">
                    <p class="text-slate-400 text-xs mb-3">Variabel sosial media untuk template</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-50">
                        @foreach ([['$settings->whatsapp', 'bi-whatsapp', 'text-green-500', 'WhatsApp'], ['$settings->facebook', 'bi-facebook', 'text-blue-500', 'Facebook'], ['$settings->instagram', 'bi-instagram', 'text-pink-500', 'Instagram'], ['$settings->linkedin', 'bi-linkedin', 'text-blue-600', 'LinkedIn'], ['$settings->youtube', 'bi-youtube', 'text-red-500', 'YouTube'], ['$settings->tiktok', 'bi-tiktok', 'text-slate-700', 'TikTok']] as [$var, $icon, $iconColor, $platform])
                            <div
                                class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50/70 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <i class="bi {{ $icon }} {{ $iconColor }} text-base w-5 text-center"></i>
                                    <div>
                                        <code class="text-indigo-600 text-xs font-mono">@{{ {
    {
        $var }} }}</code>
                                        <span class="text-slate-400 text-xs ml-2">— {{ $platform }}</span>
                                    </div>
                                </div>
                                <button onclick="copyVar(this, '@{{ {
    {
        $var }} }}')"
                                    class="opacity-0 group-hover:opacity-100 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 text-[10px] font-semibold rounded-lg transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TAB: Looping Examples --}}
                <div x-show="activeTab === 'loops'" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-x-1"
                    x-transition:enter-end="opacity-100 translate-x-0" style="display:none">
                    <p class="text-slate-400 text-xs mb-3">Contoh looping siap pakai, salin ke editor</p>

                    {{-- Loop Sub-tabs --}}
                    <div class="flex gap-1.5 mb-4">
                        <button @click="loopTab = 'posts'"
                            :class="loopTab === 'posts'
                                ?
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-200' :
                                'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                            <i class="fa-solid fa-newspaper text-[10px]"></i> Latest Posts
                        </button>
                        <button @click="loopTab = 'gallery'"
                            :class="loopTab === 'gallery'
                                ?
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-200' :
                                'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                            <i class="fa-solid fa-images text-[10px]"></i> Gallery
                        </button>
                        <button @click="loopTab = 'youtube'"
                            :class="loopTab === 'youtube'
                                ?
                                'bg-indigo-600 text-white shadow-sm shadow-indigo-200' :
                                'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                            <i class="fa-solid fa-youtube text-[10px]"></i> Youtube
                        </button>
                    </div>

                    {{-- Posts Code --}}
                    <div x-show="loopTab === 'posts'">
                        <div class="bg-slate-900 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span class="text-slate-400 text-xs font-mono">blade / latest-posts</span>
                                </div>
                                <button onclick="copyCode('code-posts', this)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                            <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-posts">@@forelse ($latestposts as $post)
    @@forelse ($post->gambar() as $gam)
        @@if ($loop->first)
            &lt;img src="@{{ $gam }}" alt="@{{ $post - > title }}" loading="lazy"&gt;
        @@endif
    @@empty
        &lt;img src="/frontend/img/default.webp"&gt;
    @@endforelse
    @{{ formatDate($post - > datepublish) }}
    @{{ $post - > title }}
    @{{ $post - > category - > name }}
@@empty
    &lt;p&gt;Belum ada Berita&lt;/p&gt;
@@endforelse</pre>
                        </div>
                    </div>

                    {{-- Gallery Code --}}
                    <div x-show="loopTab === 'gallery'" style="display:none">
                        <div class="bg-slate-900 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                    <span class="text-slate-400 text-xs font-mono">blade / gallery</span>
                                </div>
                                <button onclick="copyCode('code-gallery', this)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                            <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-gallery">@@foreach ($gallery as $galleries)
    &lt;div class="col-lg-3 col-md-12"&gt;
        &lt;a href="/storage/@{{ $galleries - > image_path }}" class="glightbox"&gt;
            &lt;img src="/storage/@{{ $galleries - > image_path }}"
                 alt="@{{ $galleries - > name }}"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
@@endforeach</pre>
                        </div>
                    </div>

                    {{-- Youtube Code --}}
                    <div x-show="loopTab === 'youtube'" style="display:none">
                        <div class="bg-slate-900 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    <span class="text-slate-400 text-xs font-mono">blade / youtube</span>
                                </div>
                                <button onclick="copyCode('code-youtube', this)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                            <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-youtube">@@foreach ($youtubes as $video)
    &lt;div class="col"&gt;
        &lt;div class="video-item" data-video-id="@{{ $video - > video_id }}"&gt;
            &lt;img src="@{{ $video - > thumbnail_url }}" alt="Thumbnail"&gt;
            &lt;i class="fab fa-youtube"&gt;&lt;/i&gt;
        &lt;/div&gt;
    &lt;/div&gt;
@@endforeach</pre>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-slate-100 bg-slate-50 shrink-0">
                <button @click="showVariableModal = false"
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 border border-slate-200 hover:border-slate-300 hover:bg-white rounded-xl text-sm font-medium transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
