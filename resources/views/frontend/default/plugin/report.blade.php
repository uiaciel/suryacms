<div class="row g-4 mt-4 justify-content-center">
    @foreach ($reports->sortByDesc('created_at')->take(4) as $report )

    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
        <div class="service-item text-center bg-light overflow-hidden h-100">
            <img class="img-fluid" loading="lazy" src="/storage/{{ $report->image }}" />
            <div class="service-text position-relative text-center h-100 p-4">
                <h5 class="mb-3">
                    {{ $report->title }}
                </h5>
                <p>
                    <a class="small" href="/storage/{{ $report->pdf }}" target="_blank">
                        DOWNLOAD
                        <i class="fa fa-arrow-right ms-3">
                        </i>
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endforeach

</div>
