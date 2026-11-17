<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'customer_name', 'phone', 'delivery_type', 'address', 'delivery_date', 'time_slot', 'payment_method', 'subtotal', 'deposit', 'status', 'notes'])]
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pendiente';

    public const STATUS_DELIVERED = 'entregado';

    public const STATUS_CANCELLED = 'cancelado';

    protected function casts(): array
    {
        return ['delivery_date' => 'date', 'subtotal' => 'integer', 'deposit' => 'integer'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
