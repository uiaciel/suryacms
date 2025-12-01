@extends('frontend::app')
@section('seo')
@php
$seo = (object)[
'title' => $page->title,
'keyword' => $page->keyword,
'description' => \Illuminate\Support\Str::limit(strip_tags($page->content), 155, ''),
'image' => $page->thumbnail ?? ($page->gambar()[0] ?? null),
'url' => url()->current(),
];
@endphp
@endsection
@section('content')
<section class="bg-page-header">
    <div class="page-header-overlay">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h1 class="display-4 text-white  slideInDown mb-4">
                            {{ $page->title }}
                        </h1>
                    </div>
                    <nav aria-label="breadcrumb animated slideInDown">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item">
                                <a class="text-white" href="/">@if(session('locale', 'id'))Beranda @else Home @endif</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">@if(session('locale', 'id'))Halaman @else Page @endif</a>
                            </li>
                            <li class="breadcrumb-item text-white active" aria-current="page">
                                {{ $page->title }}

                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-12 wow fadeIn" data-wow-delay="0.5s"
                style="visibility: visible; animation-delay: 0.5s; animation-name: fadeIn;">
                <div class="border-start border-5 border-primary ps-4 mb-5">
                    <h6 class="text-body text-uppercase mb-2"></h6>
                    <h1 class="display-6 mb-0">
                        {{ $page->title }}
                    </h1>
                </div>

                <div class="mb-4">
                    {!! $page->content !!}
                </div>

                @if ($page->pdf)
                <p>
                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#contentId" role="button"
                        aria-expanded="false" aria-controls="contentId">
                        Tampilkan PDF
                    </a>
                </p>
                <div class="collapse" id="contentId">
                    <div class="card card-body">
                        <div class="ratio ratio-1x1">

                            <iframe src="/storage/{{ $page->pdf }}">
                            </iframe>

                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        <div class="border-top mt-4 pt-4">
            <div class="row g-4">
                <div class="col-sm-3 d-flex wow fadeIn" data-wow-delay="0.1s"
                    style="visibility: visible; animation-delay: 0.1s; animation-name: fadeIn;">
                    <i class="fa fa-check fa-2x text-primary flex-shrink-0 me-3"></i>
                    <h6 class="mb-0">Ontime at services</h6>
                </div>
                <div class="col-sm-3 d-flex wow fadeIn" data-wow-delay="0.3s"
                    style="visibility: visible; animation-delay: 0.3s; animation-name: fadeIn;">
                    <i class="fa fa-check fa-2x text-primary flex-shrink-0 me-3"></i>
                    <h6 class="mb-0">24/7 hours services</h6>
                </div>
                <div class="col-sm-3 d-flex wow fadeIn" data-wow-delay="0.5s"
                    style="visibility: visible; animation-delay: 0.5s; animation-name: fadeIn;">
                    <i class="fa fa-check fa-2x text-primary flex-shrink-0 me-3"></i>
                    <h6 class="mb-0">Verified professionals</h6>
                </div>
                <div class="col-sm-3 d-flex wow fadeIn" data-wow-delay="0.7s"
                    style="visibility: visible; animation-delay: 0.5s; animation-name: fadeIn;">
                    <i class="fa fa-check fa-2x text-primary flex-shrink-0 me-3"></i>
                    <h6 class="mb-0">Trading Consultants</h6>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
