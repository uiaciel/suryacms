<?php

return [

    "info" => [
        "name" => "Surya CMS",
        "path" => "default",
        "version" => "1.0.0",
        "author" => "Kaoskeren Studio",
        "url" => "https://kaoskeren.id",
        "license" => "MIT",
        "description" => "Tema Surya CMS untuk Surya CMS.",
        "theme_preview" => "/frontend/default/theme_preview.jpg"

    ],

    "assets" => [
        "styles" => ['/css/bootstrap-icons.css', '/css/styles.css'],
        "scripts" => ['/js/bootstrap.bundle.min.js', '/js/scripts.js']
    ],

    "custom_blocks" => [
        [
            "id" => "gallery-block",
            "label" => "Gallery",
            "category" => "Plugin",
            "content" => "[[gallery]]",
            "preview" => '<div class="container">
            <div class="jumbotron">
                <h1 class="text-center">Gallery</h1>
                <p class="text-center">Gallery Description for Simple CMS, You Can Edit This Title dan Description.</p>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1477346611705-65d1883cee1e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8M3x8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1477346611705-65d1883cee1e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8M3x8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1477346611705-65d1883cee1e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8M3x8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <figure>
                        <img src="https://images.unsplash.com/photo-1477346611705-65d1883cee1e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8M3x8bW91bnRhaW5zfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=60"
                            class="img-thumbnail rounded-5">

                    </figure>
                </div>
            </div>
        </div>'
        ],
        [
            "id" => "blogs-block",
            "label" => "Blogs",
            "category" => "Plugin",
            "content" => "[[blog]]",
            "preview" => '<section class="py-4 bg-dark">
        <div class="container p-3">
            <h2 class="fw-bold text-white mb-3">Media</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="https://dummyimage.com/600x400/d4d0d4/f0f0f0"
                            class="card-img-top object-fit-cover" alt="Bienvenue sur notre nouveau site internet!"
                            style="height: 200px;">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold mb-3" style="font-size: 20px;">This is Preview Title</h4>
                            <p class="card-text text-muted small" style="line-height: 1.6;">
                                This is Preview Content Nous sommes heureux de vous compter parmi les premiers visiteurs de notre nouveau site
                                internet. Découvrez-le en quelques mots.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="https://dummyimage.com/600x400/d4d0d4/f0f0f0" class="card-img-top object-fit-cover"
                            alt="Titre de ma deuxième actualité" style="height: 200px;">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold mb-3" style="font-size: 20px;">This is Preview Title
                            </h4>
                            <p class="card-text text-muted small" style="line-height: 1.6;">
                                Nunc dolor malesuada sagittis, metus tincidunt, Nec quis fermentum et neque, ac pharetra
                                magna nullam et erat rhoncus odio.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="https://dummyimage.com/600x400/d4d0d4/f0f0f0"
                            class="card-img-top object-fit-cover"
                            alt="Titre de ma troisième actualité un peu plus long sur deux lignes"
                            style="height: 200px;">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold mb-3" style="font-size: 20px;">This is Preview Title</h4>
                            <p class="card-text text-muted small" style="line-height: 1.6;">
                                Amet nunc orci tortor sed mauris feug, cras sed eget a. Donec viverra risus mollis ut et
                                magna et vitae, vitae tellus lacus donec ut.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>'
        ],
        [
            "id" => "youtube-block",
            "label" => "Youtube",
            "category" => "Plugin",
            "content" => "[[youtube]]",
            "preview" => '<section class="py-5">
        <div class="jumbotron">
            <h1 class="text-center">Youtube Video</h1>
            <p class="text-center">Youtube Video Description for Simple CMS, You Can Edit This Title dan Description.</p>
        </div>
        <div class="container mt-4">
            <div class="row">
                <div class="col-lg-8 col-md-12 mb-4">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/VIDEO_ID" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex">
                            <div class="w-25 ratio ratio-16x9 me-3">
                                <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                            </div>
                            <div class="">
                                <h5 class="">Title of Video Youtube</h5>
                                <span>Admin</span>
                            </div>
                        </div>
                    </li>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="w-25 ratio ratio-16x9 me-3">
                                    <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                                </div>
                                <div class="">

                                    <h5 class="">Title of Video Youtube</h5>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="w-25 ratio ratio-16x9 me-3">
                                    <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                                </div>
                                <div class="">
                                    <h5 class="">Title of Video Youtube</h5>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="w-25 ratio ratio-16x9 me-3">
                                    <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                                </div>
                                <div class="">
                                    <h5 class="">Title of Video Youtube</h5>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="w-25 ratio ratio-16x9 me-3">
                                    <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                                </div>
                                <div class="">
                                    <h5 class="">Title of Video Youtube</h5>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="w-25 ratio ratio-16x9 me-3">
                                    <iframe src="https://www.youtube.com/embed/VIDEO_ID_1" title="YouTube video" allowfullscreen></iframe>
                                </div>
                                <div class="">
                                    <h5 class="">Title of Video Youtube</h5>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>'
        ]
    ],

    "sections" => [
        [
            "id" => "section-home",
            "label" => "Default Home",
            "category" => "Themes Sections",
            "content" => <<<HTML
<section id="home">
<header class="bg-dark py-5">
<div class="container px-5">
<div class="row gx-5 align-items-center justify-content-center">
    <div class="col-lg-8 col-xl-7 col-xxl-6">
    <div class="my-5 text-center text-xl-start">
    <h1 class="display-5 fw-bolder text-white mb-2"> A Bootstrap 5 template for modern businesses </h1>
    <p class="lead fw-normal text-white-50 mb-4"> Quickly design and customize responsive mobile-first sites with Bootstrap, the world’s most popular front-end open source toolkit! </p>
    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xl-start">
    <a class="btn btn-primary btn-lg px-4 me-sm-3" href="#features">
        Get Started
    </a>
    <a class="btn btn-outline-light btn-lg px-4" href="#!">
        Learn More
    </a>
    </div>
    </div>
    </div>
    <div class="col-xl-5 col-xxl-6 d-none d-xl-block text-center">
    <img alt="..." class="img-fluid rounded-3 my-5" src="https://dummyimage.com/600x400/343a40/6c757d"/>
    </div>
</div>
</div>
</header>
</section>
HTML
        ],
        [
            "id" => "section-feature",
            "label" => "Default Feature",
            "category" => "Themes Sections",
            "content" => <<<HTML
<section class="py-5" id="features">
<div class="container px-5 my-5">
<div class="row gx-5">
<div class="col-lg-4 mb-5 mb-lg-0">
    <h2 class="fw-bolder mb-0">
    A better way to start building.
    </h2>
</div>
<div class="col-lg-8">
    <div class="row gx-5 row-cols-1 row-cols-md-2">
    <div class="col mb-5 h-100">
    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3">
    <i class="bi bi-collection">
    </i>
    </div>
    <h2 class="h5">
    Featured title
    </h2>
    <p class="mb-0"> Paragraph of text beneath the heading to explain the heading. Here is just a bit more text. </p>
    </div>
    <div class="col mb-5 h-100">
    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3">
    <i class="bi bi-building">
    </i>
    </div>
    <h2 class="h5">
    Featured title
    </h2>
    <p class="mb-0"> Paragraph of text beneath the heading to explain the heading. Here is just a bit more text. </p>
    </div>
    <div class="col mb-5 mb-md-0 h-100">
    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3">
    <i class="bi bi-toggles2">
    </i>
    </div>
    <h2 class="h5">
    Featured title
    </h2>
    <p class="mb-0"> Paragraph of text beneath the heading to explain the heading. Here is just a bit more text. </p>
    </div>
    <div class="col h-100">
    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3">
    <i class="bi bi-toggles2">
    </i>
    </div>
    <h2 class="h5">
    Featured title
    </h2>
    <p class="mb-0"> Paragraph of text beneath the heading to explain the heading. Here is just a bit more text. </p>
    </div>
    </div>
</div>
</div>
</div>
</section>
HTML
        ],
        [
            "id" => "section-div",
            "label" => "Default Div",
            "category" => "Themes Sections",
            "content" => <<<HTML
<section class="" id="testimonial">
<div class="py-5 bg-light">
<div class="container px-5 my-5">
<div class="row gx-5 justify-content-center">
    <div class="col-lg-10 col-xl-7">
    <div class="text-center">
    <div class="fs-4 mb-4 fst-italic">
    "Working with Start Bootstrap templates has saved me tons of development time when building new projects! Starting with a Bootstrap template just makes things easier!"
    </div>
    <div class="d-flex align-items-center justify-content-center">
    <img alt="..." class="rounded-circle me-3" src="https://dummyimage.com/40x40/ced4da/6c757d"/>
    <div class="fw-bold">
        Tom Ato
        <span class="fw-bold text-primary mx-1">
        /
        </span>
        CEO, Pomodoro
    </div>
    </div>
    </div>
    </div>
</div>
</div>
</div>
</section>
HTML
        ],
        [
            "id" => "section-cta",
            "label" => "Default CTA",
            "category" => "Themes Sections",
            "content" => <<<HTML
<aside class="bg-primary bg-gradient rounded-3 p-4 p-sm-5 mt-5">
<div class="d-flex align-items-center justify-content-between flex-column flex-xl-row text-center text-xl-start">
    <div class="mb-4 mb-xl-0">
    <div class="fs-3 fw-bold text-white">
    New products, delivered to you.
    </div>
    <div class="text-white-50">
    Sign up for our newsletter for the latest updates.
    </div>
    </div>
    <div class="ms-xl-4">
    <div class="input-group mb-2">
    <input aria-describedby="button-newsletter" aria-label="Email address..." class="form-control" placeholder="Email address..." type="text"/>
    <button class="btn btn-outline-light" id="button-newsletter" type="button">
    Sign
                                    up
    </button>
    </div>
    <div class="small text-white-50">
    We care about privacy, and will never share your data.
    </div>
    </div>
</div>
</aside>
HTML
        ],
        [
            "id" => "section-blog",
            "label" => "Default Blog",
            "category" => "Themes Sections",
            "content" => <<<HTML
<section class="py-5">
<div class="container px-5 my-5">
<div class="row gx-5 justify-content-center">
<div class="col-lg-8 col-xl-6">
    <div class="text-center">
    <h2 class="fw-bolder">
    From our blog
    </h2>
    <p class="lead fw-normal text-muted mb-5"> Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eaque fugit ratione dicta mollitia. Officiis ad. </p>
    </div>
</div>
</div>
<div class="row gx-5">
<div class="col-lg-4 mb-5">
    <div class="card h-100 shadow border-0">
    <img alt="..." class="card-img-top" src="https://dummyimage.com/600x350/ced4da/6c757d"/>
    <div class="card-body p-4">
    <div class="badge bg-primary bg-gradient rounded-pill mb-2">
    News
    </div>
    <a class="text-decoration-none link-dark stretched-link" href="#!">
    <h5 class="card-title mb-3">
        Blog post title
    </h5>
    </a>
    <p class="card-text mb-0"> Some quick example text to build on the card title and make up the bulk of the card's content. </p>
    </div>
    <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
    <div class="d-flex align-items-end justify-content-between">
    <div class="d-flex align-items-center">
        <img alt="..." class="rounded-circle me-3" src="https://dummyimage.com/40x40/ced4da/6c757d"/>
        <div class="small">
        <div class="fw-bold">
        Kelly Rowan
        </div>
        <div class="text-muted">
        March 12, 2023 · 6 min read
        </div>
        </div>
    </div>
    </div>
    </div>
    </div>
</div>
<div class="col-lg-4 mb-5">
    <div class="card h-100 shadow border-0">
    <img alt="..." class="card-img-top" src="https://dummyimage.com/600x350/adb5bd/495057"/>
    <div class="card-body p-4">
    <div class="badge bg-primary bg-gradient rounded-pill mb-2">
    Media
    </div>
    <a class="text-decoration-none link-dark stretched-link" href="#!">
    <h5 class="card-title mb-3">
        Another blog post title
    </h5>
    </a>
    <p class="card-text mb-0"> This text is a bit longer to illustrate the adaptive height of each card. Some quick example text to build on the card title and make up the bulk of the card's content. </p>
    </div>
    <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
    <div class="d-flex align-items-end justify-content-between">
    <div class="d-flex align-items-center">
        <img alt="..." class="rounded-circle me-3" src="https://dummyimage.com/40x40/ced4da/6c757d"/>
        <div class="small">
        <div class="fw-bold">
        Josiah Barclay
        </div>
        <div class="text-muted">
        March 23, 2023 · 4 min read
        </div>
        </div>
    </div>
    </div>
    </div>
    </div>
</div>
<div class="col-lg-4 mb-5">
    <div class="card h-100 shadow border-0">
    <img alt="..." class="card-img-top" src="https://dummyimage.com/600x350/6c757d/343a40"/>
    <div class="card-body p-4">
    <div class="badge bg-primary bg-gradient rounded-pill mb-2">
    News
    </div>
    <a class="text-decoration-none link-dark stretched-link" href="#!">
    <h5 class="card-title mb-3">
        The last blog post title is a little bit longer than the
                                        others
    </h5>
    </a>
    <p class="card-text mb-0"> Some more quick example text to build on the card title and make up the bulk of the card's content. </p>
    </div>
    <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
    <div class="d-flex align-items-end justify-content-between">
    <div class="d-flex align-items-center">
        <img alt="..." class="rounded-circle me-3" src="https://dummyimage.com/40x40/ced4da/6c757d"/>
        <div class="small">
        <div class="fw-bold">
        Evelyn Martinez
        </div>
        <div class="text-muted">
        April 2, 2023 · 10 min read
        </div>
        </div>
    </div>
    </div>
    </div>
    </div>
</div>
</div>
</div>
</section>
HTML
        ]
    ]

];
