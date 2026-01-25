<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Menu;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Menu;

class MenuList extends Component
{
    public $menus;

    public $menux;

    public $categories;

    public $editMenuId = null;

    public $editName;

    public $editType;

    public $editLink;

    public $editParentId;

    public $language;

    public $categoriesmenu = ['Primary', 'Secondary'];

    // Properties for copying menu group
    public $sourceCategory;

    public $newCategory;

    // Properties for dynamic content
    protected $listeners = ['refreshMenus' => 'refreshMenus'];

    public function mount()
    {
        $this->language = Language::All();
        // $this->menus = Menu::orderBy('category')->orderBy('order')->get(); // Group menus by category
        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order'); // Urutkan children berdasarkan order
            }])
            ->orderBy('order') // Urutkan parent menu berdasarkan order
            ->get();

        // $this->categories = ['Primary', 'Secondary'];
    }

    public function refreshMenus()
    {
        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order'); // Urutkan children berdasarkan order
            }])
            ->orderBy('order') // Urutkan parent menu berdasarkan order
            ->get();
    }

    public function saveOrder($orderData)
    {
        foreach ($orderData as $index => $menuId) {
            Menu::where('id', $menuId)->update(['order' => $index + 1]);
        }

        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order'); // Urutkan children berdasarkan order
            }])
            ->orderBy('order') // Urutkan parent menu berdasarkan order
            ->get();

        session()->flash('message', 'Menu order updated successfully.');
    }

    public function saveSubmenuOrder($parentId, $orderData)
    {
        foreach ($orderData as $index => $menuId) {
            Menu::where('id', $menuId)->update(['order' => $index + 1]);
        }

        // Refresh menus to reflect the updated order
        $this->menus = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order'); // Urutkan children berdasarkan order
            }])
            ->orderBy('order') // Urutkan parent menu berdasarkan order
            ->get();

        session()->flash('message', 'Submenu order updated successfully.');
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
                // Swap orders
                $currentOrder = $menu->order;
                $menu->update(['order' => $previousMenu->order]);
                $previousMenu->update(['order' => $currentOrder]);
            }
        }

        $this->refreshMenus();
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
                // Swap orders
                $currentOrder = $menu->order;
                $menu->update(['order' => $nextMenu->order]);
                $nextMenu->update(['order' => $currentOrder]);
            }
        }

        $this->refreshMenus();
    }

    public function showEditModal($menuId)
    {
        $menu = Menu::find($menuId);
        if ($menu) {
            $this->editMenuId = $menu->id;
            $this->editName = $menu->name;
            $this->editType = $menu->type;
            $this->editLink = $menu->link;
            $this->editParentId = $menu->parent_id;

            // Initialize the Alpine.js data
            $this->dispatch('initializeMenuForm', [
                'type' => $menu->type,
                'link' => $menu->link,
            ]);

            $this->dispatch('showEditMenuModal');
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
            $this->dispatch('hideEditMenuModal');
            session()->flash('message', 'Menu updated successfully.');
        }
    }

    public function deleteMenu($menuId)
    {
        $menu = Menu::find($menuId);

        if ($menu) {
            // Delete all submenus if it's a parent menu
            if ($menu->children()->count() > 0) {
                $menu->children()->delete();
            }

            // Delete the menu itself
            $menu->delete();

            // Refresh the menu list
            $this->refreshMenus();

            // Flash success message
            session()->flash('message', 'Menu and its submenus deleted successfully.');
        } else {
            session()->flash('message', 'Menu not found.');
        }
    }

    public function showCopyModal($category)
    {
        $this->sourceCategory = $category;
        $this->dispatch('showCopyMenuModal');
    }

    public function copyMenuGroup()
    {
        $this->validate([
            'newCategory' => 'required|string|different:sourceCategory',
        ]);

        // Get all menus from source category
        $sourceMenus = Menu::where('category', $this->sourceCategory)
            ->whereNull('parent_id')
            ->get();

        foreach ($sourceMenus as $sourceMenu) {
            // Create a copy of the parent menu
            $newParentMenu = $sourceMenu->replicate();
            $newParentMenu->category = $this->newCategory;
            $newParentMenu->save();

            // Copy children if they exist
            if ($sourceMenu->children->count() > 0) {
                foreach ($sourceMenu->children as $child) {
                    $newChild = $child->replicate();
                    $newChild->parent_id = $newParentMenu->id;
                    $newChild->category = $this->newCategory;
                    $newChild->save();
                }
            }
        }

        $this->dispatch('hideCopyMenuModal');
        $this->reset(['sourceCategory', 'newCategory']);
        $this->refreshMenus();
        session()->flash('message', 'Menu group copied successfully.');
    }

    public function render()
    {

        return view('suryacms::livewire.admin.menu.menu-list')->layout('suryacms::layouts.app');
    }
}
