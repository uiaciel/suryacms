<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-end mb-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="border-start border-5 border-primary ps-4">
                    <h6 class="text-body text-uppercase mb-2">BLOGS</h6>
                    <h1 class="display-6 mb-0">
                        News & Update
                    </h1>
                </div>
            </div>
        </div>
        <div class="row">

            @forelse ($latestposts as $post)
                <!-- Single News Area -->
                <div class="col-12 col-sm-6 col-lg-4 mb-4">
                    <div class="single-blog-post style-2">
                        <!-- Blog Thumbnail -->
                        <div class="blog-thumbnail">
                            <a href="/media/{{ $post->slug }}">
                                @forelse ($post->gambar() as $gam)
                                    @if ($loop->first)
                                        <img src="{{ $gam }}" alt="{{ $post->title }}"
                                            onerror="this.src='/frontend/img/batu-bara.webp'" class="img-fluid"
                                            loading="lazy">
                                    @else
                                    @endif
                                @empty
                                    <img src="/frontend/sge/img/batu-bara.webp" class="img-fluid">
                                @endforelse
                        </div>

                        <!-- Blog Content -->
                        <div class="blog-content">
                            <span class="post-date">{{ formatDate($post->datepublish) }}</span>
                            <a href="/media/{{ $post->slug }}" class="post-title fw-bold">{{ $post->title }}</a>
                            <a href="#" class="post-author">{{ $post->category->name }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Belum ada Berita</p>
            @endforelse

            <div class="col-12">
                <div class="load-more-button text-center">
                    <a href="/category/news" class="btn newsbox-btn">Load More</a>
                </div>
            </div>

        </div>
    </div>

</div>
