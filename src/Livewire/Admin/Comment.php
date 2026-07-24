<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Livewire\Component;

class Comment extends Component
{
    public $comment;

    public $postId;

    public $author_name;

    public $content;

    public $likes;

    public $ip_address;

    public $user_agent;

    public $status;

    public $approved_at;

    public function mount($comment)
    {
        $this->comment = $comment;
        $this->postId = $comment->post_id;
        $this->author_name = $comment->author_name;
        $this->content = $comment->content;
        $this->likes = $comment->likes;
        $this->ip_address = $comment->ip_address;
        $this->user_agent = $comment->user_agent;
        $this->status = $comment->status;
        $this->approved_at = $comment->approved_at;
    }

    public function addComment()
    {
        $this->validate([
            'author_name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $this->comment->update([
            'author_name' => $this->author_name,
            'content' => $this->content,
            'likes' => $this->likes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'status' => $this->status,
            'post_id' => $this->postId,
        ]);

        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Your comment will be reviewed by an admin.']);
    }

    public function approve()
    {
        $this->comment->approve();
        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Comment approved successfully.']);
    }

    public function reject()
    {
        $this->comment->update(['status' => 'rejected']);
        $this->dispatch('swal', ['icon' => 'success', 'text' => 'Comment rejected successfully.']);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.comment');
    }
}
