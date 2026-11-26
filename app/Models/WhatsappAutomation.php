<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['key', 'name', 'trigger', 'template_id', 'delay_minutes', 'is_enabled'])]
class WhatsappAutomation extends Model
{
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'delay_minutes' => 'integer',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsappTemplate::class, 'template_id');
    }
}
