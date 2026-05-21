<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Contact;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    protected int $cacheTtl = 1440; // 24 hours

    public function index(): \Illuminate\View\View
    {
        $setting = $this->getSetting();
        $locale = App::getLocale();

        if (($setting->homepage_type ?? 'index') === 'homepage') {
            $slug = "homepage-{$locale}";

            // $page = Cache::remember("page.static.{$slug}", $this->cacheTtl, function () use ($slug) {
            //     return Page::where('slug', $slug)
            //         ->where('status', 'Publish')
            //         ->first();
            // });

            $page = Page::where('slug', $slug)
                    ->where('status', 'Publish')
                    ->first();

            if ($page) {
                return view('frontend::homepage', [
                    'html'  => $this->processShortcodes($page->html),
                    'css'   => $page->css,
                    'title' => $page->title, // Asumsi ada helper getLocalized
                ]);
            }
        }

        return view('frontend::index');
    }

    public function postshow(Request $request, $lang, $slug = null): \Illuminate\View\View
    {
        /**
         * Logika Penentuan Slug:
         * Mendukung route dengan prefix {lang} maupun tanpa prefix.
         */
        $actualSlug = $slug ?: $lang;

        $locale = App::getLocale();
        $post = Cache::remember("post.{$actualSlug}.{$locale}", $this->cacheTtl, function () use ($actualSlug) {
            return Post::where('slug', $actualSlug)
                ->where('status', 'Publish')
                ->with(['category', 'user'])
                ->firstOrFail();
        });

        if (app()->environment('production') && rand(1, 10) === 1) {
            $post->increment('view');
        }

        $recentpost = Cache::remember("recent_posts.{$post->id}.{$locale}", 3600, function () use ($post) {
            return Post::where('id', '!=', $post->id)
                ->where('status', 'Publish')
                ->where('language_id', $post->language_id) // Pastikan bahasa sama
                ->latest()
                ->limit(6)
                ->get(['id', 'title', 'slug', 'created_at']);
        });

        return view('frontend::page.post', compact('post', 'recentpost'));
    }

    public function pageshow(Request $request, $lang, $slug = null): \Illuminate\View\View
    {
        /**
         * Logika Penentuan Slug:
         * 1. Jika diakses lewat prefix (/en/about), maka $lang = 'en' dan $slug = 'about'
         * 2. Jika diakses tanpa prefix (/about), maka $lang = 'about' dan $slug = null
         */
        $actualSlug = $slug ?: $lang;

        $locale = app()->getLocale();

        // Pastikan key cache menggunakan slug yang benar
        $cacheKey = "page_content_{$actualSlug}_{$locale}";

        $page = Cache::remember($cacheKey, $this->cacheTtl, function () use ($actualSlug) {
            return \Uiaciel\SuryaCms\Models\Page::where('slug', $actualSlug)
                ->where('status', 'Publish')
                ->first();
        });

        if (!$page) {
            abort(404);
        }

        return view('frontend::page.show', [
            'page' => $page,
        ]);
    }

    /**
     * Display all categories with featured posts
     */
    public function categoryIndex(Request $request, $lang = null): \Illuminate\View\View
    {
        $locale = app()->getLocale();
        $languageId = $this->getLanguageIdFromCode($locale);

        $cacheKey = "categories_index.{$locale}";

        // $categories = Cache::remember($cacheKey, $this->cacheTtl, function () use ($languageId) {
        //     return Category::with(['posts' => function ($query) use ($languageId) {
        //             $query->where('status', 'Publish')
        //                 ->when($languageId, fn($q) => $q->where('language_id', $languageId))
        //                 ->orderBy('view', 'desc')
        //                 ->take(5);
        //         }])->get();
        // });

        $categories = Category::All();
        $posts = Post::where('status', 'Publish')
            ->when($languageId, fn($q) => $q->where('language_id', $languageId))
            ->latest()
            ->paginate(12);

        return view('frontend::category.index', compact('categories', 'posts'));
    }

    /**
     * Category Posts List
     */
    public function category(Request $request, $lang, $slug = null): \Illuminate\View\View
    {
        // Jika $slug null, berarti diakses tanpa prefix lang (param 1 adalah slugnya)
        $actualSlug = $slug ?: $lang;
        $locale = app()->getLocale();

        // Pastikan cache key unik per slug dan locale
        $category = Cache::remember("cat_obj.{$actualSlug}.{$locale}", $this->cacheTtl, function () use ($actualSlug) {
            return Category::where('slug', $actualSlug)->firstOrFail();
        });

        $posts = Post::where('category_id', $category->id)
            ->where('status', 'Publish')
            ->when(is_multilingual(), function($q) use ($locale) {
                return $q->whereHas('language', fn($l) => $l->where('code', $locale));
            })
            ->latest()
            ->paginate(12);

        return view('frontend::page.category', compact('category', 'posts'));
    }

    public function contact(Request $request): \Illuminate\View\View
    {
        return view('frontend::page.contact');
    }

    /**
     * Contact Form Submission
     */
    public function sendcontact(Request $request)
    {
        $validated = $request->validate([
            'sender'  => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        Contact::create([
            'name'       => $validated['sender'],
            'email'      => $validated['email'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', __('Success! Your message has been sent.'));
    }

    public function getPosts()
    {
        $posts = Post::where('status', 'Publish')
            ->get(['id', 'title', 'slug'])
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'link' => '/media/'.$post->slug,
                ];
            });

        return response()->json($posts);
    }

    public function getCategories()
    {
        $categories = Category::all(['id', 'name', 'slug'])
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'link' => '/category/'.$category->slug,
                ];
            });

        return response()->json($categories);
    }

    public function getPages()
    {
        $pages = Page::where('status', 'Publish')
            ->get(['id', 'title', 'slug'])
            ->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'link' => '/'.$page->slug,
                ];
            });

        return response()->json($pages);
    }

    // --- Private Helpers ---

    private function getSetting(): ?Setting
    {
        return Cache::rememberForever('cms_settings', function () {
            return Schema::hasTable('settings') ? Setting::first() : null;
        });
    }

    private function processShortcodes($html): string
    {
        if (!$html) return '';

        return preg_replace_callback('/\[\[(.*?)\]\]/', function ($matches) {
            $viewPath = "frontend::plugin." . trim($matches[1]);
            return view()->exists($viewPath) ? view($viewPath)->render() : $matches[0];
        }, $html);
    }

    private function getLanguageIdFromCode($code)
    {
        $language = Language::where('code', $code)
            ->where('status', 'Publish')
            ->first();

        return $language ? $language->id : null;
    }
}
