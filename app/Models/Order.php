<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable(['code', 'customer_name', 'phone', 'delivery_type', 'address', 'delivery_date', 'time_slot', 'payment_method', 'subtotal', 'deposit', 'status', 'notes'])]
#[Hidden(['access_token'])]
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pendiente';

    public const STATUS_DELIVERED = 'entregado';

    public const STATUS_CANCELLED = 'cancelado';

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->access_token ??= Str::random(48);
        });
    }

    protected function casts(): array
    {
        return ['delivery_date' => 'date', 'subtotal' => 'integer', 'deposit' => 'integer'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_DELIVERED && ! $this->review()->exists();
    }
}
