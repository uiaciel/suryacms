<?php

namespace Uiaciel\SuryaCms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Uiaciel\SuryaCMS\Models\Visitor;
use Illuminate\Http\Request;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;
use App\Models\User;

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
            'suryacms_version' => $this->getSuryacmsVersion(),
            'php_version' => PHP_VERSION,
            'storage' => [
                'free_human' => round($freeSpace / (1024 * 1024 * 1024), 2) . ' GB',
                'used_percent' => $usedPercent
            ],
            'core_metrics' => $coreMetrics,
            'custom_metrics' => $customMetrics,
            'visitors' => $visitors,
            'uiaciel_packages' => $this->detectUiaCielPackages(),
            'active_theme' => $this->getActiveTheme(),
            'last_backup' => cache('suryacms_last_backup_time', 'Never'),
            'user' => User::orderBy('id')->value('email'),
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

    public function storePost(Request $request)
    {
        $secretToken = config('suryacms.monitor_token');
        if (!$secretToken || $request->header('Authorization') !== 'Bearer ' . $secretToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255',
            'language_id' => 'nullable|integer|exists:languages,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'translation_id' => 'nullable|integer|exists:posts,id',
            'datepublish' => 'nullable|date',
            'tags' => 'nullable',
            'source_url' => 'nullable|url|max:255',
            'source_favicon' => 'nullable|string|max:255',
            'source_title' => 'nullable|string|max:255',
        ]);

        $languageId = $validated['language_id']
            ?? Language::query()->where('status', 'Publish')->value('id')
            ?? Language::query()->value('id');
        $userId = $validated['user_id'] ?? User::query()->orderBy('id')->value('id');

        if (!$languageId || !$userId) {
            return response()->json([
                'message' => 'A language and user are required before importing posts.',
            ], 422);
        }

        $tags = $validated['tags'] ?? null;
        if (is_array($tags)) {
            $tags = implode(', ', array_filter($tags, 'is_scalar'));
        } elseif (!is_null($tags) && !is_string($tags)) {
            throw ValidationException::withMessages([
                'tags' => ['The tags field must be a string or an array.'],
            ]);
        }

        $slug = Str::slug($validated['slug'] ?? $validated['title']);
        $slug = $slug ?: 'post';
        $baseSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $post = Post::create([
            'language_id' => $languageId,
            'translation_id' => $validated['translation_id'] ?? null,
            'user_id' => $userId,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'datepublish' => $validated['datepublish'] ?? null,
            'tags' => $tags,
            'source_url' => $validated['source_url'] ?? null,
            'source_favicon' => $validated['source_favicon'] ?? null,
            'source_title' => $validated['source_title'] ?? null,
            'feature' => 'No',
            'flash' => 'No',
            'view' => 0,
            'status' => 'Draft',
        ]);

        return response()->json([
            'message' => 'Post created as draft.',
            'post' => $post,
        ], 201);
    }

    private function detectUiaCielPackages()
    {
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
        $requires = array_merge($composerJson['require'] ?? [], $composerJson['require-dev'] ?? []);

        return array_values(array_filter(array_keys($requires), function($package) {
            return str_starts_with($package, 'uiaciel/');
        }));
    }

    private function getSuryacmsVersion(): string
    {
        if (class_exists(InstalledVersions::class)) {
            try {
                return InstalledVersions::getPrettyVersion('uiaciel/suryacms') ?: config('suryacms.version', '1.0.0');
            } catch (\Throwable $e) {
                // ignore and fallback
            }
        }

        $packageJsonPath = realpath(__DIR__.'/../../composer.json');
        if ($packageJsonPath && file_exists($packageJsonPath)) {
            $packageJson = json_decode(file_get_contents($packageJsonPath), true);
            if (! empty($packageJson['version'])) {
                return $packageJson['version'];
            }
        }

        $rootComposer = base_path('composer.json');
        if (file_exists($rootComposer)) {
            $rootJson = json_decode(file_get_contents($rootComposer), true);
            if (! empty($rootJson['require']['uiaciel/suryacms'])) {
                return $rootJson['require']['uiaciel/suryacms'];
            }
        }

        return config('suryacms.version', '1.0.0');
    }

    private function getActiveTheme(): string
    {
        if (function_exists('getActiveTheme')) {
            try {
                return getActiveTheme();
            } catch (\Throwable $e) {
                // continue fallback
            }
        }

        try {
            if (Schema::hasTable('settings')) {
                return Setting::value('active_theme') ?: config('frontend.active', 'default');
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        return config('frontend.active', 'default');
    }
}
