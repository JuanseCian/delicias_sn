<?php

namespace App\Http\Requests;

use App\Services\OrderCalculator;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items');

        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_size_id' => ['required', 'integer', 'exists:product_sizes,id'],
            'items.*.color_id' => ['nullable', 'integer', 'exists:product_colors,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.note' => ['nullable', 'string', 'max:240'],
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'delivery_type' => ['required', Rule::in(['envio', 'retiro'])],
            'address' => ['nullable', 'string', 'max:180', Rule::requiredIf(fn (): bool => $this->input('delivery_type') === 'envio')],
            'delivery_date' => [
                'required',
                'date_format:Y-m-d',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $date = CarbonImmutable::createFromFormat('Y-m-d', $value);
                    $minimumDate = app(OrderCalculator::class)->minimumDateForItems($this->input('items', []));

                    if ($date->isSunday()) {
                        $fail('Elegí un día de lunes a sábado.');
                    }

                    if ($date->lessThan($minimumDate)) {
                        $fail('La primera fecha disponible para estos productos es '.$minimumDate->format('d/m/Y').'.');
                    }
                },
            ],
            'time_slot' => ['required', Rule::in(['9 a 12 h', '12 a 15 h', '15 a 18 h', '18 a 20 h'])],
            'payment_method' => ['required', Rule::in(['transferencia', 'mercado_pago', 'efectivo'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
