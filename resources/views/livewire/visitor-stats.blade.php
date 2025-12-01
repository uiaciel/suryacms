<div>
    <div wire:poll.5s="getStats" class="card shadow-sm mb-3 position-relative d-none d-lg-block">
        <div class="card-body p-3">
            <h6 class="card-title text-primary fw-bold mb-2">
                <i class="fas fa-chart-bar me-2"></i>Visitor
            </h6>
            <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-circle-dot text-success me-2"></i>Visitor Online:
                    </span>
                    <span class="fw-bold text-success">{{ $onlineVisitors }}</span>
                </li>
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-sun me-2"></i>Visitor Today:
                    </span>
                    <span class="fw-bold">{{ $todayVisitors }}</span>
                </li>
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-cloud-moon me-2"></i>Visitor Yesterday:
                    </span>
                    <span class="fw-bold">{{ $yesterdayVisitors }}</span>
                </li>
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-calendar-week me-2"></i>Visitor This Week:
                    </span>
                    <span class="fw-bold">{{ $thisWeekVisitors }}</span>
                </li>
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-calendar-alt me-2"></i>Visitor This Month:
                    </span>
                    <span class="fw-bold">{{ $thisMonthVisitors }}</span>
                </li>
                <li class="d-flex justify-content-between">
                    <span>
                        <i class="fas fa-globe me-2"></i>Total Visitor:
                    </span>
                    <span class="fw-bold">{{ $totalVisitors }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
