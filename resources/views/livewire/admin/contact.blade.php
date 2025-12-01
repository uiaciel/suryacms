<div class="container-fluid py-4 h-100">

    <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
        <div class="d-flex flex-column">
            <h2 class="fw-bolder mb-0 text-primary">
                <i class="bi bi-inbox-fill me-2"></i> Inbox Messages
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small text-muted">
                    <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item"><a href="/admin/pages" class="text-decoration-none">Inbox</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Messages</li>
                </ol>
            </nav>
        </div>

        <button type="button" class="btn btn-primary rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalData" title="Import/Export Data">
            <i class="bi bi-database me-1"></i> Data
        </button>
    </header>

    <div class="card shadow-lg rounded-4 border-0" style="height: 80vh;">
        <div class="d-flex h-100 flex-column flex-lg-row">
            <div class="p-0 border-end overflow-auto" style="min-width: 300px; max-width: 350px;">

                <div class="p-3 bg-light border-bottom sticky-top">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-list-task me-1"></i> Messages ({{ $contacts->where('is_spam', false)->count() }})
                    </h5>
                </div>

                @forelse($contacts->where('is_spam', false) as $contact)
                <div class="p-3 border-bottom list-group-item list-group-item-action @if(!$contact->is_read) bg-light fw-semibold @endif" wire:click="selectContact({{ $contact->id }})" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-truncate me-2" @if(!$contact->is_read) style="color: var(--bs-primary);" @endif>
                            @if($contact->is_read)
                            <i class="bi bi-envelope-open text-muted me-1"></i>
                            @else
                            <i class="bi bi-envelope-fill text-primary me-1"></i>
                            @endif
                            {{ $contact->name ?? 'Unknown' }}
                        </div>
                        <small class="text-muted text-nowrap">{{ $contact->created_at->format('M d') }}</small>
                    </div>
                    <div class="text-truncate small @if($contact->is_read) text-muted @endif">
                        {{ $contact->subject }}
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted">No Inbox messages found.</div>
                @endforelse

                <div class="p-3 bg-danger-subtle border-bottom border-top">
                    <h6 class="mb-0 text-danger fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Spam ({{ $contacts->where('is_spam', true)->count() }})
                    </h6>
                </div>

                @forelse($contacts->where('is_spam', true) as $contact)
                <div class="p-3 border-bottom list-group-item list-group-item-action text-danger" wire:click="selectContact({{ $contact->id }})" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-truncate me-2">
                            <i class="bi bi-trash-fill me-1"></i>
                            {{ $contact->name ?? 'Unknown' }}
                        </div>
                        <small class="text-muted text-nowrap">{{ $contact->created_at->format('M d') }}</small>
                    </div>
                    <div class="text-truncate small">
                        {{ $contact->subject }}
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted">No Spam messages found.</div>
                @endforelse

            </div>
            <div class="flex-grow-1 overflow-auto p-4 bg-white">
                @if($selectedContact)

                <div class="pb-3 mb-4 border-bottom">
                    <h3 class="mb-2 fw-bold text-dark">{{ $selectedContact->subject }}</h3>
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-5 me-2 text-primary"></i>
                            <div>
                                <strong class="text-dark">{{ $selectedContact->name }}</strong>
                                <span class="text-muted small">&lt;{{ $selectedContact->email }}&gt;</span>
                            </div>
                        </div>
                        <div class="text-muted small mt-2 mt-sm-0">
                            <i class="bi bi-calendar me-1"></i> {{ $selectedContact->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="mb-5 py-3">
                    {!! nl2br(e($selectedContact->message)) !!}
                </div>

                <div class="p-3 bg-light rounded-3">
                    <h6 class="fw-bold mb-2 text-muted">Message Metadata</h6>
                    <div class="row small g-2">
                        <div class="col-md-6"><strong>IP Address:</strong> {{ $selectedContact->ip_address }}</div>
                        <div class="col-md-6"><strong>User Agent:</strong> {{ $selectedContact->user_agent }}</div>
                        <div class="col-md-12"><strong>Referrer:</strong> {{ $selectedContact->referrer ?? 'N/A' }}</div>
                    </div>
                </div>

                @if (session()->has('success') || session()->has('error') || session()->has('message'))
                <div class="mt-3">
                    @if (session()->has('success'))
                    <div class="alert alert-success fw-semibold" role="alert"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
                    @endif
                    @if (session()->has('error'))
                    <div class="alert alert-danger fw-semibold" role="alert"><i class="bi bi-x-octagon-fill me-2"></i> {{ session('error') }}</div>
                    @endif
                    @if (session()->has('message'))
                    <div class="alert alert-info fw-semibold" role="alert"><i class="bi bi-info-circle-fill me-2"></i> {{ session('message') }}</div>
                    @endif
                </div>
                @endif

                <div class="d-flex justify-content-between mt-4 border-top pt-3">
                    <div>
                        <button wire:click="markAsSpam({{ $selectedContact->id }})" class="btn btn-sm btn-warning rounded-pill me-2 fw-semibold">
                            <i class="bi bi-exclamation-octagon me-1"></i> Mark as Spam
                        </button>
                        <button wire:click="deleteContact({{ $selectedContact->id }})" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" onclick="return confirm('Are you sure you want to delete this message?')">
                            <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                    </div>

                    <button wire:click="forwardToEmail({{ $selectedContact->id }})" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                        <i class="bi bi-arrow-return-right me-1"></i> Forward (Test)
                    </button>
                </div>

                @else
                <div class="d-flex justify-content-center align-items-center h-100 flex-column text-muted">
                    <i class="bi bi-inbox-fill display-4 mb-3"></i>
                    <h4 class="fw-light">Select an message to view details</h4>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalData" tabindex="-1" aria-labelledby="modalDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDataLabel">Data Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>This modal is reserved for Import/Export or other data management functions.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
