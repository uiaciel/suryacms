<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-5">
            <a class="navbar-brand" href="{{ $setting->url }}">
                {{ $setting->sitename }}
            </a>
            <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse" type="button">
                <span class="navbar-toggler-icon">
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    @foreach ($menus->where('category', 'Primary') as $menu)
                    <li class="nav-item {{ $menu->children->count() ? 'dropdown' : '' }}">
                        <a class="nav-link {{ $menu->children->count() ? 'dropdown-toggle' : '' }}"
                            href="{{ $menu->link ?? '#' }}" @if ($menu->children->count()) role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false" @endif>
                            {{ $menu->name }}

                        </a>
                        @if ($menu->children->count())
                        <ul class="dropdown-menu">
                            @foreach ($menu->children as $submenu)
                            <li>
                                <a href="{{ $submenu->link ?? '#' }}" class="dropdown-item">{{ $submenu->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @endforeach

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Blog</a>
                        <ul class="dropdown-menu">
                            @foreach ($categories as $category)
                            <li><a href="/category/{{ $category->slug }}" class="dropdown-item">{{ $category->name
                                    }}</a></li>
                            @endforeach

                        </ul>
                    </li>
                    @if($setting->is_multilingual == "Yes")
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">{{
                            strtoupper(session('locale', 'id')) }}</a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('/id/') }}" class="dropdown-item">Bahasa Indonesia</a></li>
                            <li><a href="{{ url('/en/') }}" class="dropdown-item">English</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
