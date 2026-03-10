<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\YoutubeVideo;

class YoutubeVideoModal extends Component
{
    public $showModal = false;

    public $youtubeUrl = '';

    public $videosFromDb = [];

    protected $listeners = ['openYoutubeVideoModal' => 'openModal', 'closeGalleryModal' => 'closeModal'];

    public function openModal()
    {
        $this->showModal = true;
        $this->youtubeUrl = '';
        $this->loadVideosFromDatabase();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function loadVideosFromDatabase()
    {
        $this->videosFromDb = YoutubeVideo::where('status', 'published')->orderBy('published_at', 'desc')->get([
            'id',
            'title',
            'video_url',
            'thumbnail_url',
        ])->toArray();
    }

    public function insertVideoUrl()
    {
        $this->validate([
            'youtubeUrl' => 'required|url|regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
        ]);

        $embedHtml = $this->generateYoutubeEmbedCode($this->youtubeUrl);

        if ($embedHtml) {
            $this->dispatch('videoSelectedFromYoutube', ['embedHtml' => $embedHtml]);
            $this->closeModal();
        }
    }

    public function selectVideo($videoId)
    {
        $video = YoutubeVideo::find($videoId);
        if ($video && $video->video_url) {
            $embedHtml = $this->generateYoutubeEmbedCode($video->video_url);
            if ($embedHtml) {
                $this->dispatch('videoSelectedFromYoutube', ['embedHtml' => $embedHtml]);
                $this->closeModal();
            }
        }
    }

    private function generateYoutubeEmbedCode($url)
    {
        // Fungsi untuk mengekstrak ID video dari URL YouTube
        preg_match('#(?:https?://)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)/(?:watch\?v=|embed/|v/|)([a-zA-Z0-9_-]{11})#i', $url, $matches);
        $videoId = $matches[1] ?? null;

        if ($videoId) {
            // Menggunakan iframe responsive Bootstrap atau kustom
            return '<div class="ratio ratio-16x9 my-3"><iframe src="https://www.youtube.com/embed/'.$videoId.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
            // Atau tanpa div responsif jika Anda tidak menggunakan Bootstrap:
            // return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }

        return '';
    }

    public function render()
    {
        return view('suryacms::livewire.admin.youtube-video-modal');
    }
}
