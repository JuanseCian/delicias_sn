<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['event_key', 'event_name', 'payload'])]
class WhatsappWebhookEvent extends Model
{
    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
