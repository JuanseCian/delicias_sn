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

        if (is_array($items)) {
            $items = array_map(static function (mixed $item): mixed {
                if (! is_array($item)) {
                    return $item;
                }

                if (! isset($item['product_size_id']) && isset($item['size_id'])) {
                    $item['product_size_id'] = $item['size_id'];
                }

                unset($item['size_id']);

                return $item;
            }, $items);
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
            'whatsapp_consent' => ['accepted'],
            'delivery_type' => ['required', Rule::in(['envio', 'retiro'])],
            'address' => ['nullable', 'string', 'max:180', Rule::requiredIf(fn (): bool => $this->input('delivery_type') === 'envio')],
            'delivery_date' => [
                'required',
                'date_format:Y-m-d',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $date = CarbonImmutable::createFromFormat('Y-m-d', $value);
                    $minimumDate = app(OrderCalculator::class)->minimumDateForItems($this->input('items', []));

                    if ($date->lessThan($minimumDate)) {
                        $fail('La primera fecha disponible para estos productos es '.$minimumDate->format('d/m/Y').'.');
                    }
                },
            ],
            'time_slot' => ['required', Rule::in($this->availableTimeSlots($this->input('delivery_date')))],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'items.required' => 'Agregá al menos un producto al pedido.',
            'items.min' => 'Agregá al menos un producto al pedido.',
            'customer_name.required' => 'Completá tu nombre y apellido.',
            'customer_name.max' => 'El nombre no puede superar los 120 caracteres.',
            'phone.required' => 'Completá tu teléfono o WhatsApp.',
            'phone.max' => 'El teléfono no puede superar los 40 caracteres.',
            'delivery_type.required' => 'Elegí cómo vas a recibir el pedido.',
            'address.required' => 'Completá la dirección de entrega.',
            'address.max' => 'La dirección no puede superar los 180 caracteres.',
            'delivery_date.required' => 'Elegí una fecha de entrega.',
            'delivery_date.date_format' => 'La fecha de entrega no es válida.',
            'time_slot.required' => 'Elegí una franja horaria.',
            'notes.max' => 'Las notas no pueden superar los 500 caracteres.',
        ];
    }

    /** @return list<string> */
    private function availableTimeSlots(mixed $date): array
    {
        ['weekday' => $weekdaySlots, 'weekend' => $weekendSlots] = delicias_time_slots();

        if (! is_string($date) || $date === '') {
            return array_values(array_unique([...$weekdaySlots, ...$weekendSlots]));
        }

        try {
            $deliveryDate = CarbonImmutable::createFromFormat('!Y-m-d', $date);
        } catch (\Throwable) {
            return array_values(array_unique([...$weekdaySlots, ...$weekendSlots]));
        }

        return $deliveryDate->isWeekend() ? $weekendSlots : $weekdaySlots;
    }
}
