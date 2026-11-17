<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'rating', 'comment', 'approved'])]
class Review extends Model
{
    protected function casts(): array
    {
        return ['rating' => 'integer', 'approved' => 'boolean'];
    }
}
