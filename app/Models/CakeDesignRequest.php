<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['customer_name', 'phone', 'prompt', 'configuration', 'estimated_price', 'status', 'preview_path'])]
class CakeDesignRequest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'configuration' => 'array',
            'estimated_price' => 'integer',
        ];
    }
}
