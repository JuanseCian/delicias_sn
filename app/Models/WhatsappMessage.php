<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'direction', 'type', 'recipient', 'body', 'status', 'provider_message_id', 'automation_key', 'attempts', 'error_message', 'provider_payload', 'sent_at'])]
class WhatsappMessage extends Model
{
    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
