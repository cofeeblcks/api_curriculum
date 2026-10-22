<?php

namespace App\Actions\Contact;

use App\Models\ContactMessage;
use App\Traits\Models\FillModelData;

final class StoreContactMessage
{
    use FillModelData;

    public function execute(array $data): ContactMessage
    {
        $message = new ContactMessage;
        $message->fill($this->fillData(ContactMessage::class, $data));
        $message->save();

        return $message->fresh();
    }
}
