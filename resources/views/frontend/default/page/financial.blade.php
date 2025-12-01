@extends('frontend::app')
@section('content')
<section class="bg-page-header">
    <div class="page-header-overlay">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h1 class="display-4 text-white  slideInDown mb-4">
                            Financial Reports
                        </h1>
                    </div>
                    <nav aria-label="breadcrumb animated slideInDown">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item">
                                <a class="text-white" href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">Investor Relation</a>
                            </li>
                            <li class="breadcrumb-item text-white active" aria-current="page">
                                Financial Reports

                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-4">
    <div class="container">
        <div class="row mb-3">
            <div class="col-lg-12 wow fadeInUp" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                <div class="border-start border-5 border-primary ps-4 mb-3">
                    <!-- <h6 class="text-body text-uppercase mb-2"></h6> -->
                    <h1 class="display-6 mb-0">Annual Reports
                    </h1>
                </div>

            </div>
        </div>
        @php
        // Ambil semua Annual Report
        $annualReports = $reports->where('category', 'Annual Report');

        // Tentukan jumlah card per baris (5)
        $cardsPerLine = 5;

        // Hitung jumlah baris yang dibutuhkan, dibulatkan ke atas
        $totalRows = ceil($annualReports->count() / $cardsPerLine);
        @endphp

        @if ($annualReports->isNotEmpty())
        <div class="container my-4"> {{-- Gunakan container untuk pembatas --}}

            {{-- Menggunakan `row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5` untuk 5 kolom pada layar besar --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">

                @foreach ($annualReports as $annual)
                {{-- Bootstrap akan otomatis mengatur `col` untuk menyesuaikan dengan `row-cols-lg-5` --}}
                <div class="col">
                    <div class="card h-100">
                        {{-- Cover/Gambar diletakkan di atas card --}}
                        <img src="/storage/{{ $annual->image }}" class="card-img-top" alt="{{ $annual->title }}" style="height: 250px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fs-6 fw-bold">{{ $annual->title }}</h5>

                            {{-- Link di bawah --}}
                            <div class="mt-auto">
                                <a href="/storage/{{ $annual->pdf }}" target="_blank" class="btn btn-primary btn-sm w-100">
                                    Download <i class="fa fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p>Tidak ada Annual Report yang tersedia saat ini.</p>
        @endif

        @php
        $financialReports = $reports->where('category', 'Financial Report');
        @endphp

        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6">

                    <div class="wow fadeInUp mb-5" data-wow-delay="0.1s">
                        <div class="border-start border-5 border-primary ps-4">
                            <h1 class="display-6 mb-0">Financial Reports</h1>
                        </div>
                    </div>

                    @if ($financialReports->isNotEmpty())
                    <ul class="list-group list-group-flush shadow-sm">
                        @foreach ($financialReports as $financial)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">

                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-lg text-danger me-3"></i> {{-- Ikon PDF yang lebih menonjol --}}
                                <span class="fw-medium text-dark">{{ $financial->title }}</span>
                            </div>

                            <a href="/storage/{{ $financial->pdf }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill d-flex align-items-center" aria-label="Download {{ $financial->title }}">
                                <i class="fas fa-download me-2"></i> Download
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="alert alert-info" role="alert">
                        Belum ada Laporan Keuangan yang tersedia saat ini.
                    </div>
                    @endif

                    <div class="mt-4 pt-3 border-top text-muted small">
                        <div class="d-flex align-items-center">
                            <img src="/storage/images/icon_getAdobeReader.gif" class="me-3" alt="Get Adobe Reader" style="width: 30px; height: auto;">
                            <div>
                                <strong>Note:</strong> Files are in Adobe (PDF) format.<br>Please download the free <a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank">Adobe Acrobat

                                    Reader</a>

                                to view these documents.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
@endsection

