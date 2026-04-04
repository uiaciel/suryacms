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

class FrontendController extends Controller
{
    public function index($lang = null)
    {
        $setting = Setting::first();
        $homepageType = $setting->homepage_type ?? 'index';

        // 1. JIKA TYPE = HOMEPAGE
        if ($homepageType === 'homepage') {
            $currentLocale = $lang ?? App::getLocale();

            $page = Page::where('slug', "homepage-{$currentLocale}")->first();

            if ($page) {
                return view('frontend::homepage', [
                    'html'  => $page->html,
                    'css'   => $page->css,
                    'title' => $page->title,
                ]);
            }
        }
        return view('frontend::index');
    }

    public function single()
    {
        $setting = Setting::first();
        $homepageType = $setting->homepage_type ?? 'index';
        $homepageId = $setting->homepage_id ?? null;

        if ($homepageType === 'homepage' && $homepageId) {
            $page = Page::find($homepageId);

            if ($page) {
                $page->html = $this->processShortcodes($page->html);

                return view('frontend::homepage', [
                    'html' => $page->html,
                    'css' => $page->css,
                    'title' => $page->title,
                ]);
            }
        }

        return view('frontend::index');
    }

    public function postshow($slug)
    {

        $post = Post::where('slug', $slug)
            ->where('status', 'Publish')
            ->firstOrFail();

        $post->increment('view');

        $recentpostQuery = Post::where('user_id', $post->user_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'Publish');

        $recentpost = $recentpostQuery->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('frontend::page.post', [
            'post' => $post,
            'recentpost' => $recentpost,
        ]);
    }

    public function pageshow($slug)
    {

        $page = Page::where('slug', $slug)
            ->where('status', 'Publish')
            ->firstOrFail();

        return view('frontend::page.show', [
            'page' => $page,
        ]);
    }

    public function category($slug, $lang = null)
    {
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        $category = Category::where('slug', $slug)->firstOrFail();

        if ($isMultilingual && $lang) {

            $languageId = $this->getLanguageIdFromCode($lang);

            if ($languageId) {
                $posts = Post::where('category_id', $category->id)
                    ->where('status', 'Publish')
                    ->where('language_id', $languageId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
            } else {
                $posts = Post::where('category_id', $category->id)
                    ->where('status', 'Publish')
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
            }
        } else {
            $posts = Post::where('category_id', $category->id)
                ->where('status', 'Publish')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        return view('frontend::page.category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    public function categoryIndex($lang = null)
    {
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        if ($isMultilingual && $lang) {

            $languageId = $this->getLanguageIdFromCode($lang);

            if ($languageId) {
                $categories = Category::with(['posts' => function ($query) use ($languageId) {
                    $query->where('status', 'Publish')
                        ->where('language_id', $languageId)
                        ->orderBy('view', 'desc')
                        ->take(5);
                }])->get();
            } else {
                $categories = Category::with(['posts' => function ($query) {
                    $query->where('status', 'Publish')
                        ->orderBy('view', 'desc')
                        ->take(5);
                }])->get();
            }
        } else {
            $categories = Category::with(['posts' => function ($query) {
                $query->where('status', 'Publish')
                    ->orderBy('view', 'desc')
                    ->take(5);
            }])->get();
        }

        return view('frontend::category.index', [
            'categories' => $categories,
        ]);
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

    public function contact()
    {
        return view('frontend::page.contact');
    }

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
            }

            return $matches[0];
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
