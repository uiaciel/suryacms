<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\YoutubeVideo;

class YoutubeList extends Component
{
    public $titlePage = "Youtube Videos";
    public $videos;
    public $statusPerVideo = [];

    public $editId, $editTitle, $editCategory, $editDescription;

    public function mount()
    {
        $this->loadVideos();
    }

    public function openEdit($id)
    {
        $video = YoutubeVideo::findOrFail($id);

        $this->editId = $id;
        $this->editTitle = $video->title;
        $this->editCategory = $video->category;
        $this->editDescription = $video->description;

        $this->dispatch('show-edit-modal');
    }

    public function saveEdit()
    {
        $this->validate([
            'editTitle' => 'required',
            'editCategory' => 'required',
            'editDescription' => 'nullable',
        ]);

        YoutubeVideo::findOrFail($this->editId)->update([
            'title' => $this->editTitle,
            'category' => $this->editCategory,
            'description' => $this->editDescription,
        ]);

        session()->flash('success', 'Video updated successfully.');

        $this->loadVideos();

        $this->dispatch('hide-edit-modal');
    }

    public function loadVideos()
    {
        $this->videos = YoutubeVideo::latest()->get();
        $this->statusPerVideo = $this->videos->pluck('status', 'id')->toArray();
    }

    public function delete($id)
    {
        YoutubeVideo::findOrFail($id)->delete();
        session()->flash('success', 'Video berhasil dihapus.');
        $this->loadVideos();
        $this->loadVideos();
    }

    public function editStatus($id)
    {
        $status = $this->statusPerVideo[$id] ?? null;

        if ($status) {
            YoutubeVideo::findOrFail($id)->update(['status' => $status]);
            session()->flash('success', 'Status video berhasil diupdate.');
        }
    }

    public function previewVideo($id)
    {
        $video = YoutubeVideo::findOrFail($id);

        $embedUrl = str_replace('watch?v=', 'embed/', $video->video_url);

        $this->dispatch('open-youtube-modal', videoUrl: $embedUrl);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.youtube.youtube-list')->layout('suryacms::layouts.app');
    }
}
