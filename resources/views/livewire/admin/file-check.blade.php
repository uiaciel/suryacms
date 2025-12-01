<div>

     <ul class="list-group list-group-flush small">
         @foreach ($files as $file)
         @php
         $status = $this->getStatus($file['path']);
         @endphp
         <li class="list-group-item list-group-item-action py-1 px-2 d-flex justify-content-between align-items-center">
             <div>
                 <strong class="me-2">{{ $file['label'] }}:</strong>

             </div>
             @if ($status['status'] === 'Ada')
             <span class="badge bg-success rounded-pill px-2 py-1 shadow-sm">
                 <i class="bi bi-check-circle-fill me-1"></i> Available
             </span>
             @else
             <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm">
                 <i class="bi bi-x-circle-fill me-1"></i> Missing
             </span>
             @endif
         </li>
         @endforeach
     </ul>

</div>
