<?php

namespace Uiaciel\SuryaCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPublished
{
    use Dispatchable, SerializesModels;

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }
}
