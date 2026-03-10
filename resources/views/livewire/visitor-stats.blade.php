<div>
    <div wire:poll.5s="getStats" class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
        <h3 class="text-blue-600 font-bold mb-3 flex items-center gap-2">
            <i class="fas fa-chart-bar"></i> Visitor Statistics
        </h3>
        <ul class="space-y-3">
            <li class="flex justify-between items-center text-sm">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-circle-dot text-green-500"></i> Online Visitors:
                </span>
                <span class="font-bold text-green-600">{{ $onlineVisitors }}</span>
            </li>
            <li class="flex justify-between items-center text-sm">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-sun text-yellow-500"></i> Today:
                </span>
                <span class="font-bold text-gray-800">{{ $todayVisitors }}</span>
            </li>
            <li class="flex justify-between items-center text-sm">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-moon text-purple-500"></i> Yesterday:
                </span>
                <span class="font-bold text-gray-800">{{ $yesterdayVisitors }}</span>
            </li>
            <li class="flex justify-between items-center text-sm">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-calendar-week text-blue-500"></i> This Week:
                </span>
                <span class="font-bold text-gray-800">{{ $thisWeekVisitors }}</span>
            </li>
            <li class="flex justify-between items-center text-sm">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-orange-500"></i> This Month:
                </span>
                <span class="font-bold text-gray-800">{{ $thisMonthVisitors }}</span>
            </li>
            <li class="flex justify-between items-center text-sm border-t border-gray-200 pt-3">
                <span class="text-gray-700 flex items-center gap-2">
                    <i class="fas fa-globe text-teal-500"></i> Total:
                </span>
                <span class="font-bold text-gray-800">{{ $totalVisitors }}</span>
            </li>
        </ul>
    </div>
</div>
