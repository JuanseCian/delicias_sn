<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'slug', 'short_description', 'description', 'image', 'storage_instructions', 'is_featured', 'is_custom', 'active'])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_custom' => 'boolean', 'active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredient');
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class, 'product_allergen');
    }

    public function getLeadTimeHoursAttribute(): int
    {
        return $this->is_custom ? max(168, $this->category->lead_time_hours) : $this->category->lead_time_hours;
    }

    public function getStartingPriceAttribute(): int
    {
        return (int) $this->sizes->min('price');
    }
}
