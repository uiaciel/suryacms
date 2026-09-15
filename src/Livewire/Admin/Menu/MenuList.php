<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\Menu;

use Livewire\Component;
use Uiaciel\SuryaCMS\Models\Category;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Menu;
use Uiaciel\SuryaCMS\Models\Page;
use Uiaciel\SuryaCMS\Models\Post;

class MenuList extends Component
{
    public $menus;

    public $categories;

    public $posts = [];

    public $pages = [];

    public $editMenuId = null;

    public $editName;

    public $editType;

    public $editLink;

    public $editParentId;

    public $language;

    public $showModalEdit = false;

    public $showCopyModal = false;

    public $sourceCategory;

    public $newCategory;

    protected $listeners = [
        'refreshMenus' => 'refreshMenus',
        'menu-created' => 'refreshMenus',
    ];

    public function mount()
    {
        $this->language = Language::select('code', 'name')->get();
        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        // Load default options for modals
        $this->loadEditOptions();
    }

    public function openModal($category)
    {
        $this->showCopyModal = true;
        $this->sourceCategory = $category;

    }

    public function closeModal()
    {
        $this->showModalEdit = false;
        $this->showCopyModal = false;
        $this->reset(['editMenuId', 'editName', 'editType', 'editLink', 'editParentId', 'sourceCategory', 'newCategory']);
    }

    public function loadEditOptions(): void
    {

        $this->posts = Post::where('status', 'Publish')->get();
        $this->pages = Page::where('status', 'Publish')->get();
        $this->categories = Category::select('id', 'name')->get();

    }

    public function refreshMenus()
    {
        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }

    public function saveOrder($orderData)
    {
        foreach ($orderData as $index => $menuId) {
            Menu::where('id', $menuId)->update(['order' => $index + 1]);
        }

        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu order updated successfully.']);
    }

    public function saveSubmenuOrder($parentId, $orderData)
    {
        foreach ($orderData as $index => $menuId) {
            Menu::where('id', $menuId)->update(['order' => $index + 1]);
        }

        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Submenu order updated successfully.']);
    }

    public function moveUp($menuId, $parentId = null)
    {
        $menu = Menu::find($menuId);

        if ($menu) {
            $previousMenu = Menu::where('parent_id', $parentId)
                ->where('order', '<', $menu->order)
                ->orderBy('order', 'desc')
                ->first();

            if ($previousMenu) {

                $currentOrder = $menu->order;
                $menu->update(['order' => $previousMenu->order]);
                $previousMenu->update(['order' => $currentOrder]);
            }
        }

        $this->refreshMenus();
        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu order updated successfully.']);
    }

    public function moveDown($menuId, $parentId = null)
    {
        $menu = Menu::find($menuId);

        if ($menu) {
            $nextMenu = Menu::where('parent_id', $parentId)
                ->where('order', '>', $menu->order)
                ->orderBy('order', 'asc')
                ->first();

            if ($nextMenu) {

                $currentOrder = $menu->order;
                $menu->update(['order' => $nextMenu->order]);
                $nextMenu->update(['order' => $currentOrder]);
            }
        }

        $this->refreshMenus();
        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu order updated successfully.']);
    }

    public function showEditModal($menuId)
    {

        $menu = Menu::findOrFail($menuId);

        if ($menu) {
            $this->editMenuId = $menu->id;
            $this->editName = $menu->name;
            $this->editType = $menu->type;
            $this->editLink = $menu->link;
            $this->editParentId = $menu->parent_id;

            // Pastikan opsi (posts, pages, cat) sudah terisi
            $this->loadEditOptions();

            $this->showModalEdit = true;

        }

    }

    public function updateMenu()
    {
        $menu = Menu::find($this->editMenuId);
        if ($menu) {
            $menu->update([
                'name' => $this->editName,
                'type' => $this->editType,
                'link' => $this->editLink,
                'parent_id' => $this->editParentId,
            ]);
            $this->refreshMenus();
            $this->showModalEdit = false;
            $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu updated successfully.']);
        }
    }

    public function deleteMenu($menuId)
    {
        $menu = Menu::find($menuId);

        if ($menu) {

            if ($menu->children()->count() > 0) {
                $menu->children()->delete();
            }

            $menu->delete();

            $this->refreshMenus();

            $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu and its submenus deleted successfully.']);
        } else {
            $this->dispatch('swal', ['icon' => 'error', 'text' => 'Menu not found.']);
        }
    }

    public function deleteMenuGroup($category)
    {
        $menus = Menu::where('category', $category)->get();

        if ($menus->count() > 0) {
            Menu::where('category', $category)->delete();
            $this->refreshMenus();
            $this->dispatch('swal', ['icon' => 'success', 'text' => "Menu group '$category' deleted successfully."]);
        } else {
            $this->dispatch('swal', ['icon' => 'error', 'text' => 'Menu group not found.']);
        }
    }

    public function copyMenuGroup()
    {
        $this->validate([
            'newCategory' => 'required|string|different:sourceCategory',
        ]);

        $sourceMenus = Menu::where('category', $this->sourceCategory)
            ->whereNull('parent_id')
            ->get();

        foreach ($sourceMenus as $sourceMenu) {

            $newParentMenu = $sourceMenu->replicate();
            $newParentMenu->category = $this->newCategory;
            $newParentMenu->save();

            if ($sourceMenu->children->count() > 0) {
                foreach ($sourceMenu->children as $child) {
                    $newChild = $child->replicate();
                    $newChild->parent_id = $newParentMenu->id;
                    $newChild->category = $this->newCategory;
                    $newChild->save();
                }
            }
        }

        $this->showCopyModal = false;
        $this->reset(['sourceCategory', 'newCategory']);
        $this->refreshMenus();
        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Menu group copied successfully.']);
    }

    public function render()
    {

        return view('suryacms::livewire.admin.menu.menu-list')->layout('suryacms::layouts.app');
    }
}
