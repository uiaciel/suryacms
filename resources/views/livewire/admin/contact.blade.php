<div class="p-4 md:p-6 min-h-screen bg-gray-50" x-data="{ showDataModal: false, mobileSidebar: true }">

    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                <i class="fas fa-inbox"></i> Inbox Messages
            </h2>
            <nav class="mt-1">
                <ol class="flex text-sm text-gray-500 gap-2">
                    <li><a href="/admin" class="hover:text-blue-600 transition-colors">Admin</a></li>
                    <li class="before:content-['/'] before:mr-2"><a href="/admin/pages" class="hover:text-blue-600 transition-colors">Inbox</a></li>
                    <li class="before:content-['/'] before:mr-2 text-gray-700 font-medium">Messages</li>
                </ol>
            </nav>
        </div>

        <button type="button" @click="showDataModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full shadow-sm transition-colors">
            <i class="fas fa-database mr-2"></i> Data
        </button>
    </header>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 flex flex-col">
        <div class="flex flex-col lg:flex-row">
            <!-- Sidebar List -->
            <div
                class="w-full lg:w-80 xl:w-96 border-r border-gray-200 flex flex-col bg-gray-50 transition-all duration-300"
                :class="{ 'hidden lg:flex': !mobileSidebar && $wire.selectedContact, 'flex': mobileSidebar || !$wire.selectedContact }"
            >

                <div class="p-4 bg-white border-b sticky top-0 z-10">
                    <h5 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-list-ul"></i> Messages ({{ $contacts->where('is_spam', false)->count() }})
                    </h5>
                </div>

                <div class="overflow-y-auto flex-1">
                    @forelse($contacts->where('is_spam', false) as $contact)
                    <div wire:click="selectContact({{ $contact->id }}); mobileSidebar = false"
                         class="p-4 border-b border-gray-100 cursor-pointer transition-colors hover:bg-blue-50 @if(!$contact->is_read) bg-white border-l-4 border-l-blue-600 @endif @if($selectedContact && $selectedContact->id == $contact->id) bg-blue-50 @endif">
                        <div class="flex justify-between items-start mb-1">
                            <div class="truncate font-bold @if(!$contact->is_read) text-blue-700 @else text-gray-700 @endif">
                                <i class="fas {{ $contact->is_read ? 'fa-envelope-open text-gray-400' : 'fa-envelope text-blue-600' }} mr-1 w-5"></i>
                                {{ $contact->name ?? 'Unknown' }}
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ $contact->created_at->format('M d') }}</span>
                        </div>
                        <div class="truncate text-sm @if($contact->is_read) text-gray-500 @else text-gray-900 font-medium @endif">
                            {{ $contact->subject }}
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-400 text-sm italic">No Inbox messages found.</div>
                    @endforelse

                    <div class="p-3 bg-red-50 border-y border-red-100">
                        <h6 class="text-xs font-bold text-red-600 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> Spam ({{ $contacts->where('is_spam', true)->count() }})
                        </h6>
                    </div>

                    @forelse($contacts->where('is_spam', true) as $contact)
                    <div wire:click="selectContact({{ $contact->id }}); mobileSidebar = false"
                         class="p-4 border-b border-gray-100 cursor-pointer hover:bg-red-50 transition-colors @if($selectedContact && $selectedContact->id == $contact->id) bg-red-50 @endif">
                        <div class="flex justify-between items-start mb-1">
                            <div class="truncate font-bold text-red-700">
                                <i class="fas fa-trash-alt mr-1 w-5"></i>
                                {{ $contact->name ?? 'Unknown' }}
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ $contact->created_at->format('M d') }}</span>
                        </div>
                        <div class="truncate text-sm text-red-500/80">
                            {{ $contact->subject }}
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-400 text-sm italic">No Spam messages found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Content Area -->
            <div
                class="flex-1 overflow-y-auto bg-white transition-all duration-300"
                :class="{ 'hidden lg:block': mobileSidebar && !$wire.selectedContact, 'block': !mobileSidebar || $wire.selectedContact }"
            >
                @if($selectedContact)
                    <div class="sticky top-0 bg-white/95 backdrop-blur-sm z-10 p-4 border-b border-gray-100 flex items-center gap-4 lg:hidden">
                        <button @click="mobileSidebar = true" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <span class="font-bold truncate text-gray-700">{{ $selectedContact->subject }}</span>
                    </div>

                    <div class="p-6 md:p-10">
                        <h3 class="text-xl md:text-3xl font-extrabold text-gray-900 mb-6 leading-tight">{{ $selectedContact->subject }}</h3>
                        <div class="flex flex-wrap justify-between items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-inner">{{ substr($selectedContact->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $selectedContact->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $selectedContact->email }}</div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-400 flex items-center gap-2">
                                <i class="bi bi-calendar3"></i> {{ $selectedContact->created_at->format('M d, Y H:i') }}
                                <i class="far fa-calendar-alt"></i> {{ $selectedContact->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>

                        <div class="mt-8 prose max-w-none text-gray-700 mb-10 leading-relaxed whitespace-pre-line bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            {{ $selectedContact->message }}
                        </div>

                        <div class="p-4 bg-white rounded-xl border border-gray-200 mb-8 shadow-sm">
                            <h6 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Message Metadata
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div><span class="font-semibold text-gray-600">User Agent:</span> <span class="text-gray-500 break-all">{{ $selectedContact->user_agent }}</span></div>
                                <div><span class="font-semibold text-gray-600">IP Address:</span> <code class="bg-gray-100 px-2 py-0.5 rounded text-blue-600">{{ $selectedContact->ip_address }}</code></div>
                                <div class="md:col-span-2"><span class="font-semibold text-gray-600">Referrer:</span> <span class="text-gray-500 break-all">{{ $selectedContact->referrer ?? 'N/A' }}</span></div>
                            </div>
                        </div>

                        @if (session()->has('success') || session()->has('error') || session()->has('message'))
                            <div class="mb-6">
                                @if (session()->has('success'))
                                    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                                @endif
                                @if (session()->has('message'))
                                    <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center gap-2"><i class="fas fa-info-circle"></i> {{ session('message') }}</div>
                                @endif
                            </div>
                        @endif

                        <div class="flex flex-wrap justify-between items-center gap-4 pt-6 border-t border-gray-100">
                            <div class="flex gap-3">
                                <button wire:click="markAsSpam({{ $selectedContact->id }})" class="px-4 py-2 bg-amber-100 text-amber-700 hover:bg-amber-200 font-bold rounded-lg transition-colors flex items-center gap-2">
                                    <i class="fas fa-shield-alt"></i> <span class="hidden sm:inline">Mark as Spam</span>
                                </button>
                                <button wire:click="deleteContact({{ $selectedContact->id }})" class="px-4 py-2 border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-lg transition-colors flex items-center gap-2" onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="fas fa-trash-alt"></i> <span class="hidden sm:inline">Delete</span>
                                </button>
                            </div>

                            <button wire:click="forwardToEmail({{ $selectedContact->id }})" class="px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas fa-share"></i> Forward
                            </button>
                        </div>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-300">
                        <i class="bi bi-inbox-fill text-8xl mb-4 opacity-20"></i>
                        <h4 class="text-xl font-light">Select a message to view details</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Modal (Alpine) -->
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-show="showDataModal" x-cloak @click.away="showDataModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden" @click.stop>
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h5 class="text-lg font-bold text-gray-900">Data Management</h5>
                <button type="button" @click="showDataModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-8">
                <div class="flex flex-col items-center text-center py-10">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-database text-3xl"></i>
                    </div>
                    <p class="text-gray-600">This section is reserved for Import/Export or other data management functions.</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="button" @click="showDataModal = false" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
