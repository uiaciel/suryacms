<?php

namespace Uiaciel\SuryaCms\Http\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Visitor;

class VisitorStats extends Component
{
    public $onlineVisitors = 0;

    public $todayVisitors = 0;

    public $yesterdayVisitors = 0;

    public $thisWeekVisitors = 0;

    public $thisMonthVisitors = 0;

    public $totalVisitors = 0;

    public function mount()
    {
        $this->recordVisitor();
        $this->getStats();
    }

    private function recordVisitor()
    {
        $ip = Request::ip();
        $userAgent = Request::userAgent();

        // Cek apakah pengunjung sudah ada dalam 5 menit terakhir untuk menghindari duplikasi
        $latestVisitor = Visitor::where('ip_address', $ip)->where('created_at', '>', now()->subMinutes(5))->first();

        if (! $latestVisitor) {
            Visitor::create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
    }

    public function getStats()
    {
        // Pengunjung Online: Dalam 5 menit terakhir
        $this->onlineVisitors = Visitor::where('created_at', '>', now()->subMinutes(5))->count();

        // Pengunjung Hari Ini
        $this->todayVisitors = Visitor::whereDate('created_at', Carbon::today())->count();

        // Pengunjung Kemarin
        $this->yesterdayVisitors = Visitor::whereDate('created_at', Carbon::yesterday())->count();

        // Pengunjung Minggu Ini
        $this->thisWeekVisitors = Visitor::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        // Pengunjung Bulan Ini
        $this->thisMonthVisitors = Visitor::whereMonth('created_at', Carbon::now()->month)->count();

        // Total Pengunjung
        $this->totalVisitors = Visitor::count();
    }

    public function render()
    {
        return view('suryacms::livewire.visitor-stats');
    }
}
