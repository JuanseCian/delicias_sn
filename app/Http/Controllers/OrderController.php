<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderCalculator;
use App\Services\WhatsAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): View
    {
        $products = Product::with(['category', 'sizes'])->where('active', true)->get();
        $catalog = $products->map(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'image' => product_image_url($product->image),
            'category' => $product->category->name,
            'sizes' => $product->sizes->map(fn ($size): array => ['id' => $size->id, 'label' => $size->label, 'price' => $size->price])->values(),
        ])->values();

        return view('checkout', compact('catalog'));
    }

    public function store(StoreOrderRequest $request, OrderCalculator $calculator): RedirectResponse
    {
        $items = $calculator->resolveItems($request->validated('items'));
        $totals = $calculator->totals($items);

        $order = DB::transaction(function () use ($request, $items, $totals): Order {
            $year = now()->format('Y');
            $number = Order::whereYear('created_at', $year)->lockForUpdate()->count() + 1;
            $order = Order::create([
                'code' => sprintf('DSN-%s-%04d', $year, $number),
                'customer_name' => $request->validated('customer_name'),
                'phone' => $request->validated('phone'),
                'delivery_type' => $request->validated('delivery_type'),
                'address' => $request->validated('address'),
                'delivery_date' => $request->validated('delivery_date'),
                'time_slot' => $request->validated('time_slot'),
                'payment_method' => $request->validated('payment_method'),
                'subtotal' => $totals['subtotal'],
                'deposit' => $totals['deposit'],
                'status' => 'reservado',
                'notes' => $request->validated('notes'),
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_size_id' => $item['size']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'note' => $item['note'],
                ]);
            }

            return $order;
        });

        return to_route('orders.confirmed', $order->code);
    }

    public function confirmed(string $code, WhatsAppMessage $whatsAppMessage): View
    {
        $order = Order::with(['items.product', 'items.size'])->where('code', $code)->firstOrFail();

        return view('orders.confirmed', [
            'order' => $order,
            'whatsappUrl' => $whatsAppMessage->url($whatsAppMessage->forOrder($order)),
        ]);
    }
}
