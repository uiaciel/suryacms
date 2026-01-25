<?php

namespace Uiaciel\SuryaCms\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Uiaciel\SuryaCms\Models\Contact;

class ForwardInbox extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->subject("Forwarded Message: {$this->contact->subject}")
            ->view('suryacms::emails.forward-inbox');
    }
}
