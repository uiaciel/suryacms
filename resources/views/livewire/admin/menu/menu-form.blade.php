<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Menu</h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="addMenu">
            <div class="mb-3">
                <label for="newMenuName" class="form-label">Name</label>
                <input type="text" class="form-control" id="newMenuName" wire:model.defer="newMenuName">
                @error('newMenuName') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="newMenuCategory" class="form-label">Category</label>
                <input type="text" class="form-control" id="newMenuCategory" wire:model.defer="newMenuCategory">
                 @error('newMenuCategory') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
             <div class="mb-3">
                <label for="newMenuType" class="form-label">Type</label>
                <select class="form-control" id="newMenuType" wire:model="newMenuType">
                    <option value="link">Link</option>
                    <option value="page">Page</option>
                    <option value="post">Post</option>
                    <option value="category">Category</option>
                </select>
            </div>
             <div class="mb-3">
                @if ($newMenuType === 'link')
                    <label for="newMenuLink" class="form-label">URL</label>
                    <input type="text" class="form-control" id="newMenuLink" wire:model.defer="newMenuLink">
                @elseif($newMenuType === 'page')
                    <label for="newMenuLink" class="form-label">Select Page</label>
                    <select class="form-control" id="newMenuLink" wire:model.defer="newMenuLink">
                        @foreach($pages as $page)
                            <option value="/page/{{ $page->slug }}">{{ $page->title }}</option>
                        @endforeach
                    </select>
                @elseif($newMenuType === 'post')
                    <label for="newMenuLink" class="form-label">Select Post</label>
                     <select class="form-control" id="newMenuLink" wire:model.defer="newMenuLink">
                        @foreach($posts as $post)
                            <option value="/post/{{ $post->slug }}">{{ $post->title }}</option>
                        @endforeach
                    </select>
                @elseif($newMenuType === 'category')
                    <label for="newMenuLink" class="form-label">Select Category</label>
                    <select class="form-control" id="newMenuLink" wire:model.defer="newMenuLink">
                        @foreach($categories as $category)
                            <option value="/category/{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="mb-3">
                <label for="newMenuParentId" class="form-label">Parent Menu</label>
                <select class="form-control" id="newMenuParentId" wire:model.defer="newMenuParentId">
                    <option value="">None</option>
                    @foreach($menus->where('parent_id', null) as $parentMenu)
                        <option value="{{ $parentMenu->id }}">{{ $parentMenu->name }} ({{$parentMenu->category}})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add Menu</button>
        </form>
    </div>
</div>
