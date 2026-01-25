<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\YoutubeVideo;

class YoutubeCreate extends Component
{
    public $video_url;

    public $video_id;

    public $title;

    public $description;

    public $thumbnail_url;

    public $category;

    public $status = 'draft';

    public function updatedVideoUrl($value)
    {
        $videoId = $this->extractYoutubeId($value);
        $this->video_id = $videoId;

        if ($videoId) {
            $this->fetchYoutubeData($videoId);
        }
    }

    private function fetchYoutubeData($videoId)
    {
        $oembed = @file_get_contents("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$videoId}&format=json");

        if ($oembed) {
            $data = json_decode($oembed, true);

            $this->title = $data['title'] ?? null;
            $this->thumbnail_url = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

            // YouTube oEmbed tidak menyediakan deskripsi → user isi manual
            if (! $this->description) {
                $this->description = '';
            }
        }
    }

    public function extractYoutubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&]+)/', $url, $m);

        return $m[1] ?? null;
    }

    public function save()
    {
        $this->validate([
            'video_url' => 'required|url',
            'video_id' => 'required|string',
            'title' => 'required|string',
        ]);

        YoutubeVideo::create([
            'video_url' => $this->video_url,
            'video_id' => $this->video_id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail_url,
            'category' => $this->category,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Video berhasil disimpan!');

        return $this->redirect('/admin/galleries/');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.youtube.youtube-create')
            ->layout('suryacms::layouts.app');
    }
}
