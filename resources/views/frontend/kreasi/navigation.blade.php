@foreach($menus as $menu)
    <a href="{{ url($menu->link) }}">{{ $menu->name }}</a>
    @if($menu->children->count())
        <ul>
            @foreach($menu->children as $child)
                <li><a href="{{ url($child->link) }}">{{ $child->name }}</a></li>
            @endforeach
        </ul>
    @endif
@endforeach
