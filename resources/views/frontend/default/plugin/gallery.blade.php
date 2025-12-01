<section class="mb-3 py-5">
    <div class="d-flex justify-content-center mb-1">
        <h2 class=" border border-5 border-primary px-3 py-1 rounded-5 bg-white ">Gallery</h2>

    </div>
    <h4 class="mb-3 text-center">Berbagai Dokumentasi Kami Di SDIT Gema Insan Mandiri</h4>

    <div class="container wow tada">
        <div class="row">

            @foreach ($gallery as $galleries)
                <div class="col-lg-3 col-md-12">
                    <div class="gallery-item d-flex justify-content-center">
                        <a href="/storage/{{ $galleries->image_path }}" data-lightbox="gallery" class="glightbox"
                            data-title="{{ $galleries->name }}">
                            <img src="/storage/{{ $galleries->image_path }}" alt=""
                                class="object-fit-fill border rounded border-primary mb-3"
                                style="height: 150px;width: 250px;">
                        </a>
                    </div>
                </div><!-- End Gallery Item -->
            @endforeach

        </div>
    </div>
</section>
