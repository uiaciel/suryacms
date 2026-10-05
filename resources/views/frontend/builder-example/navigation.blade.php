<nav class="site-nav">
    <a class="brand" href="{{ url('/') }}">{{ $setting->sitename ?? 'Surya CMS' }}</a>
    <div class="nav-links">
        @foreach ($menus->where('category', 'Primary') as $menu)
            <a href="{{ $menu->link ?? '#' }}">{{ $menu->name }}</a>
        @endforeach
    </div>
</nav>
