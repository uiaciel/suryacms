<?php

namespace Uiaciel\SuryaCms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Uiaciel\SuryaCMS\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class MonitorController extends Controller
{
    public function index(Request $request)
    {
        // 1. Validasi Token Keamanan dari .env
        $secretToken = config('suryacms.monitor_token');
        if (!$secretToken || $request->header('Authorization') !== 'Bearer ' . $secretToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 2. Hitung Core Metrics (Sesuaikan dengan nama tabel SuryaCMS Anda)
        $inboxTable = Schema::hasTable('inboxes') ? 'inboxes' : (Schema::hasTable('contacts') ? 'contacts' : null);

        $coreMetrics = [
            'posts' => DB::table('posts')->count(),
            'pages' => DB::table('pages')->count(),
            'comments' => DB::table('comments')->count(),
            'inbox_unread' => $inboxTable ? DB::table($inboxTable)->where('is_read', false)->count() : 0,
        ];

        // 3. Hook Custom Metrics (Dinamis berdasarkan package yang ada)
        $customMetrics = [];
        if (Schema::hasTable('reports')) {
            $customMetrics['reports'] = DB::table('reports')->count();
        }
        if (Schema::hasTable('announcements')) {
            $customMetrics['announcements'] = DB::table('announcements')->count();
        }

        // 4. Hitung Data Visitor
        $visitors = [
            'today' => Visitor::where('visited_date', now()->toDateString())->count(),
            'this_week' => Visitor::where('visited_date', '>=', now()->startOfWeek()->toDateString())->count(),
            'this_month' => Visitor::where('visited_date', '>=', now()->startOfMonth()->toDateString())->count(),
            'total' => Visitor::count(),
        ];

        // 5. Cek Storage & Spesifikasi Server
        $freeSpace = disk_free_space(base_path());
        $totalSpace = disk_total_space(base_path());
        $usedPercent = $totalSpace > 0 ? round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2) : 0;

        return response()->json([
            'status' => 'online',
            'suryacms_version' => config('suryacms.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'storage' => [
                'free_human' => round($freeSpace / (1024 * 1024 * 1024), 2) . ' GB',
                'used_percent' => $usedPercent
            ],
            'core_metrics' => $coreMetrics,
            'custom_metrics' => $customMetrics,
            'visitors' => $visitors,
            'uiaciel_packages' => $this->detectUiaCielPackages(),
            'active_theme' => config('suryacms.theme', 'default'),
            'last_backup' => cache('suryacms_last_backup_time', 'Never'),
        ]);
    }

    public function triggerBackup(Request $request)
    {
        $secretToken = config('suryacms.monitor_token');
        if (!$secretToken || $request->header('Authorization') !== 'Bearer ' . $secretToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Jalankan backup via Queue agar tidak timeout
        Artisan::queue('suryacms:backup');

        cache(['suryacms_last_backup_time' => now()->toDateTimeString()], now()->addDays(30));

        return response()->json(['message' => 'Backup has been queued']);
    }

    private function detectUiaCielPackages()
    {
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
        $requires = array_merge($composerJson['require'] ?? [], $composerJson['require-dev'] ?? []);

        return array_values(array_filter(array_keys($requires), function($package) {
            return str_starts_with($package, 'uiaciel/');
        }));
    }
}
