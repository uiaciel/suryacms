<ol class="list-group list-group-numbered mb-5">
    @foreach ($announcements->sortByDesc('created_at')->take(4) as $announcement )

    <li class="list-group-item d-flex justify-content-between align-items-start">
        <div class="ms-2 me-auto">
            <div class="fw-bold">
                {{ $announcement->title }}
            </div>
            {{formatDate($announcement->datepublish)}}
        </div>
        <button class="btn btn-primary btn-sm" data-bs-target="#ann{{ $announcement->id }}" data-bs-toggle="modal"
            type="button">
            View
        </button>
    </li>
    <div aria-hidden="true" aria-labelledby="exampleModalLabel" class="modal fade" id="ann{{ $announcement->id }}"
        tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{$announcement->category}}
                    </h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                    </button>
                </div>
                <div class="modal-body">
                    <h5>
                        {{$announcement->title}}
                    </h5>
                    <img class="img-fluid" src="/storage/{{ $announcement->image }}" />
                    <a class="btn btn-primary mt-3" href="/announcement/{{ $announcement->slug }}">
                        View More
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</ol>
