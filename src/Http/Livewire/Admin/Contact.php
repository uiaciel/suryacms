<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Contact as ContactModel;
use Illuminate\Support\Facades\Mail;
use Uiaciel\SuryaCms\Mail\ForwardInbox;

class Contact extends Component
{

    public $titlePage = 'Inbox';
    public $contacts = [];
    public $activeFilter = 'inbox';
    public $selectedContact = null;

    public function selectContact($id)
    {
        $contact = ContactModel::find($id);

        if ($contact) {
            $contact->is_read = true;
            $contact->save();

            $this->selectedContact = $contact;
            // refresh list
        }
    }

    public function markAsSpam($id)
    {
        $contact = ContactModel::find($id);
        if ($contact) {
            $contact->is_spam = true;
            $contact->save();
        }

        session()->flash('success', 'Message mark as Spam.');
    }

    public function deleteContact($id)
    {
        $contact = ContactModel::find($id);
        if ($contact) {
            $contact->delete();

            $this->selectedContact = null;
        }
    }

    public function forwardToEmail($id)
    {
        $contact = ContactModel::find($id);

        if (!$contact) {
            session()->flash('error', 'Pesan tidak ditemukan.');
            return;
        }

        try {
            Mail::to("emailkamu@gmail.com") // ganti dengan Gmail pribadi
                ->send(new ForwardInbox($contact));

            session()->flash('success', 'Pesan berhasil diteruskan ke email Anda.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal meneruskan pesan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.contact')->layout('suryacms::layouts.app');
    }
}
