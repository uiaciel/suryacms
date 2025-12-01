<div>
     @if($latestStock = $stocks->first())
     @php
     $change = $latestStock->perubahan;
     $previousPrice = $latestStock->penutupan_sebelumnya;
     $percentageChange = ($previousPrice > 0) ? ($change / $previousPrice) * 100 : 0;
     $changeClass = $change >= 0 ? 'text-success' : 'text-danger';
     $iconClass = $change >= 0 ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down';
     @endphp
     <div class="card">
         <div class="card-body">
             <div>
                 <div>
                     <p class="card-text">
                         <strong>Closing Price:</strong> Rp {{ number_format($latestStock->harga, 0, ',', '.') }} <span class="badge badge text-bg-primary">IDR</span><br>
                         <strong>Change:</strong>
                         <span class="{{ $changeClass }}">
                             {{ $change }} ({{ number_format($percentageChange, 2) }}%)
                         </span><br>

                         <small class="text-secondary"><em>Last Updated : {{ \Carbon\Carbon::parse($latestStock->waktu_pembaruan)->format('d M Y H:i') }} Wib</em></small>
                     </p>
                     {{-- Anda bisa membuat link ini dinamis jika ada halaman detail --}}
                     {{-- <a href="/share-price/{{ $latestStock->kode_saham }}" class="btn btn-primary btn-sm">Lihat Detail</a> --}}

                 </div>
             </div>
             <img src="/frontend/sge/img/idx.webp" class="img-fluid mt-3" style=" height: 40px; " />
             <a href="/share-price" class="btn btn-primary btn-sm">Lihat Detail</a>
         </div>
     </div>
     @endif

</div>
