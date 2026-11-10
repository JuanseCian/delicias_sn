<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kind', 'slug', 'name', 'description', 'metadata', 'price', 'active', 'sort_order'])]
class CustomizerOption extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'price' => 'integer',
            'active' => 'boolean',
        ];
    }
}
