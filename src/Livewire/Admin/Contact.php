<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Uiaciel\SuryaCms\Mail\ForwardInbox;
use Uiaciel\SuryaCms\Models\Contact as ContactModel;

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

        if (! $contact) {
            session()->flash('error', 'Pesan tidak ditemukan.');
            return;
        }

        $setting = \Uiaciel\SuryaCms\Models\Setting::first();
        $emailTo = $setting->email_forwarder ?? config('mail.from.address');

        if (!$emailTo) {
            session()->flash('error', 'Email penerus belum dikonfigurasi di Pengaturan.');
            return;
        }

        try {
            Mail::to($emailTo)->send(new ForwardInbox($contact));

            $contact->update(['forwarded_at' => now()]);

            session()->flash('success', 'Pesan berhasil diteruskan ke ' . $emailTo);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal meneruskan pesan: ' . $e->getMessage());
        }
    }

    public function toggleImportant($id)
    {
        $contact = ContactModel::find($id);
        if ($contact) {
            $contact->is_important = !$contact->is_important;
            $contact->save();
        }
    }

    public function toggleStatus($id)
    {
        $contact = ContactModel::find($id);
        if ($contact) {
            $contact->status = ($contact->status === 'Pending') ? 'Resolved' : 'Pending';
            $contact->save();
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.contact')->layout('suryacms::layouts.app');
    }
}
