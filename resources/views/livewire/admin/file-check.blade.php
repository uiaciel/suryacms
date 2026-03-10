<div>
    <ul class="space-y-2">
        @foreach ($files as $file)
            @php
                $status = $this->getStatus($file['path']);
                $isAvailable = $status['status'] === 'Ada';
            @endphp
            <li class="flex justify-between items-center p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-sm transition-all duration-200">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-700">{{ $file['label'] }}</span>
                    <span class="text-[10px] text-gray-400 font-mono truncate max-w-[150px]">{{ $file['path'] }}</span>
                </div>

                @if ($isAvailable)
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                        <i class="fas fa-check-circle"></i> Available
                    </span>
                @else
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider badge-pulse">
                        <i class="fas fa-times-circle"></i> Missing
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
