<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\PageBuilder;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Services\HtmlToGrapeJsConverter;

class IndexPageBuilder extends Component
{
    public $titlePage = 'Page Builder';

    public $pagesbuilder;

    public $showModal = false;

    public $selectedPageId = null;

    public $rawHtml = '';

    public $rawCss = '';

    public $isLoading = false;

    public $activeTab = 'tips';
    public $selectedTemplate = 0;

    public function mount()
    {
        $this->pagesbuilder = Page::where('is_builder', 1)->get();
    }

    public function openInputModal($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            $this->selectedPageId = $pageId;
            $this->rawHtml = $page->html ?? '';
            $this->rawCss = $page->css ?? '';
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPageId = null;
        $this->rawHtml = '';
        $this->rawCss = '';
    }

    public function getTemplatePrompts()
{
    $themeStyle = get_theme_style(); // Helper untuk mendapatkan framework CSS (tailwind/bootstrap)

    return [
        [
            'title' => 'Long Form Sales Page',
            'description' => 'Halaman penjualan panjang dengan storytelling dan conversion optimization',
            'icon' => 'fas fa-scroll',
            'structure' => [
                'Hero Section dengan Headline',
                'Problem/Pain Points',
                'Solution Introduction',
                'Product Features & Benefits',
                'Social Proof & Testimonials',
                'Pricing & Offer',
                'FAQ Section',
                'Final CTA'
            ],
            'tips' => [
                'Gunakan headline yang kuat dan benefit-oriented',
                'Tambahkan urgency dan scarcity elements',
                'Gunakan storytelling untuk emotional connection',
                'Pastikan CTA button terlihat jelas dan kontras',
                'Optimasi untuk mobile-first experience'
            ],
            'prompt' => $this->generatePrompt('long-form-sales', $themeStyle)
        ],
        [
            'title' => 'Company Profile Landing',
            'description' => 'Landing page profesional untuk company profile dan branding',
            'icon' => 'fas fa-building',
            'structure' => [
                'Hero dengan Company Tagline',
                'About Us Section',
                'Vision & Mission',
                'Services/Products Overview',
                'Team Members',
                'Client Logos',
                'Contact Information',
                'Footer dengan Social Links'
            ],
            'tips' => [
                'Tampilkan company values dengan jelas',
                'Gunakan imagery profesional dan berkualitas tinggi',
                'Tambahkan trust signals (sertifikasi, awards)',
                'Pastikan navigasi mudah dan intuitif',
                'Optimasi loading speed untuk SEO'
            ],
            'prompt' => $this->generatePrompt('company-profile', $themeStyle)
        ],
        [
            'title' => 'Product Sales Landing',
            'description' => 'Landing page fokus penjualan produk dengan conversion focus',
            'icon' => 'fas fa-shopping-cart',
            'structure' => [
                'Product Hero dengan Image',
                'Key Features Grid',
                'How It Works',
                'Benefits List',
                'Customer Reviews',
                'Pricing Plans',
                'Money-back Guarantee',
                'Buy Now CTA'
            ],
            'tips' => [
                'Gunakan product image berkualitas tinggi',
                'Highlight unique selling points',
                'Tambahkan video demo jika memungkinkan',
                'Gunakan social proof yang spesifik',
                'Simplify checkout process'
            ],
            'prompt' => $this->generatePrompt('product-sales', $themeStyle)
        ],
        [
            'title' => 'Product Launch Page',
            'description' => 'Landing page untuk peluncuran produk baru dengan hype building',
            'icon' => 'fas fa-rocket',
            'structure' => [
                'Coming Soon Hero',
                'Countdown Timer',
                'Product Teaser',
                'Early Bird Benefits',
                'Email Signup Form',
                'Feature Sneak Peek',
                'Launch Timeline',
                'Notify Me CTA'
            ],
            'tips' => [
                'Bangun anticipation dengan countdown',
                'Tawarkan early bird special offer',
                'Kumpulkan email untuk launch day notification',
                'Gunakan teaser content yang menarik',
                'Tambahkan referral program'
            ],
            'prompt' => $this->generatePrompt('product-launch', $themeStyle)
        ],
        [
            'title' => 'E-commerce Product Listing',
            'description' => 'Halaman katalog produk untuk toko online',
            'icon' => 'fas fa-store',
            'structure' => [
                'Category Header',
                'Filter & Sort Options',
                'Product Grid',
                'Product Cards',
                'Pagination',
                'Recently Viewed',
                'Related Products',
                'Newsletter Signup'
            ],
            'tips' => [
                'Optimasi product cards untuk quick view',
                'Tambahkan wishlist functionality',
                'Gunakan lazy loading untuk images',
                'Implement infinite scroll atau pagination',
                'Tambahkan quick add to cart'
            ],
            'prompt' => $this->generatePrompt('ecommerce-listing', $themeStyle)
        ],
        [
            'title' => 'Membership/Community Page',
            'description' => 'Landing page untuk membership atau komunitas online',
            'icon' => 'fas fa-users',
            'structure' => [
                'Community Hero',
                'Benefits of Joining',
                'Membership Tiers',
                'Success Stories',
                'Community Features',
                'Events & Activities',
                'Pricing Plans',
                'Join Now CTA'
            ],
            'tips' => [
                'Highlight exclusive member benefits',
                'Tampilkan active community stats',
                'Gunakan testimonial dari existing members',
                'Tambahkan free trial option',
                'Showcase community achievements'
            ],
            'prompt' => $this->generatePrompt('membership', $themeStyle)
        ],
        [
            'title' => 'Blog/Article Page',
            'description' => 'Template untuk halaman blog atau artikel konten',
            'icon' => 'fas fa-newspaper',
            'structure' => [
                'Article Header',
                'Featured Image',
                'Article Content',
                'Author Bio',
                'Related Articles',
                'Comments Section',
                'Share Buttons',
                'Newsletter CTA'
            ],
            'tips' => [
                'Gunakan typography yang readable',
                'Optimasi untuk SEO on-page',
                'Tambahkan table of contents untuk long articles',
                'Implement social sharing yang mudah',
                'Tambahkan reading time estimate'
            ],
            'prompt' => $this->generatePrompt('blog-article', $themeStyle)
        ],
        [
            'title' => 'Pricing Page',
            'description' => 'Halaman pricing dengan comparison table',
            'icon' => 'fas fa-tags',
            'structure' => [
                'Pricing Hero',
                'Pricing Toggle (Monthly/Yearly)',
                'Pricing Cards',
                'Feature Comparison Table',
                'FAQ Section',
                'Money-back Guarantee',
                'Enterprise CTA',
                'Trust Badges'
            ],
            'tips' => [
                'Highlight recommended plan',
                'Gunakan annual discount untuk incentive',
                'Buat feature comparison mudah dibaca',
                'Tambahkan free trial CTA',
                'Tampilkan trust signals di dekat pricing'
            ],
            'prompt' => $this->generatePrompt('pricing', $themeStyle)
        ],
        [
            'title' => 'Portfolio/Showcase Page',
            'description' => 'Halaman portfolio untuk showcase karya atau project',
            'icon' => 'fas fa-images',
            'structure' => [
                'Portfolio Hero',
                'Filter Categories',
                'Portfolio Grid',
                'Project Details Modal',
                'Client Testimonials',
                'Awards & Recognition',
                'Contact CTA',
                'Social Links'
            ],
            'tips' => [
                'Gunakan high-quality images',
                'Implement filter/category system',
                'Tambahkan hover effects untuk interactivity',
                'Showcase project results/metrics',
                'Optimasi image loading performance'
            ],
            'prompt' => $this->generatePrompt('portfolio', $themeStyle)
        ],
        [
            'title' => 'Event/Conference Page',
            'description' => 'Landing page untuk event, webinar, atau conference',
            'icon' => 'fas fa-calendar-alt',
            'structure' => [
                'Event Hero with Date',
                'Countdown Timer',
                'Event Details',
                'Speakers Grid',
                'Schedule/Agenda',
                'Venue Information',
                'Registration Form',
                'Sponsors Logos'
            ],
            'tips' => [
                'Tampilkan countdown yang prominent',
                'Gunakan speaker photos berkualitas',
                'Tambahkan early bird pricing',
                'Implement easy registration flow',
                'Tambahkan calendar integration'
            ],
            'prompt' => $this->generatePrompt('event', $themeStyle)
        ],
        [
            'title' => 'Service Page',
            'description' => 'Halaman detail layanan atau jasa yang ditawarkan',
            'icon' => 'fas fa-concierge-bell',
            'structure' => [
                'Service Hero',
                'Service Overview',
                'Process/How It Works',
                'Service Features',
                'Case Studies',
                'Pricing Packages',
                'FAQ Section',
                'Contact/Quote CTA'
            ],
            'tips' => [
                'Jelaskan service dengan jelas dan spesifik',
                'Tampilkan process steps yang mudah dipahami',
                'Gunakan case studies dengan hasil nyata',
                'Tambahkan clear pricing atau quote request',
                'Optimasi untuk local SEO jika applicable'
            ],
            'prompt' => $this->generatePrompt('service', $themeStyle)
        ],
        [
            'title' => 'Testimonials/Reviews Page',
            'description' => 'Halaman khusus untuk menampilkan customer testimonials',
            'icon' => 'fas fa-quote-right',
            'structure' => [
                'Testimonials Hero',
                'Featured Reviews',
                'Video Testimonials',
                'Review Grid',
                'Stats & Metrics',
                'Case Study Links',
                'Trust Badges',
                'CTA to Try Product'
            ],
            'tips' => [
                'Gunakan real customer photos',
                'Include specific results/metrics',
                'Mix text dan video testimonials',
                'Tambahkan star ratings',
                'Showcase diversity of customers'
            ],
            'prompt' => $this->generatePrompt('testimonials', $themeStyle)
        ]
    ];
}

private function generatePrompt($type, $themeStyle)
{
    $framework = $themeStyle['framework'] ?? 'tailwindcss';
    $colors = $themeStyle['colors'] ?? ['primary' => '#3B82F6', 'secondary' => '#10B981'];
    $fonts = $themeStyle['fonts'] ?? ['heading' => 'Inter', 'body' => 'Inter'];

    $prompts = [
        'long-form-sales' => "Buatkan saya long form sales page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Hero Section: Headline yang powerful, subheadline, CTA button, hero image/illustration
2. Problem Section: 3-4 pain points dengan ikon, storytelling approach
3. Solution Section: Introduction produk/jasa sebagai solusi
4. Features Section: Grid 3 kolom dengan benefit-focused copy
5. Social Proof: Testimonial carousel dengan rating stars
6. Pricing Section: Pricing card dengan highlight recommended plan
7. FAQ Section: Accordion dengan 5-7 pertanyaan umum
8. Final CTA: Urgency-based CTA dengan countdown atau limited offer

REQUIREMENTS:
- Mobile-first responsive design
- Smooth scroll navigation
- Sticky header dengan CTA button
- Animasi subtle pada scroll (fade-in, slide-up)
- Accessibility compliant (ARIA labels, semantic HTML)
- Optimasi untuk conversion (multiple CTA placements)
- Gunakan placeholder images dari unsplash atau placeholder.com

OUTPUT:
Berikan HTML lengkap dan CSS (jika tidak menggunakan tailwind) yang siap digunakan. Pastikan code clean, well-commented, dan production-ready.",

        'company-profile' => "Buatkan saya company profile landing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Hero Section: Company tagline, brief description, CTA buttons (Learn More, Contact)
2. About Section: Company story, mission, vision dengan image
3. Services Section: Grid 4 kolom dengan ikon dan deskripsi singkat
4. Team Section: Team members grid dengan photo, name, position, social links
5. Clients Section: Logo carousel/grid dari client companies
6. Stats Section: Key metrics (years in business, clients served, projects completed)
7. Contact Section: Contact form, address, phone, email, map
8. Footer: Social media links, quick links, newsletter signup

REQUIREMENTS:
- Professional dan corporate design
- Mobile-responsive
- Smooth animations
- SEO optimized structure
- Fast loading (optimized images)
- Accessibility compliant

OUTPUT:
Berikan HTML lengkap dan CSS yang clean dan professional.",

        'product-sales' => "Buatkan saya product sales landing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Product Hero: Large product image, headline, price, rating, buy button
2. Features Grid: 6 key features dengan ikon dan benefit description
3. How It Works: 3-step process dengan numbered icons
4. Benefits Section: Checklist style dengan checkmark icons
5. Testimonials: Customer reviews dengan photos dan ratings
6. Pricing: Single product pricing dengan bundle options
7. Guarantee: Money-back guarantee badge dan explanation
8. FAQ: Common questions accordion
9. Final CTA: Strong buy now button dengan urgency

REQUIREMENTS:
- Product-focused design
- High conversion elements
- Trust signals (badges, guarantees)
- Mobile-optimized
- Fast loading

OUTPUT:
Berikan HTML dan CSS yang siap digunakan untuk product page.",

        'product-launch' => "Buatkan saya product launch page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Hero Section: 'Coming Soon' headline, countdown timer, email signup
2. Product Teaser: Blurred/preview images dengan 'reveal on launch'
3. Early Bird Benefits: List of exclusive early bird perks
4. Features Preview: Sneak peek of product features
5. Timeline: Launch roadmap/milestones
6. Notification Signup: Email form dengan incentive
7. Social Proof: 'Join X people waiting' counter
8. Referral Program: Share to get early access

REQUIREMENTS:
- Build anticipation dan excitement
- Countdown timer yang prominent
- Email capture optimization
- Social sharing buttons
- Mobile-responsive

OUTPUT:
Berikan HTML dan CSS untuk launch page yang engaging.",

        'ecommerce-listing' => "Buatkan saya e-commerce product listing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Category Header: Category name, breadcrumb, product count
2. Filter Sidebar: Price range, categories, brands, ratings
3. Sort Options: Price, popularity, newest, rating
4. Product Grid: 3-4 kolom responsive dengan product cards
5. Product Card: Image, title, price, rating, add to cart button, wishlist
6. Pagination: Page numbers dengan prev/next
7. Recently Viewed: Horizontal scroll section
8. Newsletter: Email signup untuk promotions

REQUIREMENTS:
- Grid layout yang responsive
- Quick view functionality
- Add to cart tanpa page reload
- Filter dan sort yang functional
- Lazy loading images
- Mobile-friendly filters

OUTPUT:
Berikan HTML dan CSS untuk product listing yang user-friendly.",

        'membership' => "Buatkan saya membership/community landing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Hero Section: Community value proposition, join CTA, member count
2. Benefits Section: What members get (exclusive content, networking, etc)
3. Membership Tiers: 3 pricing plans dengan feature comparison
4. Success Stories: Member testimonials dengan before/after
5. Community Features: Forums, events, resources, mentorship
6. Events Calendar: Upcoming events dan webinars
7. FAQ: Common questions about membership
8. Join CTA: Final call-to-action dengan guarantee

REQUIREMENTS:
- Community-focused design
- Clear value proposition
- Trust building elements
- Easy signup process
- Mobile-responsive

OUTPUT:
Berikan HTML dan CSS untuk membership page yang compelling.",

        'blog-article' => "Buatkan saya blog/article page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Article Header: Title, meta (date, author, reading time), featured image
2. Table of Contents: Sticky sidebar dengan anchor links
3. Article Content: Well-formatted content dengan headings, images, quotes
4. Author Bio: Author photo, bio, social links
5. Related Articles: 3 related posts grid
6. Comments Section: Comment form dan existing comments
7. Share Buttons: Social media sharing
8. Newsletter CTA: Subscribe for more content

REQUIREMENTS:
- Readable typography
- SEO optimized structure
- Social sharing integration
- Mobile-responsive
- Fast loading
- Accessibility compliant

OUTPUT:
Berikan HTML dan CSS untuk blog page yang clean dan readable.",

        'pricing' => "Buatkan saya pricing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Pricing Hero: Headline, subtitle, billing toggle (monthly/yearly)
2. Pricing Cards: 3 tiers (Basic, Pro, Enterprise) dengan highlight
3. Feature Comparison: Detailed table comparing all features
4. FAQ Section: Pricing-related questions
5. Guarantee: Money-back guarantee badge
6. Enterprise CTA: Custom pricing contact form
7. Trust Badges: Security, satisfaction guarantees
8. Testimonials: Customer quotes about value

REQUIREMENTS:
- Clear pricing presentation
- Easy comparison
- Annual discount highlight
- Trust building elements
- Mobile-responsive
- Accessible

OUTPUT:
Berikan HTML dan CSS untuk pricing page yang conversion-focused.",

        'portfolio' => "Buatkan saya portfolio/showcase page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Portfolio Hero: Creative headline, brief intro
2. Filter Categories: All, Web Design, Branding, Photography, etc
3. Portfolio Grid: Masonry atau grid layout dengan hover effects
4. Project Modal: Lightbox dengan project details
5. Client Testimonials: Quotes from clients
6. Awards Section: Recognition dan achievements
7. Contact CTA: Start a project button
8. Social Links: Behance, Dribbble, Instagram

REQUIREMENTS:
- Visual-focused design
- Smooth animations
- Filter functionality
- Lazy loading images
- Mobile-responsive
- Fast performance

OUTPUT:
Berikan HTML dan CSS untuk portfolio page yang visually stunning.",

        'event' => "Buatkan saya event/conference landing page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Event Hero: Event name, date, location, countdown timer, register CTA
2. About Event: Event description, what to expect
3. Speakers Grid: Speaker photos, names, titles, topics
4. Schedule: Day-by-day agenda dengan time slots
5. Venue Info: Location details, map, accommodation
6. Sponsors: Sponsor logos grid
7. Registration: Ticket types dengan pricing
8. FAQ: Event-related questions

REQUIREMENTS:
- Event-focused design
- Countdown timer prominent
- Easy registration flow
- Mobile-responsive
- Calendar integration
- Social sharing

OUTPUT:
Berikan HTML dan CSS untuk event page yang engaging.",

        'service' => "Buatkan saya service page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Service Hero: Service name, value proposition, CTA
2. Service Overview: Detailed description dengan image
3. Process Section: Step-by-step how it works
4. Features/Benefits: What's included
5. Case Studies: Success stories dengan results
6. Pricing Packages: Service tiers atau packages
7. FAQ: Service-related questions
8. Contact/Quote: Request quote form

REQUIREMENTS:
- Clear service explanation
- Trust building elements
- Easy contact process
- Mobile-responsive
- SEO optimized

OUTPUT:
Berikan HTML dan CSS untuk service page yang professional.",

        'testimonials' => "Buatkan saya testimonials/reviews page menggunakan {$framework} dengan konfigurasi berikut:

STYLE CONFIGURATION:
- Framework: {$framework}
- Primary Color: {$colors['primary']}
- Secondary Color: {$colors['secondary']}
- Heading Font: {$fonts['heading']}
- Body Font: {$fonts['body']}

STRUKTUR HALAMAN:
1. Testimonials Hero: Headline, overall rating, review count
2. Featured Reviews: 3 highlighted testimonials
3. Video Testimonials: Video embed section
4. Review Grid: All reviews dengan filters (rating, date)
5. Stats Section: Average rating, total reviews, recommendation rate
6. Case Studies: Links to detailed case studies
7. Trust Badges: Review platform badges
8. CTA: Try it yourself button

REQUIREMENTS:
- Authentic feel
- Star ratings prominent
- Filter functionality
- Mobile-responsive
- Fast loading
- Social proof focused

OUTPUT:
Berikan HTML dan CSS untuk testimonials page yang trustworthy."
    ];

    return $prompts[$type] ?? $prompts['long-form-sales'];
}

    public function saveHtmlCss()
    {
        // Validasi input
        if (empty($this->rawHtml)) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Validation Error',
                'text' => 'HTML content cannot be empty',
            ]);

            return;
        }

        try {
            $this->isLoading = true;

            $page = Page::find($this->selectedPageId);
            if (! $page) {
                throw new \Exception('Page not found');
            }

            // Convert HTML to GrapeJS compatible format
            $converter = new HtmlToGrapeJsConverter;
            $convertedHtml = $converter->convert($this->rawHtml);

            // Save to database
            $page->update([
                'html' => $convertedHtml,
                'css' => $this->rawCss,
            ]);

            $this->isLoading = false;

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'HTML and CSS have been converted and saved. Ready for GrapeJS editing!',
            ]);

            // Refresh the list
            $this->pagesbuilder = Page::where('is_builder', 1)->get();
            $this->closeModal();
        } catch (\Exception $e) {
            $this->isLoading = false;

            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function delete($pageId)
    {
        try {
            $page = Page::find($pageId);
            if ($page) {
                $page->delete();
                $this->pagesbuilder = Page::where('is_builder', 1)->get();

                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Deleted',
                    'text' => 'Page has been deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.page-builder.index')->layout('suryacms::layouts.app');
    }
}
