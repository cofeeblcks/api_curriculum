<?php

namespace App\Actions\Contact;

use App\Models\ContactMessage;

final class MarkContactMessageRead
{
    public function execute(ContactMessage $message, bool $read = true): ContactMessage
    {
        $message->forceFill([
            'read_at' => $read ? now() : null,
        ])->save();

        return $message->fresh();
    }
}
