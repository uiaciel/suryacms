<div x-data="{ open: false }" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between md:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Visitor Counter</h3>
            <p class="text-xs text-gray-500">Detailed traffic overview with breakdown and analysis.</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <button wire:click="refreshStats" type="button"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
            <button type="button" @click="open = !open"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                <i class="fas fa-chevron-down" :class="open ? 'rotate-180' : ''"></i>
                <span x-text="open ? 'Close' : 'Detail'"></span>
            </button>

        </div>
    </div>

    <div class="space-y-6 mt-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Online</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($stats['online'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-2">Active in last 5 minutes</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Today</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['today'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ number_format($stats['unique_today'] ?? 0) }} unique
                    visits
                </p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">This Week</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['this_week'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ number_format($stats['last_week'] ?? 0) }} last week</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-2">All visitor records</p>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="grid gap-4 lg:grid-cols-3 mt-6 w-full max-w-full overflow-hidden">
        <div class="lg:col-span-2 space-y-4 min-w-0">
            <!-- Traffic Analysis -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                <h4 class="text-base font-semibold text-gray-900">Traffic Analysis</h4>
                <div class="mt-4 space-y-3 text-sm text-slate-700">
                    @foreach ($analysis as $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 break-words">
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Visitors -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                <h4 class="text-base font-semibold text-gray-900">Recent Visitors</h4>
                <div class="mt-4 overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                    <table class="w-full min-w-[500px] text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 text-slate-500 uppercase text-[10px] tracking-[0.2em]">
                            <tr>
                                <th class="px-3 py-3">IP Address</th>
                                <th class="px-3 py-3">User Agent</th>
                                <th class="px-3 py-3">Visited Date</th>
                                <th class="px-3 py-3 whitespace-nowrap">Recorded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentVisitors as $visitor)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-slate-800 whitespace-nowrap">
                                        {{ $visitor->ip_address }}
                                        <span class="block text-xs font-normal text-slate-500 mt-0.5">
                                            {{ $visitorCountries[$visitor->ip_address] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="max-w-[150px] sm:max-w-xs truncate"
                                            title="{{ $visitor->user_agent }}">
                                            {{ $visitor->user_agent }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $visitor->visited_date }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        {{ $visitor->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-slate-500 text-center">
                                        No recent visitor data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-4 min-w-0">
            <!-- Top Browsers -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                <h4 class="text-base font-semibold text-gray-900">Top Browsers</h4>
                <div class="mt-4 space-y-2">
                    @foreach ($topBrowsers as $browser => $count)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 gap-2">
                            <span class="text-sm text-slate-700 truncate">{{ $browser }}</span>
                            <span class="text-sm font-semibold text-slate-900 shrink-0">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Platforms -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                <h4 class="text-base font-semibold text-gray-900">Top Platforms</h4>
                <div class="mt-4 space-y-2">
                    @foreach ($topPlatforms as $platform => $count)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 gap-2">
                            <span class="text-sm text-slate-700 truncate">{{ $platform }}</span>
                            <span class="text-sm font-semibold text-slate-900 shrink-0">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
