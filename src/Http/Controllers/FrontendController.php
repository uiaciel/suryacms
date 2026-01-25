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

/**
 * Frontend Controller
 *
 * Handles public-facing frontend routes for both multilingual and single-language modes.
 * Views are rendered using the 'frontend' namespace which points to the active theme.
 */
class FrontendController extends Controller
{
    /**
     * Render homepage for multilingual setup
     *
     * @param  string  $lang  Language code (id|en)
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $lang = 'id')
    {
        // Set locale for this request
        session(['locale' => $lang]);
        app()->setLocale($lang);

        $setting = Setting::first();
        $homepageType = $setting->homepage_type ?? 'index';
        $homepageId = $setting->homepage_id ?? null;

        if ($homepageType === 'homepage' && $homepageId) {
            // Get homepage based on language slug
            $slug = $lang === 'en' ? 'homepage-en' : 'homepage-id';
            $page = Page::where('slug', $slug)->first();

            if ($page) {
                // Process shortcodes [[plugin]]
                $page->html = $this->processShortcodes($page->html);

                return view('frontend::homepage', [
                    'html' => $page->html,
                    'css' => $page->css,
                    'title' => $page->title,
                ]);
            } else {
                abort(404, 'Halaman tidak ditemukan.');
            }
        }

        return view('frontend::index');
    }

    /**
     * Render homepage for single-language (non-multilingual) setup
     *
     * @return \Illuminate\View\View
     */
    public function single()
    {
        $setting = Setting::first();
        $homepageType = $setting->homepage_type ?? 'index';
        $homepageId = $setting->homepage_id ?? null;

        if ($homepageType === 'homepage' && $homepageId) {
            $page = Page::find($homepageId);

            if ($page) {
                // Process shortcodes [[plugin]]
                $page->html = $this->processShortcodes($page->html);

                return view('frontend::homepage', [
                    'html' => $page->html,
                    'css' => $page->css,
                    'title' => $page->title,
                ]);
            } else {
                abort(404, 'Halaman tidak ditemukan.');
            }
        }

        return view('frontend::index');
    }

    /**
     * Show individual post/media page
     * Supports both multilingual and single-language modes
     *
     * @param  string  $slug  Post slug
     * @param  string|null  $lang  Language code for multilingual mode
     * @return \Illuminate\View\View
     */
    public function postshow($slug, $lang = null)
    {
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        $query = Post::where('slug', $slug)->where('status', 'Publish');

        if ($isMultilingual && $lang) {
            // Set locale for multilingual mode
            session(['locale' => $lang]);
            app()->setLocale($lang);

            // Get language ID from language code
            $languageId = $this->getLanguageIdFromCode($lang);
            if ($languageId) {
                $query->where('language_id', $languageId);
            }
        }

        $post = $query->firstOrFail();
        $post->increment('view');

        $recentpost = Post::where('user_id', $post->user_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'Publish');

        if ($isMultilingual && $lang) {
            $languageId = $this->getLanguageIdFromCode($lang);
            if ($languageId) {
                $recentpost->where('language_id', $languageId);
            }
        }

        $recentpost = $recentpost->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('frontend::page.post', [
            'post' => $post,
            'recentpost' => $recentpost,
        ]);
    }

    /**
     * Show individual page by slug
     * Only shows published pages to prevent exposing draft content
     * Supports both multilingual and single-language modes
     *
     * @param  string  $slug  Page slug
     * @param  string|null  $lang  Language code for multilingual mode
     * @return \Illuminate\View\View
     */
    public function pageshow($slug, $lang = null)
    {
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        $query = Page::where('slug', $slug)->where('status', 'Publish');

        if ($isMultilingual && $lang) {
            // Set locale for multilingual mode
            session(['locale' => $lang]);
            app()->setLocale($lang);

            // Get language ID from language code
            $languageId = $this->getLanguageIdFromCode($lang);
            if ($languageId) {
                $query->where('language_id', $languageId);
            }
        }

        $page = $query->firstOrFail();

        return view('frontend::page.show', [
            'page' => $page,
        ]);
    }

    /**
     * Show posts in a specific category
     * Supports both multilingual and single-language modes
     *
     * @param  string  $slug  Category slug
     * @param  string|null  $lang  Language code for multilingual mode
     * @return \Illuminate\View\View
     */
    public function category($slug, $lang = null)
    {
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Post::where('category_id', $category->id)->where('status', 'Publish');

        if ($isMultilingual && $lang) {
            // Set locale for multilingual mode
            session(['locale' => $lang]);
            app()->setLocale($lang);

            // Get language ID from language code
            $languageId = $this->getLanguageIdFromCode($lang);
            if ($languageId) {
                $query->where('language_id', $languageId);
            }
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend::page.category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    /**
     * Show all categories with top posts
     *
     * @return \Illuminate\View\View
     */
    public function categoryIndex()
    {
        $topposts = Post::where('status', 'Publish')
            ->orderBy('view', 'desc')
            ->take(25)
            ->get();

        return view('frontend::category.index', [
            'topposts' => $topposts,
        ]);
    }

    /**
     * Get all published posts as JSON (for admin menu builder)
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Get all categories as JSON (for admin menu builder)
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Get all published pages as JSON (for admin menu builder)
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Show contact form page
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('frontend::page.contact');
    }

    /**
     * Handle contact form submission
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendcontact(Request $request)
    {
        $validated = $request->validate([
            'sender' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
            'subject' => 'required|string|max:255',
        ]);

        Contact::create([
            'name' => $validated['sender'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
        ]);

        return redirect()->back()
            ->with('success', 'Thank you for your message. We have received your inquiry and will promptly review its contents. We will respond to your message via email as soon as possible. We appreciate your patience and understanding.');
    }

    /**
     * Process shortcodes [[plugin]] in HTML content
     * Renders view if exists, otherwise returns original shortcode
     *
     * @param  string  $html  HTML content with shortcodes
     * @return string Processed HTML
     */
    private function processShortcodes($html)
    {
        return preg_replace_callback('/\[\[(.*?)\]\]/', function ($matches) {
            $plugin = $matches[1];
            $path = "frontend::plugin.{$plugin}";

            try {
                if (view()->exists($path)) {
                    return view($path)->render();
                }
            } catch (\Exception $e) {
                // Return original shortcode if plugin render fails
            }

            return $matches[0];
        }, $html);
    }

    /**
     * Get language ID from language code (id|en)
     * Returns the corresponding language ID from the database
     *
     * @param  string  $code  Language code (id|en)
     * @return int|null Language ID or null if not found
     */
    private function getLanguageIdFromCode($code)
    {
        $language = Language::where('code', $code)->where('status', 'Active')->first();

        return $language ? $language->id : null;
    }
}
