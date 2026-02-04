<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

class ThemeParserService
{
    private $dom;

    private $xpath;

    private $themeName;

    private $components = [];

    private $layouts = [];

    private $routes = [];

    private $files = [];

    public function __construct($htmlContent, $themeName)
    {
        $this->themeName = $themeName;
        $this->loadHTML($htmlContent);
    }

    private function loadHTML($htmlContent)
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $this->dom->loadHTML(
            mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);
    }

    public function parse()
    {
        // Parse sections into components
        $this->parseSections();

        // Parse navigation
        $this->parseNavigation();

        // Parse footer
        $this->parseFooter();

        // Generate layout file
        $this->generateLayout();

        // Generate route file
        $this->generateRoutes();

        // Generate component files
        $this->generateComponentFiles();

        return [
            'components' => $this->components,
            'layouts' => $this->layouts,
            'routes' => $this->routes,
            'files' => $this->files,
        ];
    }

    private function parseSections()
    {
        $sections = $this->xpath->query('//section[@id]');

        if ($sections instanceof \DOMNodeList) {
            foreach ($sections as $section) {
                $id = $section->getAttribute('id');
                $content = $this->dom->saveHTML($section);

                $this->components[$id] = [
                    'id' => $id,
                    'type' => 'section',
                    'content' => $content,
                    'class' => $this->generateComponentClass($id),
                ];
            }
        }
    }

    private function parseNavigation()
    {
        $nav = $this->xpath->query('//nav[@id="navigation"]')->item(0);

        if ($nav) {
            $this->components['navigation'] = [
                'id' => 'navigation',
                'type' => 'navigation',
                'content' => $this->dom->saveHTML($nav),
                'class' => 'Components\\Navigation',
            ];
        }
    }

    private function parseFooter()
    {
        $footer = $this->xpath->query('//footer[@id="footer"]')->item(0);

        if ($footer) {
            $this->components['footer'] = [
                'id' => 'footer',
                'type' => 'footer',
                'content' => $this->dom->saveHTML($footer),
                'class' => 'Components\\Footer',
            ];
        }
    }

    private function generateComponentClass($id)
    {
        $className = str_replace('-', ' ', $id);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);

        return "Components\\{$className}";
    }

    private function generateLayout()
    {
        $layoutContent = <<<'BLADE'
<!DOCTYPE html>
<html lang="{{$setting->language}}">
<head>
    @yield('seo')
    @if($setting->language == "en")
    <title>{{ $seo->title ?? $setting->sitename_translation }}</title>
    @else
    <title>{{ $seo->title ?? $setting->sitename }}</title>
    @endif
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="{{ asset('frontend/THEME_NAME/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/THEME_NAME/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    @component('frontend.THEME_NAME.components.navigation')
    @endcomponent

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    @component('frontend.THEME_NAME.components.footer')
    @endcomponent

    <!-- Scripts -->
    <script src="{{ asset('frontend/THEME_NAME/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/THEME_NAME/js/script.js') }}"></script>

    @stack('scripts')
</body>
</html>
BLADE;

        $layoutPath = "layouts/{$this->themeName}.blade.php";
        $this->files[$layoutPath] = $layoutContent;
        $this->layouts[] = $layoutPath;
    }

    private function generateRoutes()
    {
        $routeContent = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// Theme Routes
Route::middleware('web')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
});
PHP;

        $routePath = "routes/theme-{$this->themeName}.php";
        $this->files[$routePath] = $routeContent;
        $this->routes[] = $routePath;
    }

    private function generateComponentFiles()
    {
        foreach ($this->components as $component) {
            $className = basename($component['class']);
            $filename = "components/{$className}.blade.php";

            // Extract inner content from HTML
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML($component['content']);
            libxml_clear_errors();

            $body = $dom->getElementsByTagName('body')->item(0);
            $innerContent = '';

            if ($body) {
                foreach ($body->childNodes as $child) {
                    $innerContent .= $dom->saveHTML($child);
                }
            }

            $componentContent = <<<'BLADE'
<!-- THEME_NAME Component: CLASS_NAME -->
<div class="COMPONENT_ID">
    COMPONENT_CONTENT
</div>
BLADE;

            $componentContent = str_replace('THEME_NAME', $this->themeName, $componentContent);
            $componentContent = str_replace('CLASS_NAME', $className, $componentContent);
            $componentContent = str_replace('COMPONENT_ID', $component['id'], $componentContent);
            $componentContent = str_replace('COMPONENT_CONTENT', $innerContent, $componentContent);

            $this->files[$filename] = $componentContent;
        }
    }

    public function getComponentCount()
    {
        return count($this->components);
    }

    public function getLayoutCount()
    {
        return count($this->layouts);
    }

    public function getRouteCount()
    {
        return count($this->routes);
    }
}
