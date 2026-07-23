<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Uiaciel\SuryaCMS\Models\Visitor;

class VisitorDetail extends Component
{
    public array $stats = [];

    public array $analysis = [];

    public array $topBrowsers = [];

    public array $topPlatforms = [];

    public array $visitorCountries = [];

    public Collection $recentVisitors;

    public function mount()
    {
        $this->getStats();
    }

    public function refreshStats()
    {
        $this->getStats();
    }

    public function getStats(): void
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();
        $sevenDaysAgo = $today->copy()->subDays(6);

        $todayCount = Visitor::whereDate('created_at', $today)->count();
        $yesterdayCount = Visitor::whereDate('created_at', $yesterday)->count();
        $thisWeekCount = Visitor::whereBetween('created_at', [$thisWeekStart, $now])->count();
        $lastWeekCount = Visitor::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        $thisMonthCount = Visitor::whereBetween('created_at', [$thisMonthStart, $now])->count();
        $lastMonthCount = Visitor::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $totalCount = Visitor::count();
        $onlineCount = Visitor::where('created_at', '>', now()->subMinutes(5))->count();
        $uniqueToday = Visitor::whereDate('created_at', $today)->distinct('ip_address')->count('ip_address');
        $repeatToday = max(0, $todayCount - $uniqueToday);
        $avgLast7Days = round(Visitor::whereDate('created_at', '>=', $sevenDaysAgo)->count() / 7, 2);

        $this->stats = [
            'online' => $onlineCount,
            'today' => $todayCount,
            'yesterday' => $yesterdayCount,
            'this_week' => $thisWeekCount,
            'last_week' => $lastWeekCount,
            'this_month' => $thisMonthCount,
            'last_month' => $lastMonthCount,
            'total' => $totalCount,
            'unique_today' => $uniqueToday,
            'repeat_today' => $repeatToday,
            'avg_last_7_days' => $avgLast7Days,
            'growth_today' => $this->computeGrowth($todayCount, $yesterdayCount),
            'growth_week' => $this->computeGrowth($thisWeekCount, $lastWeekCount),
            'growth_month' => $this->computeGrowth($thisMonthCount, $lastMonthCount),
            'repeat_rate' => $todayCount > 0 ? round(($repeatToday / $todayCount) * 100, 2) : 0,
        ];

        $this->analysis = $this->buildAnalysis($this->stats);
        $this->topBrowsers = $this->getTopUserAgentBreakdown('browser');
        $this->topPlatforms = $this->getTopUserAgentBreakdown('platform');
        $this->recentVisitors = Visitor::orderByDesc('created_at')->take(10)->get(['ip_address', 'user_agent', 'visited_date', 'created_at']);
        $this->visitorCountries = $this->buildVisitorCountries($this->recentVisitors->pluck('ip_address')->unique()->filter()->values()->all());
    }

    private function buildVisitorCountries(array $ips): array
    {
        $countries = [];

        foreach ($ips as $ip) {
            $countries[$ip] = $this->resolveCountryForIp($ip);
        }

        return $countries;
    }

    private function resolveCountryForIp(string $ip): string
    {
        if ($this->isLocalOrPrivateIp($ip)) {
            return $this->getLocalIpLabel($ip);
        }

        return cache()->remember("visitor_country_{$ip}", now()->addDays(7), function () use ($ip) {
            return $this->lookupCountryFromIp($ip);
        });
    }

    private function isLocalOrPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', '0.0.0.0'], true)) {
            return true;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        return false;
    }

    private function getLocalIpLabel(string $ip): string
    {
        if (in_array($ip, ['127.0.0.1', '::1', '0.0.0.0'], true)) {
            return 'Localhost';
        }

        return 'Private Network';
    }

    private function lookupCountryFromIp(string $ip): string
    {
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,message,country,regionName,city");

            if (! $response->ok()) {
                return 'Unknown';
            }

            $payload = $response->json();
            if (isset($payload['status']) && $payload['status'] === 'success' && ! empty($payload['country'])) {
                $parts = array_filter([
                    $payload['city'] ?? null,
                    $payload['regionName'] ?? null,
                    $payload['country'] ?? null,
                ]);

                return implode(', ', $parts);
            }
        } catch (\Throwable $e) {
            // ignore failures and return unknown
        }

        return 'Unknown';
    }

    private function computeGrowth(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current === 0 ? 0.0 : 100.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function buildAnalysis(array $stats): array
    {
        $isTodayUp = $stats['growth_today'] >= 0;
        $isWeekUp = $stats['growth_week'] >= 0;
        $isMonthUp = $stats['growth_month'] >= 0;

        $analysis = [
            'today' => sprintf(
                "%s %s%% compared to yesterday.",
                $isTodayUp ? 'Visitors are up' : 'Visitors are down',
                abs($stats['growth_today'])
            ),
            'week' => sprintf(
                "%s %s%% compared to last week.",
                $isWeekUp ? 'This week is stronger' : 'This week is weaker',
                abs($stats['growth_week'])
            ),
            'month' => sprintf(
                "%s %s%% compared to last month.",
                $isMonthUp ? 'Monthly traffic is increasing' : 'Monthly traffic is decreasing',
                abs($stats['growth_month'])
            ),
            'repeat' => sprintf(
                "Repeat visitor rate today is %s%% (%s repeat hits).",
                $stats['repeat_rate'],
                number_format($stats['repeat_today'])
            ),
            'average' => sprintf(
                "Average daily visits during the last 7 days: %s.",
                number_format($stats['avg_last_7_days'], 2)
            ),
            'online' => sprintf(
                "%s visitors are currently active within the last 5 minutes.",
                number_format($stats['online'])
            ),
        ];

        return $analysis;
    }

    private function getTopUserAgentBreakdown(string $type): array
    {
        $userAgents = Visitor::latest()->take(500)->pluck('user_agent');
        $breakdown = [];

        foreach ($userAgents as $userAgent) {
            $parsed = $this->parseUserAgent($userAgent);
            $key = $parsed[$type] ?? 'Unknown';
            $breakdown[$key] = ($breakdown[$key] ?? 0) + 1;
        }

        arsort($breakdown);

        return array_slice($breakdown, 0, 6, true);
    }

    private function parseUserAgent(string $userAgent): array
    {
        $browser = 'Unknown Browser';
        $platform = 'Unknown Platform';

        $browsers = [
            'Chrome' => '/chrome/i',
            'Firefox' => '/firefox/i',
            'Safari' => '/safari/i',
            'Edge' => '/edge/i',
            'Opera' => '/opera|opr/i',
            'Internet Explorer' => '/msie|trident/i',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $browser = $name;
                break;
            }
        }

        $platforms = [
            'Windows' => '/windows/i',
            'macOS' => '/macintosh|mac os x/i',
            'Linux' => '/linux/i',
            'Android' => '/android/i',
            'iOS' => '/iphone|ipad/i',
        ];

        foreach ($platforms as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $platform = $name;
                break;
            }
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    public function render()
    {
        return view('suryacms::livewire.admin.visitor-detail');
    }
}
