@extends('frontend::app')
@section('content')

<section class="bg-page-header  wow fadeIn" data-wow-delay="0.1s">
    <div class="page-header-overlay">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h1 class="display-4 text-white  slideInDown mb-4">Audit Committee Charter</h1>
                    </div>
                    <nav aria-label="breadcrumb animated slideInDown">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">ESG</a>
                            </li>
                            <li class="breadcrumb-item text-white active" aria-current="page">
                                Audit Committee Charter
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container-xxl py-5">
    <!-- Service End -->
    <div class="container">

        <ul class="list-group">

            @forelse ($acc->where('category', 'Audit Committee Charter') as $index => $financial)
            <li
                class="list-group-item list-group-item-action bg-waring d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-file-pdf me-2"></i> {{ $financial->title }}</h5>
                <div>
                    <a href="/report/{{ $financial->slug }}" class="me-2"><i class="fa fa-eye" aria-hidden="true"></i>
                        View </a>
                    <a href="/storage/{{ $financial->pdf }}" class=""><i class="fa fa-download" aria-hidden="true"></i>
                        Download</a>

                </div>
            </li>
            @empty
            <h6>Data Belum tersedia</h6>
            @endforelse
        </ul>
        <div class="mt-5">
            <div class="ir_textDivider reset"></div>
            <p class="ir_textFootnote si_fixed">
                <a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank" title="Get Adobe Reader">
                    <img src="/storage/images/icon_getAdobeReader.gif" class="ir_left" alt="Get Adobe Reader">
                </a>
                <strong>Note:</strong> Files are in Adobe (PDF) format.<br>Please download the free <a
                    href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank">Adobe Acrobat
                    Reader</a>
                to view these documents.
            </p>
        </div>
    </div>
</div>

@endsection