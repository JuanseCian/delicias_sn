<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['key', 'name', 'body', 'is_active'])]
class WhatsappTemplate extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function automations(): HasMany
    {
        return $this->hasMany(WhatsappAutomation::class, 'template_id');
    }
}
