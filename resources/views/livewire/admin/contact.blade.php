<div class="p-3 md:p-6 h-screen flex flex-col bg-gray-50 overflow-hidden" x-data="{ showDataModal: false }">

    <!-- Header Section -->
    <header class="flex items-center justify-between gap-4 mb-4 shrink-0">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-blue-700 flex items-center gap-2">
                <i class="fas fa-inbox"></i> Inbox Messages
            </h2>
            <nav class="hidden sm:block mt-0.5">
                <ol class="flex text-xs text-gray-500 gap-2">
                    <li><a href="/{{ config('suryacms.admin_prefix') }}"
                            class="hover:text-blue-600 transition-colors">Admin</a></li>
                    <li class="before:content-['/'] before:mr-2"><a
                            href="/{{ config('suryacms.admin_prefix') }}/contacts"
                            class="hover:text-blue-600 transition-colors">Inbox</a></li>
                    <li class="before:content-['/'] before:mr-2 text-gray-700 font-medium">Messages</li>
                </ol>
            </nav>
        </div>

        <button type="button" @click="showDataModal = true"
            class="inline-flex items-center px-3 py-1.5 md:px-4 md:py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-full shadow-sm transition-colors shrink-0">
            <i class="fas fa-database mr-1.5"></i> Data
        </button>
    </header>

    <!-- Main Content Box (Fill remaining height) -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 flex-1 flex overflow-hidden">

        <!-- ================= SIDEBAR (DAFTAR PESAN) ================= -->
        <!-- Di Mobile: Sembunyi jika $selectedContact ada. Di Desktop (lg): Selalu Muncul -->
        <div
            class="w-full lg:w-80 xl:w-96 border-r border-gray-200 flex flex-col bg-gray-50 shrink-0 transition-all duration-300
                    {{ $selectedContact ? 'hidden lg:flex' : 'flex' }}">

            <!-- Header Sidebar -->
            <div class="p-4 bg-white border-b sticky top-0 z-10 shrink-0 flex items-center justify-between">
                <h5 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-600"></i> Messages
                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">
                        {{ $contacts->where('is_spam', false)->count() }}
                    </span>
                </h5>
            </div>

            <!-- List Scrollable -->
            <div class="overflow-y-auto flex-1 divide-y divide-gray-100">
                <!-- Inbox List -->
                @forelse($contacts->where('is_spam', false) as $contact)
                    <div wire:click="selectContact({{ $contact->id }})"
                        class="p-4 cursor-pointer transition-colors hover:bg-blue-50/60 relative
                        @if (!$contact->is_read) bg-white font-semibold @else bg-gray-50/50 text-gray-600 @endif 
                        @if ($selectedContact && $selectedContact->id == $contact->id) bg-blue-50 border-l-4 border-l-blue-600 @endif">

                        <div class="flex justify-between items-start mb-1 gap-2">
                            <div
                                class="truncate text-sm font-bold @if (!$contact->is_read) text-blue-900 @else text-gray-700 @endif">
                                <i
                                    class="fas {{ $contact->is_read ? 'fa-envelope-open text-gray-400' : 'fa-envelope text-blue-600' }} mr-1.5"></i>
                                {{ $contact->name ?? 'Unknown' }}
                            </div>
                            <span
                                class="text-[11px] text-gray-400 whitespace-nowrap shrink-0">{{ $contact->created_at->format('M d') }}</span>
                        </div>
                        <div
                            class="truncate text-xs @if ($contact->is_read) text-gray-500 @else text-gray-800 font-medium @endif">
                            {{ $contact->subject }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm italic">No Inbox messages found.</div>
                @endforelse

                <!-- Section Spam Divider -->
                <div class="p-3 bg-red-50/80 border-y border-red-100 sticky top-0">
                    <h6 class="text-xs font-bold text-red-600 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-exclamation-triangle"></i> Spam
                        <span>({{ $contacts->where('is_spam', true)->count() }})</span>
                    </h6>
                </div>

                <!-- Spam List -->
                @forelse($contacts->where('is_spam', true) as $contact)
                    <div wire:click="selectContact({{ $contact->id }})"
                        class="p-4 cursor-pointer hover:bg-red-50/60 transition-colors 
                        @if ($selectedContact && $selectedContact->id == $contact->id) bg-red-50 border-l-4 border-l-red-500 @endif">
                        <div class="flex justify-between items-start mb-1 gap-2">
                            <div class="truncate text-sm font-bold text-red-700">
                                <i class="fas fa-trash-alt mr-1.5"></i>
                                {{ $contact->name ?? 'Unknown' }}
                            </div>
                            <span
                                class="text-[11px] text-gray-400 whitespace-nowrap shrink-0">{{ $contact->created_at->format('M d') }}</span>
                        </div>
                        <div class="truncate text-xs text-red-500/80">
                            {{ $contact->subject }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-xs italic">No Spam messages found.</div>
                @endforelse
            </div>
        </div>

        <!-- ================= CONTENT AREA (ISI PESAN) ================= -->
        <!-- Di Mobile: Sembunyi jika $selectedContact null. Di Desktop (lg): Selalu Muncul -->
        <div
            class="flex-1 flex flex-col bg-white overflow-y-auto transition-all duration-300
                    {{ !$selectedContact ? 'hidden lg:flex' : 'flex' }}">
            @if ($selectedContact)
                <!-- Navigation Bar Mobile (Top Bar Back Button) -->
                <div
                    class="sticky top-0 bg-white/95 backdrop-blur border-b border-gray-100 p-3 px-4 flex items-center gap-3 lg:hidden z-20 shrink-0">
                    <button wire:click="$set('selectedContact', null)"
                        class="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-full transition-colors flex items-center gap-2 text-sm font-semibold">
                        <i class="fas fa-arrow-left text-base text-blue-600"></i> Back
                    </button>
                    <div class="h-4 w-[1px] bg-gray-200"></div>
                    <span class="font-bold truncate text-sm text-gray-800">{{ $selectedContact->subject }}</span>
                </div>

                <!-- Isi Pesan Container -->
                <div class="p-4 md:p-8 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Subject Title -->
                        <h3 class="text-xl md:text-2xl font-extrabold text-gray-900 mb-6 leading-tight">
                            {{ $selectedContact->subject }}
                        </h3>

                        <!-- Sender Info Header -->
                        <div class="flex flex-wrap justify-between items-center gap-4 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm shrink-0">
                                    {{ strtoupper(substr($selectedContact->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm md:text-base">
                                        {{ $selectedContact->name }}</div>
                                    <div class="text-xs md:text-sm text-gray-500">{{ $selectedContact->email }}</div>
                                </div>
                            </div>
                            <div
                                class="text-xs text-gray-400 flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                                <i class="far fa-calendar-alt"></i>
                                {{ $selectedContact->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div
                            class="mt-6 text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50/70 p-4 md:p-6 rounded-2xl border border-gray-100 text-sm md:text-base">
                            {{ $selectedContact->message }}
                        </div>

                        <!-- Metadata Card -->
                        <div class="mt-6 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <h6
                                class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Message Metadata
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs md:text-sm">
                                <div><span class="font-semibold text-gray-600">User Agent:</span> <span
                                        class="text-gray-500 break-all">{{ $selectedContact->user_agent }}</span></div>
                                <div><span class="font-semibold text-gray-600">IP Address:</span> <code
                                        class="bg-gray-100 px-2 py-0.5 rounded text-blue-600">{{ $selectedContact->ip_address }}</code>
                                </div>
                                <div class="md:col-span-2"><span class="font-semibold text-gray-600">Referrer:</span>
                                    <span
                                        class="text-gray-500 break-all">{{ $selectedContact->referrer ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Flash Messages -->
                        @if (session()->has('success') || session()->has('error') || session()->has('message'))
                            <div class="mt-4">
                                @if (session()->has('success'))
                                    <div
                                        class="p-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div
                                        class="p-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    </div>
                                @endif
                                @if (session()->has('message'))
                                    <div
                                        class="p-3 bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded-lg flex items-center gap-2">
                                        <i class="fas fa-info-circle"></i> {{ session('message') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Action Bar Bottom -->
                    <div
                        class="flex flex-wrap justify-between items-center gap-3 pt-6 mt-6 border-t border-gray-100 shrink-0">
                        <div class="flex gap-2">
                            <button wire:click="markAsSpam({{ $selectedContact->id }})"
                                class="px-3 py-2 text-xs md:text-sm bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold rounded-xl transition-colors flex items-center gap-1.5 border border-amber-200">
                                <i class="fas fa-shield-alt"></i> <span>Spam</span>
                            </button>
                            <button wire:click="deleteContact({{ $selectedContact->id }})"
                                class="px-3 py-2 text-xs md:text-sm bg-red-50 text-red-600 hover:bg-red-100 font-bold rounded-xl transition-colors flex items-center gap-1.5 border border-red-200"
                                onclick="return confirm('Are you sure you want to delete this message?')">
                                <i class="fas fa-trash-alt"></i> <span>Delete</span>
                            </button>
                        </div>

                        <button wire:click="forwardToEmail({{ $selectedContact->id }})"
                            class="px-4 py-2 text-xs md:text-sm bg-blue-600 text-white hover:bg-blue-700 font-bold rounded-xl transition-colors flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-share"></i> Forward
                        </button>
                    </div>
                </div>
            @else
                <!-- Empty State Desktop -->
                <div class="h-full flex flex-col items-center justify-center text-gray-300 p-8 text-center">
                    <i class="fas fa-inbox text-7xl mb-4 opacity-20"></i>
                    <h4 class="text-lg font-medium text-gray-400">Select a message to view details</h4>
                </div>
            @endif
        </div>

    </div>

    <!-- Data Modal (Alpine) -->
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-show="showDataModal" x-cloak
        @click.away="showDataModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.stop>
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h5 class="text-base font-bold text-gray-900">Data Management</h5>
                <button type="button" @click="showDataModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 text-center">
                <div
                    class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-database text-xl"></i>
                </div>
                <p class="text-sm text-gray-600">This section is reserved for Import/Export or other data management
                    functions.</p>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="button" @click="showDataModal = false"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-bold rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
