<?php

namespace Uiaciel\SuryaCms\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
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
