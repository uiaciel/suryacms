<li class="list-group-item" wire:key="menu-{{ $menu->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <span>
            @if($menu->children->count() > 0)
                <i class="fas fa-folder"></i>
            @else
                <i class="fas fa-file"></i>
            @endif
            {{ $menu->name }}
            <small class="text-muted">({{ $menu->link }})</small>
        </span>
        <div>
            <button class="btn btn-sm btn-primary" wire:click="showEditModal({{ $menu->id }})">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger" wire:click="deleteMenu({{ $menu->id }})" wire:confirm="Are you sure you want to delete this menu item?">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    @if ($menu->children->count() > 0)
        <ul class="list-group mt-2" wire:sortable-group.item-group="{{$menu->id}}" wire:sortable-group.options="{ animation: 150, handle: '.handle'}" wire:sortable-group.end="saveSubmenuOrder('{{$menu->id}}', $event.target.sortable.toArray())">
            @foreach ($menu->children as $child)
                @include('suryacms::livewire.admin.menu.menu-item', ['menu' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
