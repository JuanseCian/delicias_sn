<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

#[Fillable(['category_id', 'name', 'slug', 'short_description', 'description', 'image', 'storage_instructions', 'is_featured', 'is_custom', 'active'])]
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getLeadTimeHoursAttribute(): int
    {
        return $this->category->lead_time_hours;
    }

    public function getStartingPriceAttribute(): int
    {
        return (int) $this->sizes->min('price');
    }

    public function replaceImage(UploadedFile $file): void
    {
        $this->deleteStoredImage();
        $this->update(['image' => $file->store('products', 'public')]);
    }

    public function clearImage(): void
    {
        $this->deleteStoredImage();
        $this->update(['image' => null]);
    }

    private function deleteStoredImage(): void
    {
        if ($this->image && ! str_starts_with($this->image, 'http')) {
            Storage::disk('public')->delete($this->image);
        }
    }
}
