<div class="p-4 md:p-6 space-y-6">
    <header>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Sites</h1>
        <p class="mt-1 text-sm text-slate-500">
            Tambahkan site dan domain dari admin domain utama. Setiap site memiliki konten dan pengaturan sendiri.
        </p>
    </header>

    <x-suryacms::session-status />

    <section class="grid gap-6 xl:grid-cols-2">
        <form wire:submit="createSite" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 class="font-semibold text-slate-800">Buat site baru</h2>
                <p class="mt-1 text-sm text-slate-500">Site baru dibuat dengan domain utama dan pengaturan awal.</p>
            </div>

            <label class="block text-sm font-medium text-slate-700">
                Nama site
                <input wire:model="siteName" type="text" maxlength="255" required
                    class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('siteName') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-medium text-slate-700">
                Domain utama
                <input wire:model="primaryDomain" type="text" maxlength="255" placeholder="contoh.id" required
                    class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('primaryDomain') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <button type="submit" wire:loading.attr="disabled" wire:target="createSite"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                Buat site
            </button>
        </form>

        <form wire:submit="addDomain" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 class="font-semibold text-slate-800">Tambahkan domain alias</h2>
                <p class="mt-1 text-sm text-slate-500">Beberapa domain dapat menampilkan site yang sama.</p>
            </div>

            <label class="block text-sm font-medium text-slate-700">
                Site tujuan
                <select wire:model="domainSiteId" required
                    class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih site</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->name }} (ID {{ $site->id }})</option>
                    @endforeach
                </select>
                @error('domainSiteId') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-medium text-slate-700">
                Domain tambahan
                <input wire:model="additionalDomain" type="text" maxlength="255" placeholder="www.contoh.id" required
                    class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('additionalDomain') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <button type="submit" wire:loading.attr="disabled" wire:target="addDomain"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900 disabled:opacity-50">
                Petakan domain
            </button>
        </form>
    </section>

    @error('domains')
        <p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</p>
    @enderror

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-800">Daftar site dan akses admin</h2>
        </div>

        @forelse ($sites as $site)
            <article class="border-b border-slate-100 p-5 last:border-b-0">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        @if ($editingSiteId === $site->id)
                            <form wire:submit="renameSite" class="flex flex-wrap items-center gap-2">
                                <input wire:model="editingSiteName" maxlength="255" required
                                    class="rounded-lg border-slate-300 text-sm shadow-sm">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Simpan</button>
                                <button type="button" wire:click="$set('editingSiteId', null)" class="px-2 py-2 text-xs text-slate-500">Batal</button>
                            </form>
                            @error('editingSiteName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        @else
                            <h3 class="font-semibold text-slate-800">{{ $site->name }}</h3>
                            <p class="text-xs text-slate-500">Site ID: {{ $site->id }}</p>
                        @endif
                    </div>
                    @if ($editingSiteId !== $site->id)
                        <button type="button" wire:click="beginRename({{ $site->id }})"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Ubah nama
                        </button>
                    @endif
                </div>

                <div class="mt-4 space-y-2">
                    @foreach ($site->domains as $domain)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-sm text-slate-700">{{ $domain->domain }}</span>
                                @if ($domain->is_primary)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">Utama</span>
                                @endif
                                <a href="{{ request()->getScheme() }}://{{ $domain->domain }}/{{ $adminUrlPath }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-xs font-semibold text-blue-600 hover:underline">
                                    Buka admin site
                                </a>
                            </div>

                            <div class="flex items-center gap-2">
                                @unless ($domain->is_primary)
                                    <button type="button" wire:click="setPrimaryDomain({{ $domain->id }})"
                                        class="text-xs font-semibold text-slate-600 hover:text-blue-600">
                                        Jadikan utama
                                    </button>
                                    <button type="button" wire:click="deleteDomain({{ $domain->id }})"
                                        wire:confirm="Hapus pemetaan domain ini?"
                                        class="text-xs font-semibold text-red-600 hover:text-red-700">
                                        Hapus domain
                                    </button>
                                @endunless
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        @empty
            <p class="p-6 text-sm text-slate-500">Belum ada site yang terdaftar.</p>
        @endforelse
    </section>

    <aside class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
        Admin sebuah site dapat dibuka di <code>https://domain-site/{{ $adminUrlPath }}</code>. Pengguna admin saat ini
        dipakai bersama, tetapi konten dan pengaturan yang dikelola mengikuti domain yang sedang dibuka.
    </aside>
</div>
