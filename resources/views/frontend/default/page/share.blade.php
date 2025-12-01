@extends('frontend::app')
@section('content')
<section class="bg-page-header  wow fadeIn" data-wow-delay="0.1s">
    <div class="page-header-overlay">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h1 class="display-4 text-white  slideInDown mb-4">Share Price</h1>
                    </div>
                    <nav aria-label="breadcrumb animated slideInDown">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-white" href="#">Investor Relation</a>
                            </li>
                            <li class="breadcrumb-item text-white active" aria-current="page">
                                Share Price
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container">
    <div class="row mt-4">
        <div class="col-xl-4">
        @if($latestStock = $stocks->first())
        @php
        $change = $latestStock->perubahan;
        $previousPrice = $latestStock->penutupan_sebelumnya;
        $percentageChange = ($previousPrice > 0) ? ($change / $previousPrice) * 100 : 0;
        $changeClass = $change >= 0 ? 'text-success' : 'text-danger';
        $iconClass = $change >= 0 ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down';
        @endphp
        <div class="card">
         <div class="card-header bg-dark text-white">
             SHARE PRICE
         </div>

            <div class="card-body">
                <h5 class="{{ $changeClass }}"><i aria-hidden="true" class="{{ $iconClass }}"></i> {{ $latestStock->kode_saham }}</h5>
                <div>
                    <div>
                        <p class="card-text">
                            <strong>Harga Terakhir:</strong> Rp {{ number_format($latestStock->harga, 0, ',', '.') }} <span class="badge badge text-bg-primary">IDR</span><br>
                            <strong>Perubahan:</strong>
                            <span class="{{ $changeClass }}">
                                {{ $change }} ({{ number_format($percentageChange, 2) }}%)
                            </span><br>

                            <small class="text-secondary"><em>Update Terakhir : {{ \Carbon\Carbon::parse($latestStock->waktu_pembaruan)->format('d M Y H:i') }} Wib</em></small>
                        </p>
                        {{-- Anda bisa membuat link ini dinamis jika ada halaman detail --}}
                        {{-- <a href="/share-price/{{ $latestStock->kode_saham }}" class="btn btn-primary btn-sm">Lihat Detail</a> --}}

                    </div>
                </div>
                <img src="/frontend/sge/img/idx.webp" class="img-fluid mt-3" style=" height: 40px; " />

            </div>
        </div>
        @endif


        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <!-- TradingView Widget BEGIN -->
                    <div class="tradingview-widget-container">
                        <div id="tradingview_0da21"></div>
                        <div class="tradingview-widget-copyright"><a href="https://id.tradingview.com/symbols/IDX-SGER/"
                                rel="noopener" target="_blank"><span class="blue-text">SGER Chart</span></a> oleh
                            TradingView</div>
                        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                        <script type="text/javascript">
                            new TradingView.widget({
                                    "width": 700,
                                    "height": 610,
                                    "symbol": "IDX:SGER",
                                    "interval": "D",
                                    "timezone": "Etc/UTC",
                                    "theme": "light",
                                    "style": "1",
                                    "locale": "id",
                                    "toolbar_bg": "#f1f3f6",
                                    "enable_publishing": false,
                                    "allow_symbol_change": true,
                                    "container_id": "tradingview_0da21"
                                });
                        </script>
                    </div>
                    <!-- TradingView Widget END -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
