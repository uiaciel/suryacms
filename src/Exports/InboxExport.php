<?php

namespace Uiaciel\SuryaCms\Exports;

use Uiaciel\SuryaCms\Models\Contact;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InboxExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Contact::query();
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'message',
            'status',
            'is_read',
            'is_spam',
            'ip_address',
            'user_agent',

            'created_at',
            'updated_at',
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->name,
            $contact->email,
            $contact->phone,
            $contact->message,
            $contact->status,
            $contact->is_read,
            $contact->is_spam,
            $contact->ip_address,
            $contact->user_agent,

            $contact->created_at,
            $contact->updated_at,
        ];
    }
} {
}
